<?php

namespace App\Service;

use App\dto\CountryDTO;
use App\Repository\CountryRepository;

class CountryService {
    private CountryRepository $countryRepository;

    public function __construct() {
        $this->countryRepository = new CountryRepository();
    }

    public function getAll(array $queryParams): array {
        $page = (int)($queryParams['page'] ?? 1);
        $pageSize = (int)($queryParams['pageSize'] ?? 10);

        $data = $this->countryRepository->findAll($page, $pageSize);
        $totalItems = $this->countryRepository->count();

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
        return $this->countryRepository->insertCountry($data['name'], $data['code']);
    }

    public function getById(int $id): ?CountryDTO {
        $countryData = $this->countryRepository->findById($id);

        if (!$countryData) {
            return null;
        }

        return new CountryDTO($countryData);
    }

    public function update(int $id, array $data): bool
    {
        $existing = $this->countryRepository->findById($id);
        if (!$existing) {
            return false;
        }

        return $this->countryRepository->updateCountry(
            $id,
            !empty($data['name']) ? $data['name'] : $existing['name'],
            !empty($data['code']) ? $data['code'] : $existing['code'],
        );
    }

    public function delete(int $id): bool
    {
        return $this->countryRepository->delete($id);
    }

}
