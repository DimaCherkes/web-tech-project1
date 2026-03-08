<?php

namespace App\Service;

use App\Repository\AthleteRepository;
use App\Dto\AthleteDTO;

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
}
