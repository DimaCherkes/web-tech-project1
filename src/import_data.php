<?php

session_start();

// Restriction: Only logged in users can access this page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: /project1/login");
    exit;
}

require_once(__DIR__ . '/../../config.php');
require_once 'utils-functions.php';
require_once(__DIR__ . '/repository/athlete_medals_repo.php');
require_once(__DIR__ . '/repository/athletes_repo.php');
require_once(__DIR__ . '/repository/countries_repo.php');
require_once(__DIR__ . '/repository/discipline_repo.php');
require_once(__DIR__ . '/repository/medal_types_repo.php');
require_once(__DIR__ . '/repository/olympic_games_repo.php');

$data = []; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {

    $file = $_FILES['csv_file'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

    if (strtolower($ext) !== 'csv') {
        die("Povolené sú iba CSV súbory.");
    }

    if ($file['error'] === 0) {
        $data = parseCsvToAssocArray($file['tmp_name'], ",");
    }

    $pdo = connectDatabase($hostname, $database, $username, $password);
    if ($pdo && !empty($data)) {
        // Import logic...
    }
}
?>


<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Import CSV - Olympijskí športovci</title>
    <link rel="stylesheet" href="/project1/view/css/style.css">
</head>
<body>
<?php include __DIR__ . '/view/partials/header.php'; ?>

<main>
    <h1>Import údajov o športovcoch</h1>

    <div class="details-card">
        <form method="POST" enctype="multipart/form-data" class="register-form" style="max-width: 100%;">
            <div class="form-group">
                <label for="csv_file">Vyberte CSV súbor:</label>
                <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
            </div>
            <button type="submit">Nahrať a spracovať</button>
        </form>
    </div>

    <?php if (!empty($data)): ?>
        <div class="details-card" style="margin-top: 20px;">
            <h3>Výsledky importu:</h3>
            <p>Spracovanie dokončené. Podrobnosti:</p>
            <pre style="background: #f1f1f1; padding: 10px; border-radius: 4px; overflow: auto; max-height: 400px;"><?php 
                echo "Spracovaných " . count($data) . " záznamov.";
            ?></pre>
        </div>
    <?php endif; ?>
</main>

</body>
</html>
