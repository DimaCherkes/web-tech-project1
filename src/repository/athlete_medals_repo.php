<?php
/*
 * Athlete Medals Repository
 */

function findAthleteMedalId(PDO $pdo, int $athleteId, int $gameId, int $disciplineId, int $medalId): ?int {
    $sql = "SELECT id FROM athlete_medals 
            WHERE athlete_id = :athlete_id 
              AND game_id = :game_id 
              AND discipline_id = :discipline_id 
              AND medal_id = :medal_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':athlete_id' => $athleteId,
        ':game_id' => $gameId,
        ':discipline_id' => $disciplineId,
        ':medal_id' => $medalId
    ]);
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int) $row['id'] : null;
}

function insertAthleteMedal(PDO $pdo, int $athleteId, int $gameId, int $disciplineId, int $medalId): int {
    $existingId = findAthleteMedalId($pdo, $athleteId, $gameId, $disciplineId, $medalId);
    if ($existingId) {
        return $existingId;
    }

    $sql = "INSERT INTO athlete_medals (athlete_id, game_id, discipline_id, medal_id) 
            VALUES (:athlete_id, :game_id, :discipline_id, :medal_id)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':athlete_id' => $athleteId,
        ':game_id' => $gameId,
        ':discipline_id' => $disciplineId,
        ':medal_id' => $medalId
    ]);

    return (int) $pdo->lastInsertId();
}
