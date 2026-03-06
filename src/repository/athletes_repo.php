<?php

require_once 'countries_repo.php';

function findAthleteId(PDO $pdo, string $firstName, string $lastName): ?int {
    $sql = "SELECT id FROM athletes WHERE first_name = :first_name AND last_name = :last_name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':first_name' => $firstName, ':last_name' => $lastName]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int) $row['id'] : null;
}

function insertAthlete(
    PDO $pdo,
    string $firstName,
    string $lastName,
    ?string $birthDate = null,
    ?string $birthPlace = null,
    ?string $birthCountryName = null,
    ?string $deathDate = null,
    ?string $deathPlace = null,
    ?string $deathCountryName = null
): int {
    $existingId = findAthleteId($pdo, $firstName, $lastName);
    if ($existingId) {
        return $existingId;
    }

    $birthCountryId = $birthCountryName ? findCountryId($pdo, $birthCountryName) : null;
    $deathCountryId = $deathCountryName ? findCountryId($pdo, $deathCountryName) : null;

    $sql = "INSERT INTO athletes
            (first_name, last_name, birth_date, birth_place, birth_country_id,
             death_date, death_place, death_country_id)
            VALUES
            (:first_name, :last_name, :birth_date, :birth_place, :birth_country_id,
             :death_date, :death_place, :death_country_id)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':first_name' => $firstName,
        ':last_name' => $lastName,
        ':birth_date' => $birthDate,
        ':birth_place' => $birthPlace,
        ':birth_country_id' => $birthCountryId,
        ':death_date' => $deathDate,
        ':death_place' => $deathPlace,
        ':death_country_id' => $deathCountryId
    ]);

    return (int) $pdo->lastInsertId();
}
