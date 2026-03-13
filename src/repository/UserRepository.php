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

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM user_accounts WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function syncGoogleUser(array $data): int
    {
        $userByEmail = $this->findByEmail($data['email']);
        
        if ($userByEmail) {
            return (int)$userByEmail['id'];
        }

        // Create new user if not exists
        $sql = "INSERT INTO user_accounts (first_name, last_name, email, password_hash) 
                VALUES (:first_name, :last_name, :email, :password_hash)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':first_name' => $data['firstName'],
            ':last_name' => $data['lastName'],
            ':email' => $data['email'],
            ':password_hash' => null
        ]);

        return (int)$this->db->lastInsertId();
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

    public function update(int $userId, string $firstName, string $lastName): bool
    {
        $stmt = $this->db->prepare("UPDATE user_accounts SET first_name = :first_name, last_name = :last_name WHERE id = :id");
        return $stmt->execute([
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':id' => $userId
        ]);
    }

    public function updatePassword(int $userId, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_ARGON2ID);
        $stmt = $this->db->prepare("UPDATE user_accounts SET password_hash = :hash WHERE id = :id");
        return $stmt->execute([
            ':hash' => $hash,
            ':id' => $userId
        ]);
    }
}
