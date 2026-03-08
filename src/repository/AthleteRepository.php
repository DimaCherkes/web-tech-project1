<?php

namespace App\Repository;

use App\Core\Database;
use PDO;

class AthleteRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * @param array $filters (e.g., ['firstName' => 'John', 'country' => 'USA'])
     * @param string $sortBy (e.g., 'last_name')
     * @param string $sortDir (ASC/DESC)
     * @param int $page (1, 2, 3...)
     * @param int $pageSize (10, 20...)
     * @return array
     */
    public function findAll(array $filters = [], string $sortBy = 'id', string $sortDir = 'ASC', int $page = 1, int $pageSize = 10): array {
        $offset = ($page - 1) * $pageSize;
        
        // Allowed sort columns for safety (avoid SQL Injection)
        $allowedSort = ['id', 'firstName', 'lastName', 'birthDate'];
        if (!in_array($sortBy, $allowedSort)) {
            $sortBy = 'id';
        }
        $sortDir = (strtoupper($sortDir) === 'DESC') ? 'DESC' : 'ASC';

        // Base Query
        $sql = "SELECT a.*, c.name as birth_country_name 
                FROM athletes a
                LEFT JOIN countries c ON a.birth_country_id = c.id";
        
        $where = [];
        $params = [];

        // Filtering Logic
        if (!empty($filters['firstName'])) {
            $where[] = "a.first_name LIKE :first_name";
            $params[':first_name'] = '%' . $filters['firstName'] . '%';
        }
        if (!empty($filters['lastName'])) {
            $where[] = "a.last_name LIKE :last_name";
            $params[':last_name'] = '%' . $filters['lastName'] . '%';
        }
        if (!empty($filters['countryId'])) {
            $where[] = "a.birth_country_id = :country_id";
            $params[':country_id'] = $filters['countryId'];
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        // Sorting & Pagination
        $sql .= " ORDER BY $sortBy $sortDir LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);

        // Re-bind params
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        // PDO::PARAM_INT используется для проверки на sql инъекцию?
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(array $filters = []): int {
        $sql = "SELECT COUNT(*) FROM athletes a";
        $where = [];
        $params = [];

        if (!empty($filters['firstName'])) {
            $where[] = "a.first_name LIKE :first_name";
            $params[':first_name'] = '%' . $filters['firstName'] . '%';
        }
        // ... (rest of filtering logic)

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }
}
