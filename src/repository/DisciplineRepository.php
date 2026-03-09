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
}
