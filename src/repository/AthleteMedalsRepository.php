<?php

namespace App\repository;

use App\Core\Database;
use PDO;

class AthleteMedalsRepository
{
    private PDO $db;
    private MedalTypesRepository $medalTypesRepository;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->medalTypesRepository = new MedalTypesRepository();
    }

    public function findAthleteMedal(int $athleteId, int $gameId, int $disciplineId): ?int {
        $sql = "SELECT id FROM athlete_medals 
            WHERE athlete_id = :athlete_id 
              AND olympic_games_id = :game_id 
              AND discipline_id = :discipline_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':athlete_id' => $athleteId,
            ':game_id' => $gameId,
            ':discipline_id' => $disciplineId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['id'] : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $pageSize = 10): array {
        $offset = ($page - 1) * $pageSize;
        
        $sql = "SELECT am.*, a.first_name, a.last_name, og.year, og.type, d.name as discipline_name, mt.name as medal_name, mt.placing
                FROM athlete_medals am
                JOIN athletes a ON am.athlete_id = a.id
                JOIN olympic_games og ON am.olympic_games_id = og.id
                JOIN disciplines d ON am.discipline_id = d.id
                JOIN medal_types mt ON am.medal_type_id = mt.id";

        $where = [];
        $params = [];

        if (!empty($filters['type'])) {
            $where[] = "og.type = :type";
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['year'])) {
            $where[] = "og.year = :year";
            $params[':year'] = (int)$filters['year'];
        }
        if (!empty($filters['medal_type_id'])) {
            $where[] = "am.medal_type_id = :medal_type_id";
            $params[':medal_type_id'] = (int)$filters['medal_type_id'];
        }
        if (!empty($filters['discipline_id'])) {
            $where[] = "am.discipline_id = :discipline_id";
            $params[':discipline_id'] = (int)$filters['discipline_id'];
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY og.year DESC, a.last_name ASC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count(array $filters = []): int {
        $sql = "SELECT COUNT(*) 
                FROM athlete_medals am
                JOIN olympic_games og ON am.olympic_games_id = og.id";
        
        $where = [];
        $params = [];

        if (!empty($filters['type'])) {
            $where[] = "og.type = :type";
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['year'])) {
            $where[] = "og.year = :year";
            $params[':year'] = (int)$filters['year'];
        }
        if (!empty($filters['medal_type_id'])) {
            $where[] = "am.medal_type_id = :medal_type_id";
            $params[':medal_type_id'] = (int)$filters['medal_type_id'];
        }
        if (!empty($filters['discipline_id'])) {
            $where[] = "am.discipline_id = :discipline_id";
            $params[':discipline_id'] = (int)$filters['discipline_id'];
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array {
        $sql = "SELECT * FROM athlete_medals WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function update(int $id, int $athleteId, int $gameId, int $disciplineId, int $medalTypeId): bool {
        $sql = "UPDATE athlete_medals SET 
                athlete_id = :athlete_id, 
                olympic_games_id = :game_id, 
                discipline_id = :discipline_id, 
                medal_type_id = :medal_type_id 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':athlete_id' => $athleteId,
            ':game_id' => $gameId,
            ':discipline_id' => $disciplineId,
            ':medal_type_id' => $medalTypeId
        ]);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM athlete_medals WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    function insertAthleteMedal(int $athleteId, int $gameId, int $disciplineId, string $placing): int {
        $existingId = $this->findAthleteMedal($athleteId, $gameId, $disciplineId);
        if ($existingId) {
            return $existingId;
        }

        $placingInt = (int)$placing;
        $medalTypeId = $this->medalTypesRepository->ensureMedalTypeExists($placingInt);

        $sql = "INSERT INTO athlete_medals (athlete_id, olympic_games_id, discipline_id, medal_type_id) 
            VALUES (:athlete_id, :game_id, :discipline_id, :medal_type_id)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':athlete_id' => $athleteId,
            ':game_id' => $gameId,
            ':discipline_id' => $disciplineId,
            ':medal_type_id' => $medalTypeId
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function insertAthleteMedalByIds(int $athleteId, int $gameId, int $disciplineId, int $medalTypeId): int {
        $existingId = $this->findAthleteMedal($athleteId, $gameId, $disciplineId);
        if ($existingId) {
            return $existingId;
        }

        $sql = "INSERT INTO athlete_medals (athlete_id, olympic_games_id, discipline_id, medal_type_id) 
            VALUES (:athlete_id, :game_id, :discipline_id, :medal_type_id)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':athlete_id' => $athleteId,
            ':game_id' => $gameId,
            ':discipline_id' => $disciplineId,
            ':medal_type_id' => $medalTypeId
        ]);

        return (int) $this->db->lastInsertId();
    }
}
