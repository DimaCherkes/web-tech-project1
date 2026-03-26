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

    public function findAll(): array {
        $sql = "SELECT og.*, c.name as country_name 
                FROM olympic_games og
                LEFT JOIN countries c ON og.country_id = c.id
                ORDER BY og.year DESC, og.type ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllPageable(int $page = 1, int $pageSize = 10): array {
        $offset = ($page - 1) * $pageSize;
        $sql = "SELECT og.*, c.name as country_name 
                FROM olympic_games og
                LEFT JOIN countries c ON og.country_id = c.id
                ORDER BY og.year DESC, og.type ASC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count(): int {
        $sql = "SELECT COUNT(*) FROM olympic_games";
        $stmt = $this->db->query($sql);
        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array {
        $sql = "SELECT og.*, c.name as country_name 
                FROM olympic_games og
                LEFT JOIN countries c ON og.country_id = c.id
                WHERE og.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function update(int $id, int $year, string $type, string $city, int $countryId): bool {
        $allowedTypes = ['LOH', 'ZOH'];
        if (!in_array($type, $allowedTypes)) {
            throw new \InvalidArgumentException("Invalid game type. Allowed: " . implode(', ', $allowedTypes));
        }

        $sql = "UPDATE olympic_games SET year = :year, type = :type, city = :city, country_id = :country_id WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':year' => $year,
            ':type' => $type,
            ':city' => $city,
            ':country_id' => $countryId
        ]);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM athlete_medals WHERE olympic_games_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $sql = "DELETE FROM olympic_games WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
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

    public function insertOlympicGamesWithId(int $year, string $type, string $city, int $countryId): int {
        $allowedTypes = ['LOH', 'ZOH'];
        if (!in_array($type, $allowedTypes)) {
            throw new \InvalidArgumentException("Invalid game type. Allowed: " . implode(', ', $allowedTypes));
        }

        $existingId = $this->findGameId($year, $type, $city);
        if ($existingId) {
            return $existingId;
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
