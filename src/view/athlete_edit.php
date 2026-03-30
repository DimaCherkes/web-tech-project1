<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upraviť športovca</title>
    <link rel="stylesheet" href="/project1/view/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>

    <main>
        <h1>Upraviť údaje športovca</h1>
        <form id="editAthleteForm" class="details-card">
            <input type="hidden" id="athleteId">
            <div class="form-group">
                <label for="firstName">Meno:</label>
                <input type="text" id="firstName" required>
            </div>
            <div class="form-group">
                <label for="lastName">Priezvisko:</label>
                <input type="text" id="lastName" required>
            </div>
            <div class="form-group">
                <label for="birthDate">Dátum narodenia:</label>
                <input type="date" id="birthDate">
            </div>
            <div class="form-group">
                <label for="birthPlace">Miesto narodenia:</label>
                <input type="text" id="birthPlace">
            </div>
            <div class="form-group">
                <label for="birthCountryId">Krajina narodenia:</label>
                <select id="birthCountryId">
                    <option value="">-- Vyberte krajinu --</option>
                </select>
            </div>
            <div class="form-group">
                <label for="deathDate">Dátum úmrtia:</label>
                <input type="date" id="deathDate">
            </div>
            <div class="form-group">
                <label for="deathPlace">Miesto úmrtia:</label>
                <input type="text" id="deathPlace">
            </div>
            <div class="form-group">
                <label for="deathCountryId">Krajina úmrtia:</label>
                <select id="deathCountryId">
                    <option value="">-- Vyberte krajinu --</option>
                </select>
            </div>
            
            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Uložiť zmeny</button>
                <a href="/project1/" class="btn">Zrušiť</a>
            </div>
        </form>
    </main>

    <script src="/project1/view/js/athlete_edit.js"></script>
</body>
</html>
