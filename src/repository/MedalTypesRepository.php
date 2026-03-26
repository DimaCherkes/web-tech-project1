<?php

namespace App\repository;

use App\Core\Database;
use PDO;

class MedalTypesRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array {
        $sql = "SELECT * FROM medal_types ORDER BY placing ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array {
        $sql = "SELECT * FROM medal_types WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findMedalTypeIdByPlacing(int $placing): ?int {
        $stmt = $this->db->prepare("SELECT id FROM medal_types WHERE placing = :placing");
        $stmt->execute([':placing' => $placing]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['id'] : null;
    }

    public function insertMedalType(int $placing): int {
        $existingId = $this->findMedalTypeIdByPlacing($placing);
        if ($existingId) {
            return $existingId;
        }

        $medalNames = [
            1 => ['name' => 'Gold', 'desc' => 'Zlato'],
            2 => ['name' => 'Silver', 'desc' => 'Striebro'],
            3 => ['name' => 'Bronze', 'desc' => 'Bronz']
        ];

        $name = $medalNames[$placing]['name'] ?? 'Other';
        $desc = $medalNames[$placing]['desc'] ?? 'Iné umiestnenie';

        $stmt = $this->db->prepare("INSERT INTO medal_types (placing, name, description) VALUES (:placing, :name, :desc)");
        $stmt->execute([
            ':placing' => $placing,
            ':name' => $name,
            ':desc' => $desc
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function ensureMedalTypeExists(int $placing): int {
        $id = $this->findMedalTypeIdByPlacing($placing);
        if (!$id) {
            $id = $this->insertMedalType($placing);
        }
        return $id;
    }

}