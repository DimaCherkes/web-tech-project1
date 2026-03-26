<?php

namespace App\Repository;

use App\Core\Database;
use PDO;

class DisciplineRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAllCategories(): array
    {
        $sql = "SELECT DISTINCT category FROM disciplines WHERE category IS NOT NULL ORDER BY category ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function findAll(): array {
        $sql = "SELECT * FROM disciplines ORDER BY name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array {
        $sql = "SELECT * FROM disciplines WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function updateDiscipline(int $id, string $name, ?string $category = null): bool {
        $sql = "UPDATE disciplines SET name = :name, category = :category WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':category' => $category
        ]);
    }

    public function deleteDiscipline(int $id): bool {
        $sql = "DELETE FROM athlete_medals WHERE discipline_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $sql = "DELETE FROM disciplines WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function findDisciplineId(string $name, ?string $category = null): ?int {
        $sql = "SELECT id FROM disciplines WHERE name = :name AND ";
        if ($category === null) {
            $sql .= "category IS NULL";
        } else {
            $sql .= "category = :category";
        }

        $stmt = $this->db->prepare($sql);
        $params = [':name' => $name];
        if ($category !== null) {
            $params[':category'] = $category;
        }

        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['id'] : null;
    }

    public function insertDiscipline(string $name, ?string $category = null) : int {
        $existingId = $this->findDisciplineId($name, $category);
        if ($existingId) {
            return $existingId;
        }

        $sql = "INSERT INTO disciplines (name, category) VALUES (:name, :category)";
        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':name' => $name,
            ':category' => $category
        ]);

        return (int) $this->db->lastInsertId();
    }
}
