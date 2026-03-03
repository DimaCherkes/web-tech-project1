<?php

function findDisciplineId(PDO $pdo, string $name, ?string $category = null): ?int {
    $sql = "SELECT id FROM disciplines WHERE name = :name";
    if ($category !== null) {
        $sql .= " AND category = :category";
    }
    $stmt = $pdo->prepare($sql);
    $params = [':name' => $name];
    if ($category !== null) {
        $params[':category'] = $category;
    }
    $stmt->execute($params);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int) $row['id'] : null;
}

function insertDiscipline(
    PDO $pdo, string $name, ?string $category = null
) : int {
    $existingId = findDisciplineId($pdo, $name, $category);
    if ($existingId) {
        return $existingId;
    }

    $sql = "INSERT INTO disciplines (name, category) VALUES (:name, :category)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':name' => $name,
        ':category' => $category
    ]);

    return (int) $pdo->lastInsertId();
}
