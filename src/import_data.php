<?php

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/repository/athlete_medals_repo.php');
require_once(__DIR__ . '/repository/athletes_repo.php');
require_once(__DIR__ . '/repository/countries_repo.php');
require_once(__DIR__ . '/repository/discipline_repo.php');
require_once(__DIR__ . '/repository/medal_types_repo.php');
require_once(__DIR__ . '/repository/olympic_games_repo.php');

$data = []; // Definicia premennej pre ukladanie obsahu csv

// Ak bol odoslany formular, a vo formulari sa nachadza subor s klucom csv_file, spracujeme ho.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {

    $file = $_FILES['csv_file'];  // Ziskame subor zo superglobal pola
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);  // Zistime pripomu suboru...

    if (strtolower($ext) !== 'csv') {  // ...a skontrolujeme, ci ide o csv subor.
        die("Povolené sú iba CSV súbory.");  // Ak nie, skript sa ukonci.
    }

    if ($file['error'] === 0) {  // Ak bol subor nacitany bez chyby...
        $data = parseCsvToAssocArray($file['tmp_name'], ",");  // CSV usually uses comma
    }

    $pdo = connectDatabase($hostname, $database, $username, $password);
    if ($pdo && !empty($data)) {
        echo "Connected to DB. Processing " . count($data) . " rows...<br>";
        
        // Detect CSV type by headers of the first row
        $firstRow = $data[0];
        
        if (isset($firstRow['type'], $firstRow['year'], $firstRow['city'], $firstRow['country'])) {
            // Processing games CSV: oh_v2(OH).csv
            foreach ($data as $row) {
                $countryId = insertCountry($pdo, $row['country'], $row['code'] ?? null);
                insertOlympicGames($pdo, (int)$row['year'], $row['type'], $row['city'], $row['country']);
                echo "Processed game: {$row['type']}, {$row['year']} in {$row['city']}<br>";
            }
        } elseif (isset($firstRow['placing'], $firstRow['discipline'], $firstRow['name'], $firstRow['surname'])) {
            // Processing people CSV: oh_v2-people.csv
            foreach ($data as $row) {
                // 1. Ensure country exists
                insertCountry($pdo, $row['oh_country']);
                
                // 2. Ensure game exists
                $gameId = insertOlympicGames($pdo, (int)$row['oh_year'], $row['oh_type'], $row['oh_city'], $row['oh_country']);
                
                // 3. Ensure discipline exists
                $disciplineName = $row['discipline'];
                $disciplineCategory = null;
                if (strpos($disciplineName, ' - ') !== false) {
                    [$disciplineCategory, $disciplineName] = explode(' - ', $disciplineName, 2);
                }
                $disciplineId = insertDiscipline($pdo, trim($disciplineName), $disciplineCategory ? trim($disciplineCategory) : null);
                
                // 4. Ensure athlete exists
                $athleteId = insertAthlete(
                    $pdo,
                    $row['name'],
                    $row['surname'],
                    $row['birth_day'],
                    $row['birth_place'],
                    $row['birth_country'],
                    $row['death_day'],
                    $row['death_place'],
                    $row['death_country']
                );
                
                // 5. Insert placement/result
                insertPlacement($pdo, $athleteId, $gameId, $disciplineId, $row['placing']);
                
                echo "Processed athlete: {$row['name']} {$row['surname']} - {$row['discipline']}<br>";
            }
        } else {
            // Fallback for simple country lists or unknown format
            foreach ($data as $row) {
                if (isset($row['country'])) {
                    insertCountry($pdo, $row['country'], $row['code'] ?? null);
                    echo "Country added: " . $row['country'] . "<br>";
                }
            }
        }
    }
}
?>

<?php
function parseCsvToAssocArray(string $filePath, string $delimiter = ","): array
{
    $result = [];
    if (!file_exists($filePath)) return [];

    $handle = fopen($filePath, 'r');
    if (!$handle) return [];

    $headers = fgetcsv($handle, 0, $delimiter, "\"", ""); 
    if (!$headers) {
        fclose($handle);
        return [];
    }
    
    $headers = array_map('trim', $headers);

    // Parsovanie riadkov
    while (($row = fgetcsv($handle, 0, $delimiter, "\"", "")) !== false) {
        if (count($row) === count($headers)) {
            $result[] = array_combine($headers, $row);
        }
    }

    // Korektne ukoncenie prace so suborom a vratenie spracovanych dat.
    fclose($handle);
    return $result;
}

function connectDatabase(string $hostname, string $database, string $username, string $password): ?PDO {
    try {
        $pdo = new PDO("mysql:host=$hostname;dbname=$database;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Connection failed: " . $e->getMessage());
        return null;
    }
}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>CSV Upload</title>
</head>
<body>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="csv_file" accept=".csv" required>
    <br><br>
    <button type="submit">Nahrať a spracovať</button>
</form>

<?php if (!empty($data)): ?>
    <h3>Obsah súboru (prvý záznam):</h3>
    <pre><?php var_dump($data[0]); ?></pre>
    
    <h3>Všetky dáta:</h3>
    <pre><?php print_r($data); ?></pre>
<?php endif; ?>

</body>
</html>


