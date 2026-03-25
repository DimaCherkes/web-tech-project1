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

    public function updateCountryCode(int $id, string $code): void {
        $sql = "UPDATE countries SET code = :code WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':code' => $code, ':id' => $id]);
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