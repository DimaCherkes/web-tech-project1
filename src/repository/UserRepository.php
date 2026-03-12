<?php

namespace App\Repository;

use App\Core\Database;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM user_accounts WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO user_accounts (first_name, last_name, email, password_hash, tfa_secret) 
                VALUES (:first_name, :last_name, :email, :password_hash, :tfa_secret)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':first_name' => $data['firstName'],
            ':last_name' => $data['lastName'],
            ':email' => $data['email'],
            ':password_hash' => password_hash($data['password'], PASSWORD_ARGON2ID),
            ':tfa_secret' => $data['tfaSecret'] ?? null
        ]);
    }
    }

