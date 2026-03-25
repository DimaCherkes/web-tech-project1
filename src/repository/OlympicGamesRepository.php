<?php

namespace App\Repository;

use App\Core\Database;
use PDO;

class OlympicGamesRepository
{
    private PDO $db;
    private CountryRepository $countryRepository;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->countryRepository = new CountryRepository();
    }

    public function findAllByAllYears(): array
    {
        $sql = "SELECT DISTINCT year FROM olympic_games ORDER BY year DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function findGameId(int $year, string $type, string $city): ?int {
        $sql = "SELECT id FROM olympic_games WHERE year = :year AND type = :type AND city = :city";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':year' => $year, ':type' => $type, ':city' => $city]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['id'] : null;
    }

    public function insertOlympicGames(int $year, string $type, string $city, string $countryName): int {
        // Kontrola, ci argument type splna podmienky ENUM typu (LOH,ZOH)
        $allowedTypes = ['LOH', 'ZOH'];
        if (!in_array($type, $allowedTypes)) {
            throw new InvalidArgumentException("Invalid game type. Allowed: " . implode(', ', $allowedTypes));
        }

        $existingId = $this->findGameId($year, $type, $city);
        if ($existingId) {
            return $existingId;
        }

        $countryId = $this->countryRepository->findCountryId($countryName);
        if (!$countryId) {
            $countryId = $this->countryRepository->insertCountry($countryName);
        }

        $sql = "INSERT INTO olympic_games (year, type, city, country_id) VALUES (:year, :type, :city, :country_id)";
        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':year' => $year,
            ':type' => $type,
            ':city' => $city,
            ':country_id' => $countryId
        ]);

        return (int) $this->db->lastInsertId();
    }
}
