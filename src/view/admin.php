<?php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: /project1/login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrácia - Olympijské hry</title>
    <link rel="stylesheet" href="/project1/view/css/style.css">
    <style>
        .admin-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .tab-btn {
            padding: 10px 20px;
            background: #f1f1f1;
            border: 1px solid #ccc;
            border-radius: 4px 4px 0 0;
            cursor: pointer;
            font-weight: bold;
            color: #333;
        }
        .tab-btn.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .admin-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .admin-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #eee;
        }
        .admin-form .form-group {
            margin-bottom: 0;
        }
        .admin-form button {
            align-self: flex-end;
            padding: 10px;
        }
        .filters-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            background: #f0f7ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            align-items: flex-end;
        }
        .filters-container .form-group {
            margin-bottom: 0;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.9em;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #333;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 8px;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover { color: black; }
        
        .action-btns {
            display: flex;
            gap: 5px;
        }
        
        #loadingOverlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        /* Notifications */
        #notificationArea {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .notification {
            padding: 15px 25px;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease-out;
            min-width: 250px;
        }
        .notification.success { background-color: #28a745; }
        .notification.error { background-color: #dc3545; }
        .notification.info { background-color: #007bff; }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Bulk Athlete Rows */
        .athlete-bulk-row {
            background: #fff;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            position: relative;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
        }
        .remove-row-btn {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            border: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>

    <main>
        <h1>Administrácia entít</h1>
        <div id="notificationArea"></div>

        <div class="admin-tabs">
            <button class="tab-btn active" onclick="openTab('countries')">Krajiny</button>
            <button class="tab-btn" onclick="openTab('disciplines')">Disciplíny</button>
            <button class="tab-btn" onclick="openTab('games')">OH Hry</button>
            <button class="tab-btn" onclick="openTab('medals')">Medaily</button>
            <button class="tab-btn" onclick="openTab('athletes')">Športovci</button>
        </div>

        <!-- COUNTRIES -->
        <div id="countries" class="tab-content active">
            <div class="admin-section">
                <h2>Pridať novú krajinu</h2>
                <form id="addCountryForm" class="admin-form">
                    <div class="form-group">
                        <label>Názov:</label>
                        <input type="text" name="name" required placeholder="Slovensko">
                    </div>
                    <div class="form-group">
                        <label>Kód (ISO):</label>
                        <input type="text" name="code" placeholder="SVK">
                    </div>
                    <button type="submit">Uložiť krajinu</button>
                </form>

                <h2>Zoznam krajín</h2>
                <div class="table-container">
                    <table id="countriesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Názov</th>
                                <th>Kód</th>
                                <th>Akcie</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- DISCIPLINES -->
        <div id="disciplines" class="tab-content">
            <div class="admin-section">
                <h2>Pridať novú disciplínu</h2>
                <form id="addDisciplineForm" class="admin-form">
                    <div class="form-group">
                        <label>Názov:</label>
                        <input type="text" name="name" required placeholder="Vodný slalom">
                    </div>
                    <div class="form-group">
                        <label>Kategória:</label>
                        <input type="text" name="category" placeholder="Kanoistika">
                    </div>
                    <button type="submit">Uložiť disciplínu</button>
                </form>

                <h2>Zoznam disciplín</h2>
                <div class="table-container">
                    <table id="disciplinesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Názov</th>
                                <th>Kategória</th>
                                <th>Akcie</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- OLYMPIC GAMES -->
        <div id="games" class="tab-content">
            <div class="admin-section">
                <h2>Pridať nové hry</h2>
                <form id="addGameForm" class="admin-form">
                    <div class="form-group">
                        <label>Rok:</label>
                        <input type="number" name="year" required placeholder="2024">
                    </div>
                    <div class="form-group">
                        <label>Typ:</label>
                        <select name="type">
                            <option value="LOH">LOH (Letné)</option>
                            <option value="ZOH">ZOH (Zimné)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Mesto:</label>
                        <input type="text" name="city" required placeholder="Paríž">
                    </div>
                    <div class="form-group">
                        <label>Krajina:</label>
                        <select name="country_id" id="gameCountrySelect" required>
                            <option value="">-- Vyberte krajinu --</option>
                        </select>
                    </div>
                    <button type="submit">Uložiť hry</button>
                </form>

                <h2>Zoznam olympijských hier</h2>
                <div class="table-container">
                    <table id="gamesTable">
                        <thead>
                            <tr>
                                <th>Rok</th>
                                <th>Typ</th>
                                <th>Mesto</th>
                                <th>Akcie</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ATHLETE MEDALS -->
        <div id="medals" class="tab-content">
            <div class="admin-section">
                <h2>Priradiť medailu športovcovi</h2>
                <form id="addMedalForm" class="admin-form">
                    <div class="form-group">
                        <label>Športovec:</label>
                        <select name="athlete_id" id="medalAthleteSelect" required></select>
                    </div>
                    <div class="form-group">
                        <label>Hry:</label>
                        <select name="olympic_games_id" id="medalGameSelect" required></select>
                    </div>
                    <div class="form-group">
                        <label>Disciplína:</label>
                        <select name="discipline_id" id="medalDisciplineSelect" required></select>
                    </div>
                    <div class="form-group">
                        <label>Typ medaily:</label>
                        <select name="medal_type_id" required>
                            <option value="1">Zlato (1. miesto)</option>
                            <option value="2">Striebro (2. miesto)</option>
                            <option value="3">Bronz (3. miesto)</option>
                        </select>
                    </div>
                    <button type="submit">Priradiť medailu</button>
                </form>

                <h2>Zoznam medailí</h2>
                
                <div class="filters-container">
                    <div class="form-group">
                        <label>Typ:</label>
                        <select id="filterMedalType">
                            <option value="">Všetky</option>
                            <option value="LOH">LOH</option>
                            <option value="ZOH">ZOH</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Rok:</label>
                        <input type="number" id="filterMedalYear" placeholder="Rok" style="width: 100px;">
                    </div>
                    <div class="form-group">
                        <label>Medaile:</label>
                        <select id="filterMedalId">
                            <option value="">Všetky</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Disciplína:</label>
                        <select id="filterMedalDiscipline">
                            <option value="">Všetky</option>
                        </select>
                    </div>
                    <button onclick="refreshData('medals')" class="btn-sm">Filtrovať</button>
                </div>

                <div class="table-container">
                    <table id="medalsTable">
                        <thead>
                            <tr>
                                <th>Športovec</th>
                                <th>Hry</th>
                                <th>Disciplína</th>
                                <th>Medaile</th>
                                <th>Akcie</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ATHLETES -->
        <div id="athletes" class="tab-content">
            <div class="admin-section">
                <h2>Pridať športovcov (hromadne)</h2>
                <form id="addAthleteForm">
                    <div id="athletesRowsContainer">
                        <!-- Rows added via JS -->
                    </div>
                    
                    <div style="margin-top: 15px; display: flex; gap: 10px;">
                        <button type="button" id="addMoreAthletesBtn" class="btn-sm" style="background: #6c757d;">+ Pridať ďalšieho športovca</button>
                        <button type="submit" class="btn-sm btn-primary">Uložiť všetkých športovcov</button>
                    </div>
                </form>

                <h2 style="margin-top: 40px;">Zoznam športovcov</h2>

                <div class="filters-container">
                    <div class="form-group">
                        <label>Meno:</label>
                        <input type="text" id="filterAthleteFirstName" placeholder="Hľadať meno">
                    </div>
                    <div class="form-group">
                        <label>Priezvisko:</label>
                        <input type="text" id="filterAthleteLastName" placeholder="Hľadať priezvisko">
                    </div>
                    <button onclick="refreshData('athletes')" class="btn-sm">Hľadať</button>
                </div>

                <div class="table-container">
                    <table id="athletesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Meno</th>
                                <th>Priezvisko</th>
                                <th>Dátum narodenia</th>
                                <th>Akcie</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- EDIT MODAL -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Upraviť záznam</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="editForm" class="admin-form" style="grid-template-columns: 1fr;">
                <div id="editFields"></div>
                <button type="submit">Uložiť zmeny</button>
            </form>
        </div>
    </div>

    <div id="loadingOverlay">
        <strong>Načítavam...</strong>
    </div>

    <script src="/project1/view/js/admin.js"></script>
    <script>
        function openTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');
            
            if (typeof refreshData === 'function') refreshData(tabId);
        }
    </script>
</body>
</html>
