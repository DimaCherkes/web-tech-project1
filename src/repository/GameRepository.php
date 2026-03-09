<?php

namespace App\Repository;

use App\Core\Database;
use PDO;

class GameRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAllYears(): array
    {
        $sql = "SELECT DISTINCT year FROM olympic_games ORDER BY year DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
