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

        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <section class="admin-actions" style="margin-top: 30px; padding: 20px; background: #fdf2f2; border: 1px solid #f8d7da; border-radius: 8px;">
                <h3 style="margin-top:0">Administratívne akcie</h3>
                <div style="display: flex; gap: 15px;">
                    <button id="deleteBtn" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">Vymazať športovca</button>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <script src="/project1/view/js/athlete.js"></script>
</body>
</html>
