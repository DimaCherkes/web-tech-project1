<?php

function findCountryId(PDO $pdo, string $name): ?int {
    $sql = "SELECT id FROM countries WHERE name = :name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':name' => $name]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int) $row['id'] : null;
}

function insertCountry(PDO $pdo, string $name, ?string $code = null): int {
    $existingId = findCountryId($pdo, $name);
    if ($existingId) {
        return $existingId;
    }

    $sql = "INSERT INTO countries (name, code) VALUES (:name, :code)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':name' => $name,
        ':code' => $code
    ]);

    return (int) $pdo->lastInsertId();
}
