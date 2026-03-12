<?php

namespace App\Repository;

use App\Core\Database;
use PDO;

class LoginHistoryRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function record(int $userId, string $type): bool
    {
        $allowedTypes = ['LOCAL', 'OAUTH'];
        if (!in_array($type, $allowedTypes)) {
            $type = 'LOCAL';
        }

        $sql = "INSERT INTO login_history (user_id, login_type) VALUES (:user_id, :login_type)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':login_type' => $type
        ]);
    }

    public function findByUserId(int $userId): array
    {
        $sql = "SELECT login_type, created_at FROM login_history WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }
}
