<?php
require_once 'insert-functions.php';
require_once(__DIR__ . '/../config.php');

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
    if ($pdo) {
        echo "Connected to DB.<br>";
        
        foreach ($data as $row) {
            if (isset($row['country'])) {
                insertCountry($pdo, $row['country'], $row['code']);
                echo "Country added: " . $row['country'] . "<br>";
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
    
    // Очищаем заголовки от лишних пробелов и символов переноса строки (важно для PHP!)
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


