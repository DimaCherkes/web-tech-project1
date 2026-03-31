<?php

namespace App\service;

use App\dto\AthleteMedalDTO;
use App\repository\AthleteMedalsRepository;
use App\repository\AthleteRepository;

class AthleteMedalService {
    private AthleteMedalsRepository $repository;
    private AthleteRepository $athleteRepository;

    public function __construct() {
        $this->repository = new AthleteMedalsRepository();
        $this->athleteRepository = new AthleteRepository();
    }

    public function getAll(array $queryParams): array {
        $page = (int)($queryParams['page'] ?? 1);
        $pageSize = (int)($queryParams['pageSize'] ?? 10);

        $filters = [
            'type' => $queryParams['type'] ?? null,
            'year' => $queryParams['year'] ?? null,
            'medal_type_id' => $queryParams['medal_type_id'] ?? null,
            'discipline_id' => $queryParams['discipline_id'] ?? null,
        ];

        $data = $this->repository->findAll($filters, $page, $pageSize);
        $totalItems = $this->repository->count($filters);

        return [
            'items' => $data,
            'pagination' => [
                'totalItems' => $totalItems,
                'page' => $page,
                'pageSize' => $pageSize,
                'totalPages' => ceil($totalItems / $pageSize)
            ]
        ];
    }

    public function create(array $data): int
    {
        return $this->repository->insertAthleteMedalByIds(
            $data['athleteId'], $data['gameId'], $data['disciplineId'], $data['medalTypeId']);
    }

    public function getById(int $id): ?AthleteMedalDTO {
        $athleteData = $this->repository->findById($id);

        if (!$athleteData) {
            return null;
        }

        return new AthleteMedalDTO($athleteData);
    }

    public function update(int $id, array $data): bool
    {
         return $this->repository->update(
            $id, $data['athleteId'], $data['gameId'], $data['disciplineId'], $data['medalTypeId']);
    }

    public function delete(int $id): bool
    {
        return $this->athleteRepository->deleteAthlete($id);
    }

}
