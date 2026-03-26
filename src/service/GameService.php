<?php

namespace App\Service;

use App\dto\OlympicGameDTO;
use App\repository\CountryRepository;
use App\Repository\OlympicGamesRepository;

class GameService
{
    private OlympicGamesRepository $gameRepository;
    private CountryRepository $countryRepository;

    public function __construct()
    {
        $this->gameRepository = new OlympicGamesRepository();
        $this->countryRepository = new CountryRepository();
    }

    public function getAllYears(): array
    {
        return $this->gameRepository->findAllByAllYears();
    }

    public function getAll(array $queryParams): array {
        $page = (int)($queryParams['page'] ?? 1);
        $pageSize = (int)($queryParams['pageSize'] ?? 10);

        $data = $this->gameRepository->findAllPageable($page, $pageSize);
        $totalItems = $this->gameRepository->count();

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
        return $this->gameRepository->insertOlympicGames($data['year'], $data['type'], $data['city'], $data['countryName']);
    }

    public function getById(int $id): ?OlympicGameDTO {
        $countryData = $this->gameRepository->findById($id);

        if (!$countryData) {
            return null;
        }

        return new OlympicGameDTO($countryData);
    }

    public function update(int $id, array $data): bool
    {
        $existing = $this->gameRepository->findById($id);
        if (!$existing) {
            return false;
        }

        $countryId = null;
        if (!empty($data['countryName'])) {
            $countryId = $this->countryRepository->findCountryId($data['countryName']);
        }

        return $this->gameRepository->update(
            $id,
            !empty($data['year']) ? $data['year'] : $existing['year'],
            !empty($data['type']) ? $data['type'] : $existing['type'],
            !empty($data['city']) ? $data['city'] : $existing['city'],
            $countryId,
        );
    }

    public function delete(int $id): bool
    {
        return $this->gameRepository->delete($id);
    }
}
