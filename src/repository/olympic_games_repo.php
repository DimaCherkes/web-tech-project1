<?php

require_once 'countries_repo.php';

function findGameId(PDO $pdo, int $year, string $type, string $city): ?int {
    $sql = "SELECT id FROM olympic_games WHERE year = :year AND type = :type AND city = :city";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':year' => $year, ':type' => $type, ':city' => $city]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int) $row['id'] : null;
}

function insertOlympicGames(PDO $pdo, int $year, string $type, string $city, string $countryName): int {
    // Kontrola, ci argument type splna podmienky ENUM typu (LOH,ZOH)
    $allowedTypes = ['LOH', 'ZOH'];
    if (!in_array($type, $allowedTypes)) {
        throw new InvalidArgumentException("Invalid game type. Allowed: " . implode(', ', $allowedTypes));
    }

    $existingId = findGameId($pdo, $year, $type, $city);
    if ($existingId) {
        return $existingId;
    }

    $countryId = findCountryId($pdo, $countryName);
    if (!$countryId) {
        $countryId = insertCountry($pdo, $countryName);
    }

    $sql = "INSERT INTO olympic_games (year, type, city, country_id) VALUES (:year, :type, :city, :country_id)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':year' => $year,
        ':type' => $type,
        ':city' => $city,
        ':country_id' => $countryId
    ]);

    return (int) $pdo->lastInsertId();
}
