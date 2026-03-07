<?php
/*
 * Athlete Medals Repository
 */

require_once 'medal_types_repo.php';

function findAthleteMedal(PDO $pdo, int $athleteId, int $gameId, int $disciplineId): ?int {
    $sql = "SELECT id FROM athlete_medals 
            WHERE athlete_id = :athlete_id 
              AND olympic_games_id = :game_id 
              AND discipline_id = :discipline_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':athlete_id' => $athleteId,
        ':game_id' => $gameId,
        ':discipline_id' => $disciplineId
    ]);
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int) $row['id'] : null;
}

function insertAthleteMedal(PDO $pdo, int $athleteId, int $gameId, int $disciplineId, string $placing): int {
    $existingId = findAthleteMedal($pdo, $athleteId, $gameId, $disciplineId);
    if ($existingId) {
        return $existingId;
    }

    $placingInt = (int)$placing;
    
    // Only process top 3 spots as medals
    if ($placingInt < 1 || $placingInt > 3) {
        return 0; 
    }

    // Get medal type ID from separate repository
    $medalTypeId = ensureMedalTypeExists($pdo, $placingInt);

    $sql = "INSERT INTO athlete_medals (athlete_id, olympic_games_id, discipline_id, medal_type_id) 
            VALUES (:athlete_id, :game_id, :discipline_id, :medal_type_id)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':athlete_id' => $athleteId,
        ':game_id' => $gameId,
        ':discipline_id' => $disciplineId,
        ':medal_type_id' => $medalTypeId
    ]);

    return (int) $pdo->lastInsertId();
}
