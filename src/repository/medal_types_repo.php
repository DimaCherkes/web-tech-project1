<?php
/*
 * Medal Types Repository
 */

function findMedalTypeId(PDO $pdo, string $type): ?int {
    $sql = "SELECT id FROM medal_types WHERE type = :type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':type' => $type]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int) $row['id'] : null;
}

function insertMedalType(PDO $pdo, string $type): int {
    $existingId = findMedalTypeId($pdo, $type);
    if ($existingId) {
        return $existingId;
    }

    $sql = "INSERT INTO medal_types (type) VALUES (:type)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':type' => $type]);

    return (int) $pdo->lastInsertId();
}
