<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil športovca</title>
    <link rel="stylesheet" href="/project1/view/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>

    <main id="athlete-profile">
        <h1 id="full-name">Načítavam...</h1>

        <section class="details-card">
            <h2>Osobné informácie</h2>
            <div id="personal-info">
                <!-- Dáta sa načítajú sem -->
            </div>
        </section>

        <section class="participation-history">
            <h2>Olympijská história</h2>
            <table id="participationTable">
                <thead>
                    <tr>
                        <th>Rok</th>
                        <th>Typ</th>
                        <th>Mesto</th>
                        <th>Disciplína</th>
                        <th>Kategória</th>
                        <th>Medaile / Umiestnenie</th>
                    </tr>
                </thead>
                <tbody id="participationBody">
                    <!-- Dáta sa načítajú sem -->
                </tbody>
            </table>
        </section>

    </main>

    <script src="/project1/view/js/athlete.js"></script>
</body>
</html>
