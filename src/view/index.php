<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olympijskí športovci</title>
    <link rel="stylesheet" href="/project1/view/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>

    <main>


        <div class="filters">
            <div class="filter-group">
                <label for="categoryFilter">Kategória:</label>
                <select id="categoryFilter">
                    <option value="">Všetky kategórie</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="yearFilter">Rok:</label>
                <select id="yearFilter">
                    <option value="">Všetky roky</option>
                </select>
            </div>
        </div>

        <div class="table-container">
            <table id="athletesTable">
                <thead>
                    <tr>
                        <th data-sort="id">ID</th>
                        <th data-sort="firstName">Meno</th>
                        <th data-sort="lastName">Priezvisko</th>
                        <th data-sort="year">Rok</th>
                        <th data-sort="country">Krajina</th>
                        <th data-sort="sportName">Disciplína</th>
                    </tr>
                </thead>
                <tbody id="athletesBody">
                    <!-- Dáta sa načítajú sem -->
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <button id="prevPage">Predchádzajúca</button>
            <span id="pageInfo">Strana 1 z 1</span>
            <button id="nextPage">Nasledujúca</button>
        </div>
    </main>

    <script src="/project1/view/js/app.js"></script>
</body>
</html>
