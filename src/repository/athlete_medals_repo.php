<?php
/*
 * Athlete Medals Repository
 */

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

    // Map placing (1, 2, 3) to medal_type_id from medal_types table
    $stmtMedal = $pdo->prepare("SELECT id FROM medal_types WHERE placing = :placing");
    $stmtMedal->execute([':placing' => (int)$placing]);
    $medalRow = $stmtMedal->fetch(PDO::FETCH_ASSOC);
    $medalTypeId = $medalRow ? (int)$medalRow['id'] : null;

    if (!$medalTypeId) {
        return 0; // Not a medal-winning placement
    }

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
