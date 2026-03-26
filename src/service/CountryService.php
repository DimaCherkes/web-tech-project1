<?php

namespace App\Service;

use App\Repository\CountryRepository;

class CountryService {
    private CountryRepository $countryRepository;

    public function __construct() {
        $this->countryRepository = new CountryRepository();
    }

    public function getAllCountries(array $queryParams): array {
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
}
