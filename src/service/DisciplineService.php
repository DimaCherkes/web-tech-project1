<?php

namespace App\service;

use App\dto\DisciplineDTO;
use App\Repository\DisciplineRepository;

class DisciplineService
{
    private DisciplineRepository $disciplineRepository;

    public function __construct()
    {
        $this->disciplineRepository = new DisciplineRepository();
    }

    public function getAllCategories(): array
    {
        return $this->disciplineRepository->findAllCategories();
    }

    public function getAll(array $queryParams): array {
        $page = (int)($queryParams['page'] ?? 1);
        $pageSize = (int)($queryParams['pageSize'] ?? 10);

        $data = $this->disciplineRepository->findAllPageable($page, $pageSize);
        $totalItems = $this->disciplineRepository->count();

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
        return $this->disciplineRepository->insertDiscipline($data['name'], $data['category']);
    }

    public function getById(int $id): ?DisciplineDTO {
        $countryData = $this->disciplineRepository->findById($id);

        if (!$countryData) {
            return null;
        }

        return new DisciplineDTO($countryData);
    }

    public function update(int $id, array $data): bool
    {
        $existing = $this->disciplineRepository->findById($id);
        if (!$existing) {
            return false;
        }

        return $this->disciplineRepository->updateDiscipline(
            $id,
            !empty($data['name']) ? $data['name'] : $existing['name'],
            !empty($data['category']) ? $data['category'] : $existing['category'],
        );
    }

    public function delete(int $id): bool
    {
        return $this->disciplineRepository->delete($id);
    }
}
