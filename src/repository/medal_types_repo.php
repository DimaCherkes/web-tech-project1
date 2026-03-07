<?php
/*
 * Medal Types Repository
 */

function findMedalTypeIdByPlacing(PDO $pdo, int $placing): ?int {
    $stmt = $pdo->prepare("SELECT id FROM medal_types WHERE placing = :placing");
    $stmt->execute([':placing' => $placing]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int)$row['id'] : null;
}

function insertMedalType(PDO $pdo, int $placing): int {
    $existingId = findMedalTypeIdByPlacing($pdo, $placing);
    if ($existingId) {
        return $existingId;
    }

    $medalNames = [
        1 => ['name' => 'Gold', 'desc' => 'Zlato'],
        2 => ['name' => 'Silver', 'desc' => 'Striebro'],
        3 => ['name' => 'Bronze', 'desc' => 'Bronz']
    ];

    $name = $medalNames[$placing]['name'] ?? 'Other';
    $desc = $medalNames[$placing]['desc'] ?? 'Iné umiestnenie';

    $stmt = $pdo->prepare("INSERT INTO medal_types (placing, name, description) VALUES (:placing, :name, :desc)");
    $stmt->execute([
        ':placing' => $placing,
        ':name' => $name,
        ':desc' => $desc
    ]);

    return (int)$pdo->lastInsertId();
}

function ensureMedalTypeExists(PDO $pdo, int $placing): int {
    $id = findMedalTypeIdByPlacing($pdo, $placing);
    if (!$id) {
        $id = insertMedalType($pdo, $placing);
    }
    return $id;
}
