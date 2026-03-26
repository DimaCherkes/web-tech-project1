<?php

namespace App\repository;

use App\Core\Database;
use PDO;

class CountryRepository
{
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findCountryId(string $name): ?int {
        $sql = "SELECT id FROM countries WHERE name = :name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':name' => $name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['id'] : null;
    }

    public function findAll(): array {
        $sql = "SELECT * FROM countries ORDER BY name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array {
        $sql = "SELECT * FROM countries WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function updateCountryCode(int $id, string $code): void {
        $sql = "UPDATE countries SET code = :code WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':code' => $code, ':id' => $id]);
    }

    public function updateCountry(int $id, string $name, ?string $code = null): bool {
        $sql = "UPDATE countries SET name = :name, code = :code WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':code' => $code
        ]);
    }

    public function deleteCountry(int $id): bool {
        // a. Delete from athlete_medals where olympic_games_id IN (select id from olympic_games where country_id = :id)
        $sql = "DELETE FROM athlete_medals WHERE olympic_games_id IN (SELECT id FROM olympic_games WHERE country_id = :id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        // b. Delete from olympic_games where country_id = :id
        $sql = "DELETE FROM olympic_games WHERE country_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        // c. Set NULL in athletes
        $sql = "UPDATE athletes SET birth_country_id = NULL WHERE birth_country_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $sql = "UPDATE athletes SET death_country_id = NULL WHERE death_country_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        // d. Delete country
        $sql = "DELETE FROM countries WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function insertCountry(string $name, ?string $code = null): int {
        $existingId = $this->findCountryId($name);

        if ($existingId) {
            // If we have a code now, but the existing record doesn't, update it
            if ($code !== null) {
                $stmt = $this->db->prepare("SELECT code FROM countries WHERE id = :id");
                $stmt->execute([':id' => $existingId]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (empty($row['code'])) {
                    $this->updateCountryCode($existingId, $code);
                }
            }
            return $existingId;
        }

        $sql = "INSERT INTO countries (name, code) VALUES (:name, :code)";
        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':name' => $name,
            ':code' => $code
        ]);

        return (int) $this->db->lastInsertId();
    }

}