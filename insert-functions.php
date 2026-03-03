<?php
/*
 * Repository
 */

function insertCountry(PDO $pdo, string $name, ?string $code = null): int {
    // TODO: verify this country has not been already persisted in DB
    $sql = "INSERT INTO countries (name, code) VALUES (:name, :code)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':name' => $name,
        ':code' => $code
    ]);

    return (int) $pdo->lastInsertId();
}

function insertOlympicGames(PDO $pdo, int $year, string $type, string $city, int $countryName): int {

    // TODO: verify this country has not been already persisted in DB

    // TODO: kontrola, ci argument type splna podmienky ENUM typu (LOH,ZOH)
    // TODO: findCountryId by countryName in country table

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

function insertAthlete(
    PDO $pdo,
    string $firstName,
    string $lastName,
    ?string $birthDate = null,
    ?string $birthPlace = null,
    ?int $birthCountryName = null,
    ?string $deathDate = null,
    ?string $deathPlace = null,
    ?int $deathCountryName = null
): int {

    // TODO: verify this country has not been already persisted in DB

    // TODO: find countryId by 'deathCountryName'
    // TODO: find countryId by 'birthCountryName'

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

function insertDiscipline(
    PDO $pdo, string $name, ?string $category = null
) : int {

    // TODO: verify this country has not been already persisted in DB


    $sql = "INSERT INTO countries (name, category) VALUES (:name, :category)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':name' => $name,
        ':code' => $category
    ]);

    return (int) $pdo->lastInsertId();
}

