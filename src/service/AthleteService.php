<?php

namespace App\Service;

use App\Repository\AthleteRepository;
use App\Dto\AthleteDTO;
use App\Dto\AthleteListDTO;
use App\Dto\AthleteDetailDTO;

class AthleteService {
    private AthleteRepository $athleteRepository;

    public function __construct() {
        $this->athleteRepository = new AthleteRepository();
    }

    /**
     * @param array $queryParams (raw GET parameters)
     * @return array
     */
    public function getAllAthletes(array $queryParams): array {
        // Prepare filters, sorting, and pagination from query params

        $filters = [
            'firstName' => $queryParams['firstName'] ?? null,
            'lastName' => $queryParams['lastName'] ?? null,
            'countryId' => $queryParams['countryId'] ?? null,
        ];

        $sortBy = $queryParams['sortBy'] ?? 'id';
        $sortDir = $queryParams['sortDir'] ?? 'ASC';
        $page = (int)($queryParams['page'] ?? 1);
        $pageSize = (int)($queryParams['pageSize'] ?? 10);

        // Fetch Raw Data
        $data = $this->athleteRepository->findAll($filters, $sortBy, $sortDir, $page, $pageSize);
        $totalItems = $this->athleteRepository->count($filters);

        // Map to DTOs
        $dtos = array_map(fn($row) => new AthleteDTO($row), $data);

        // Return a structured response (Total items, data, current page, etc.)
        return [
            'items' => $dtos,
            'pagination' => [
                'totalItems' => $totalItems,
                'page' => $page,
                'pageSize' => $pageSize,
                'totalPages' => ceil($totalItems / $pageSize)
            ]
        ];
    }

    /**
     * @param array $queryParams (raw GET parameters)
     * @return array
     */
    public function getAthletesList(array $queryParams): array {
        $filters = [
            'firstName' => $queryParams['firstName'] ?? null,
            'lastName' => $queryParams['lastName'] ?? null,
            'category' => $queryParams['category'] ?? null,
            'year' => $queryParams['year'] ?? null,
        ];

        $sortBy = $queryParams['sortBy'] ?? 'id';
        $sortDir = $queryParams['sortDir'] ?? 'ASC';
        $page = (int)($queryParams['page'] ?? 1);
        $pageSize = (int)($queryParams['pageSize'] ?? 10);

        // Fetch Raw Data
        $data = $this->athleteRepository->findAllList($filters, $sortBy, $sortDir, $page, $pageSize);
        $totalItems = $this->athleteRepository->countAllList($filters);

        // Map to DTOs
        $dtos = array_map(fn($row) => new AthleteListDTO($row), $data);

        // Return a structured response
        return [
            'items' => $dtos,
            'pagination' => [
                'totalItems' => $totalItems,
                'page' => $page,
                'pageSize' => $pageSize,
                'totalPages' => ceil($totalItems / $pageSize)
            ]
        ];
    }

    public function getAthleteById(int $id): ?AthleteDetailDTO {
        $athleteData = $this->athleteRepository->findById($id);
        if (!$athleteData) {
            return null;
        }

        $participations = $this->athleteRepository->findParticipationsByAthleteId($id);
        
        return new AthleteDetailDTO($athleteData, $participations);
    }

    public function createAthlete(array $data): int
    {
        $firstName = $data['firstName'] ?? '';
        $lastName = $data['lastName'] ?? '';

        // Check for duplicate
        if ($this->athleteRepository->findAthleteId($firstName, $lastName)) {
            return 0;
        }

        return $this->athleteRepository->insertAthleteWithIds(
            $firstName,
            $lastName,
            ($data['birthDate'] ?? '') ?: null,
            ($data['birthPlace'] ?? '') ?: null,
            (int)($data['birthCountryId'] ?? 0) ?: null, 
            ($data['deathDate'] ?? '') ?: null,
            ($data['deathPlace'] ?? '') ?: null,
            (int)($data['deathCountryId'] ?? 0) ?: null
        );
    }

    public function updateAthlete(int $id, array $data): bool
    {
        $existing = $this->athleteRepository->findById($id);
        if (!$existing) {
            return false;
        }

        return $this->athleteRepository->updateAthlete(
            $id,
            !empty($data['firstName']) ? $data['firstName'] : $existing['first_name'],
            !empty($data['lastName']) ? $data['lastName'] : $existing['last_name'],
            !empty($data['birthDate']) ? $data['birthDate'] : $existing['birth_date'],
            !empty($data['birthPlace']) ? $data['birthPlace'] : $existing['birth_place'],
            !empty($data['birthCountryId']) ? (int)$data['birthCountryId'] : $existing['birth_country_id'],
            !empty($data['deathDate']) ? $data['deathDate'] : $existing['death_date'],
            !empty($data['deathPlace']) ? $data['deathPlace'] : $existing['death_place'],
            !empty($data['deathCountryId']) ? (int)$data['deathCountryId'] : $existing['death_country_id']
        );
    }

    public function deleteAthlete(int $id): bool
    {
        return $this->athleteRepository->deleteAthlete($id);
    }
}
