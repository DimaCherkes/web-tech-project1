<?php

function findCountryId(PDO $pdo, string $name): ?int {
    $sql = "SELECT id FROM countries WHERE name = :name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':name' => $name]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int) $row['id'] : null;
}

function updateCountryCode(PDO $pdo, int $id, string $code): void {
    $sql = "UPDATE countries SET code = :code WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':code' => $code, ':id' => $id]);
}

function insertCountry(PDO $pdo, string $name, ?string $code = null): int {
    $existingId = findCountryId($pdo, $name);
    
    if ($existingId) {
        // If we have a code now, but the existing record doesn't, update it
        if ($code !== null) {
            $stmt = $pdo->prepare("SELECT code FROM countries WHERE id = :id");
            $stmt->execute([':id' => $existingId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (empty($row['code'])) {
                updateCountryCode($pdo, $existingId, $code);
            }
        }
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
