<?php

namespace App\repository;

use App\Core\Database;
use PDO;

class AthleteRepository {
    private PDO $db;
    private CountryRepository $countryRepository;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->countryRepository = new CountryRepository();
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

    /**
     * @param array $filters (e.g., ['firstName' => 'John', 'category' => 'Swimming', 'year' => 2024])
     * @param string $sortBy (e.g., 'last_name')
     * @param string $sortDir (ASC/DESC)
     * @param int $page (1, 2, 3...)
     * @param int $pageSize (10, 20...)
     * @return array
     */
    public function findAllList(array $filters = [], string $sortBy = 'id', string $sortDir = 'ASC', int $page = 1, int $pageSize = 10): array {
        $offset = ($page - 1) * $pageSize;

        // Map DTO field names to SQL column names for sorting
        $sortMap = [
            'id' => 'a.id',
            'firstName' => 'a.first_name',
            'lastName' => 'a.last_name',
            'year' => 'og.year',
            'country' => 'c.name',
            'sportName' => 'd.name'
        ];

        // Validation: if sortBy is not in our map, default to 'id'
        if (!array_key_exists($sortBy, $sortMap)) {
            $sortBy = 'id';
        }

        $orderSql = $sortMap[$sortBy];
        $sortDir = (strtoupper($sortDir) === 'DESC') ? 'DESC' : 'ASC';

        // Base Query
        $sql = "SELECT 
                    a.id, 
                    a.first_name, 
                    a.last_name, 
                    og.year, 
                    c.name as country, 
                    d.name as sportName
                FROM athletes a
                JOIN athlete_medals am ON a.id = am.athlete_id
                JOIN olympic_games og ON am.olympic_games_id = og.id
                JOIN disciplines d ON am.discipline_id = d.id
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
        if (!empty($filters['category'])) {
            $where[] = "d.category = :category";
            $params[':category'] = $filters['category'];
        }
        if (!empty($filters['year'])) {
            $where[] = "og.year = :year";
            $params[':year'] = (int)$filters['year'];
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        // Sorting & Pagination
        $sql .= " ORDER BY $orderSql $sortDir LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        // Bind filters
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAllList(array $filters = []): int {
        $sql = "SELECT COUNT(*) 
                FROM athletes a
                JOIN athlete_medals am ON a.id = am.athlete_id
                JOIN olympic_games og ON am.olympic_games_id = og.id
                JOIN disciplines d ON am.discipline_id = d.id";

        $where = [];
        $params = [];

        if (!empty($filters['firstName'])) {
            $where[] = "a.first_name LIKE :first_name";
            $params[':first_name'] = '%' . $filters['firstName'] . '%';
        }
        if (!empty($filters['lastName'])) {
            $where[] = "a.last_name LIKE :last_name";
            $params[':last_name'] = '%' . $filters['lastName'] . '%';
        }
        if (!empty($filters['category'])) {
            $where[] = "d.category = :category";
            $params[':category'] = $filters['category'];
        }
        if (!empty($filters['year'])) {
            $where[] = "og.year = :year";
            $params[':year'] = (int)$filters['year'];
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
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

    public function findById(int $id): ?array {
        $sql = "SELECT a.*, c.name as birth_country_name, dc.name as death_country_name
                FROM athletes a
                LEFT JOIN countries c ON a.birth_country_id = c.id
                LEFT JOIN countries dc ON a.death_country_id = dc.id
                WHERE a.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findParticipationsByAthleteId(int $athleteId): array {
        $sql = "SELECT 
                    og.year, 
                    og.type, 
                    og.city, 
                    d.name as disciplineName, 
                    d.category, 
                    mt.name as medalName, 
                    mt.placing
                FROM athlete_medals am
                JOIN olympic_games og ON am.olympic_games_id = og.id
                JOIN disciplines d ON am.discipline_id = d.id
                JOIN medal_types mt ON am.medal_type_id = mt.id
                WHERE am.athlete_id = :athlete_id
                ORDER BY og.year DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':athlete_id' => $athleteId]);
        return $stmt->fetchAll();
    }

    public function insertAthleteWithIds(
        string $firstName,
        string $lastName,
        ?string $birthDate = null,
        ?string $birthPlace = null,
        ?int $birthCountryId = null,
        ?string $deathDate = null,
        ?string $deathPlace = null,
        ?int $deathCountryId = null
    ): int {
        $sql = "INSERT INTO athletes
            (first_name, last_name, birth_date, birth_place, birth_country_id,
             death_date, death_place, death_country_id)
            VALUES
            (:first_name, :last_name, :birth_date, :birth_place, :birth_country_id,
             :death_date, :death_place, :death_country_id)";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':birth_date' => $birthDate,
            ':birth_place' => $birthPlace,
            ':birth_country_id' => $birthCountryId,
            ':death_date' => $deathDate,
            ':death_place' => $deathPlace,
            ':death_country_id' => $deathCountryId
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function insertAthlete(
        string $firstName,
        string $lastName,
        ?string $birthDate = null,
        ?string $birthPlace = null,
        ?string $birthCountryName = null,
        ?string $deathDate = null,
        ?string $deathPlace = null,
        ?string $deathCountryName = null
    ): int {
        $existingId = $this->findAthleteId($firstName, $lastName);
        if ($existingId) {
            return $existingId;
        }

        $birthCountryId = null;
        if ($birthCountryName) {
            $birthCountryId = $this->countryRepository->findCountryId($birthCountryName);
            if (!$birthCountryId) {
                $birthCountryId = $this->countryRepository->insertCountry($birthCountryName);
            }
        }

        $deathCountryId = null;
        if ($deathCountryName) {
            $deathCountryId = $this->countryRepository->findCountryId($deathCountryName);
            if (!$deathCountryId) {
                $deathCountryId = $this->countryRepository->insertCountry($deathCountryName);
            }
        }

        $sql = "INSERT INTO athletes
            (first_name, last_name, birth_date, birth_place, birth_country_id,
             death_date, death_place, death_country_id)
            VALUES
            (:first_name, :last_name, :birth_date, :birth_place, :birth_country_id,
             :death_date, :death_place, :death_country_id)";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':birth_date' => $birthDate,
            ':birth_place' => $birthPlace,
            ':birth_country_id' => $birthCountryId,
            ':death_date' => $deathDate,
            ':death_place' => $deathPlace,
            ':death_country_id' => $deathCountryId
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findAthleteId(string $firstName, string $lastName): ?int {
        $sql = "SELECT id FROM athletes WHERE first_name = :first_name AND last_name = :last_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':first_name' => $firstName, ':last_name' => $lastName]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['id'] : null;
    }

    public function updateAthlete(
        int $id,
        string $firstName,
        string $lastName,
        ?string $birthDate = null,
        ?string $birthPlace = null,
        ?int $birthCountryId = null,
        ?string $deathDate = null,
        ?string $deathPlace = null,
        ?int $deathCountryId = null
    ): bool {
        $sql = "UPDATE athletes SET 
                first_name = :first_name, 
                last_name = :last_name, 
                birth_date = :birth_date, 
                birth_place = :birth_place, 
                birth_country_id = :birth_country_id,
                death_date = :death_date, 
                death_place = :death_place, 
                death_country_id = :death_country_id
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':birth_date' => $birthDate,
            ':birth_place' => $birthPlace,
            ':birth_country_id' => $birthCountryId,
            ':death_date' => $deathDate,
            ':death_place' => $deathPlace,
            ':death_country_id' => $deathCountryId
        ]);
    }

    public function deleteAthlete(int $id): bool {
        $sql = "DELETE FROM athlete_medals WHERE athlete_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $sql = "DELETE FROM athletes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

}
