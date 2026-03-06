<?php

function findPlacementId(PDO $pdo, int $athleteId, int $gameId, int $disciplineId): ?int {
    $sql = "SELECT id FROM placements WHERE athlete_id = :athlete_id AND game_id = :game_id AND discipline_id = :discipline_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':athlete_id' => $athleteId, ':game_id' => $gameId, ':discipline_id' => $disciplineId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int) $row['id'] : null;
}

function insertPlacement(PDO $pdo, int $athleteId, int $gameId, int $disciplineId, string $placing): int {
    $existingId = findPlacementId($pdo, $athleteId, $gameId, $disciplineId);
    if ($existingId) {
        return $existingId;
    }

    $sql = "INSERT INTO placements (athlete_id, game_id, discipline_id, placing) VALUES (:athlete_id, :game_id, :discipline_id, :placing)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':athlete_id' => $athleteId,
        ':game_id' => $gameId,
        ':discipline_id' => $disciplineId,
        ':placing' => $placing
    ]);
    return (int) $pdo->lastInsertId();
}

//function formatDateForMySQL(?string $dateStr): ?string {
//    if (!$dateStr || trim($dateStr) === "" || strpos($dateStr, '#') !== false) {
//        return null;
//    }
//
//    // Attempt to parse d/m/Y
//    $parts = explode('/', $dateStr);
//    if (count($parts) === 3) {
//        return sprintf('%04d-%02d-%02d', $parts[2], $parts[1], $parts[0]);
//    }
//
//    return null;
//}

