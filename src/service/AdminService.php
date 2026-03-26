<?php

namespace App\Service;

use App\Repository\AthleteRepository;
use App\Repository\CountryRepository;
use App\Repository\DisciplineRepository;
use App\Repository\OlympicGamesRepository;
use App\Repository\MedalTypesRepository;
use App\Repository\AthleteMedalsRepository;

class AdminService
{
    private AthleteRepository $athleteRepository;
    private CountryRepository $countryRepository;
    private DisciplineRepository $disciplineRepository;
    private OlympicGamesRepository $olympicGamesRepository;
    private MedalTypesRepository $medalTypesRepository;
    private AthleteMedalsRepository $athleteMedalsRepository;

    public function __construct()
    {
        $this->athleteRepository = new AthleteRepository();
        $this->countryRepository = new CountryRepository();
        $this->disciplineRepository = new DisciplineRepository();
        $this->olympicGamesRepository = new OlympicGamesRepository();
        $this->medalTypesRepository = new MedalTypesRepository();
        $this->athleteMedalsRepository = new AthleteMedalsRepository();
    }

    public function getAllData(): array
    {
        return [
            'athletes' => $this->athleteRepository->findAll([], 'id', 'ASC', 1, 1000),
            'countries' => $this->countryRepository->findAll(),
            'disciplines' => $this->disciplineRepository->findAll(),
            'games' => $this->olympicGamesRepository->findAll(),
            'medalTypes' => $this->medalTypesRepository->findAll(),
            'athleteMedals' => $this->athleteMedalsRepository->findAll()
        ];
    }

    // Athlete CRUD
    public function createAthlete(array $data): int
    {
        return $this->athleteRepository->insertAthlete(
            $data['firstName'] ?? '',
            $data['lastName'] ?? '',
            ($data['birthDate'] ?? '') ?: null,
            ($data['birthPlace'] ?? '') ?: null,
            ($data['birthCountryName'] ?? '') ?: null, 
            ($data['deathDate'] ?? '') ?: null,
            ($data['deathPlace'] ?? '') ?: null,
            ($data['deathCountryName'] ?? '') ?: null
        );
    }

    public function updateAthlete(int $id, array $data): bool
    {
        return $this->athleteRepository->updateAthlete(
            $id,
            $data['firstName'] ?? '',
            $data['lastName'] ?? '',
            ($data['birthDate'] ?? '') ?: null,
            ($data['birthPlace'] ?? '') ?: null,
            (int)($data['birthCountryId'] ?? 0) ?: null,
            ($data['deathDate'] ?? '') ?: null,
            ($data['deathPlace'] ?? '') ?: null,
            (int)($data['deathCountryId'] ?? 0) ?: null
        );
    }

    public function deleteAthlete(int $id): bool
    {
        return $this->athleteRepository->deleteAthlete($id);
    }

    // Country CRUD
    public function createCountry(string $name, ?string $code): int
    {
        return $this->countryRepository->insertCountry($name, $code);
    }

    public function updateCountry(int $id, string $name, ?string $code): bool
    {
        return $this->countryRepository->updateCountry($id, $name, $code);
    }

    public function deleteCountry(int $id): bool
    {
        return $this->countryRepository->deleteCountry($id);
    }

    // Discipline CRUD
    public function createDiscipline(string $name, ?string $category): int
    {
        return $this->disciplineRepository->insertDiscipline($name, $category);
    }

    public function updateDiscipline(int $id, string $name, ?string $category): bool
    {
        return $this->disciplineRepository->updateDiscipline($id, $name, $category);
    }

    public function deleteDiscipline(int $id): bool
    {
        return $this->disciplineRepository->deleteDiscipline($id);
    }

    // Olympic Games CRUD
    public function createGame(int $year, string $type, string $city, int $countryId): int
    {
        return $this->olympicGamesRepository->insertOlympicGamesWithId($year, $type, $city, $countryId);
    }

    public function updateGame(int $id, int $year, string $type, string $city, int $countryId): bool
    {
        return $this->olympicGamesRepository->updateOlympicGames($id, $year, $type, $city, $countryId);
    }

    public function deleteGame(int $id): bool
    {
        return $this->olympicGamesRepository->deleteOlympicGames($id);
    }

    // Athlete Medals CRUD
    public function createAthleteMedal(int $athleteId, int $gameId, int $disciplineId, int $medalTypeId): int
    {
        return $this->athleteMedalsRepository->insertAthleteMedalByIds($athleteId, $gameId, $disciplineId, $medalTypeId);
    }

    public function updateAthleteMedal(int $id, int $athleteId, int $gameId, int $disciplineId, int $medalTypeId): bool
    {
        return $this->athleteMedalsRepository->updateAthleteMedal($id, $athleteId, $gameId, $disciplineId, $medalTypeId);
    }

    public function deleteAthleteMedal(int $id): bool
    {
        return $this->athleteMedalsRepository->deleteAthleteMedal($id);
    }
}
