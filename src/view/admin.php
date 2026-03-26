<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Administrácia - Olympijské hry</title>
    <link rel="stylesheet" href="/project1/view/css/style.css">
    <style>
        section { margin-bottom: 40px; padding: 20px; border: 1px solid #ddd; }
        form { display: grid; gap: 10px; max-width: 500px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #eee; padding: 8px; text-align: left; }
        .actions { display: flex; gap: 5px; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>

    <main>
        <h1>Administrácia entít</h1>

        <!-- ATHLETES -->
        <section>
            <h2>Športovci</h2>
            <form action="/project1/admin/athlete/create" method="POST">
                <h3>Pridať nového športovca</h3>
                <input type="text" name="firstName" placeholder="Meno" required>
                <input type="text" name="lastName" placeholder="Priezvisko" required>
                <input type="text" name="birthDate" placeholder="Dátum narodenia (YYYY-MM-DD)">
                <input type="text" name="birthPlace" placeholder="Miesto narodenia">
                <input type="text" name="birthCountryName" placeholder="Krajina narodenia (názov)">
                <input type="text" name="deathDate" placeholder="Dátum úmrtia (YYYY-MM-DD)">
                <input type="text" name="deathPlace" placeholder="Miesto úmrtia">
                <input type="text" name="deathCountryName" placeholder="Krajina úmrtia (názov)">
                <button type="submit">Vytvoriť</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Meno</th>
                        <th>Priezvisko</th>
                        <th>Akcie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['athletes'] as $a): ?>
                    <tr>
                        <td><?= $a['id'] ?></td>
                        <td><?= htmlspecialchars($a['first_name']) ?></td>
                        <td><?= htmlspecialchars($a['last_name']) ?></td>
                        <td class="actions">
                            <a href="/project1/admin/athlete/delete?id=<?= $a['id'] ?>" onclick="return confirm('Naozaj vymazať?')">Vymazať</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- COUNTRIES -->
        <section>
            <h2>Krajiny</h2>
            <form action="/project1/admin/country/create" method="POST">
                <input type="text" name="name" placeholder="Názov krajiny" required>
                <input type="text" name="code" placeholder="Kód (napr. SVK)">
                <button type="submit">Pridať krajinu</button>
            </form>
            <table>
                <?php foreach ($data['countries'] as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['code'] ?? '') ?>)</td>
                    <td><a href="/project1/admin/country/delete?id=<?= $c['id'] ?>">Vymazať</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <!-- DISCIPLINES -->
        <section>
            <h2>Disciplíny</h2>
            <form action="/project1/admin/discipline/create" method="POST">
                <input type="text" name="name" placeholder="Názov disciplíny" required>
                <input type="text" name="category" placeholder="Kategória">
                <button type="submit">Pridať disciplínu</button>
            </form>
            <table>
                <?php foreach ($data['disciplines'] as $d): ?>
                <tr>
                    <td><?= $d['id'] ?></td>
                    <td><?= htmlspecialchars($d['name']) ?> (<?= htmlspecialchars($d['category'] ?? '-') ?>)</td>
                    <td><a href="/project1/admin/discipline/delete?id=<?= $d['id'] ?>">Vymazať</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <!-- OLYMPIC GAMES -->
        <section>
            <h2>Olympijské hry</h2>
            <form action="/project1/admin/game/create" method="POST">
                <input type="number" name="year" placeholder="Rok" required>
                <select name="type">
                    <option value="LOH">LOH</option>
                    <option value="ZOH">ZOH</option>
                </select>
                <input type="text" name="city" placeholder="Mesto" required>
                <select name="countryId" required>
                    <option value="">Vyberte krajinu</option>
                    <?php foreach ($data['countries'] as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Pridať hry</button>
            </form>
            <table>
                <?php foreach ($data['games'] as $g): ?>
                <tr>
                    <td><?= $g['id'] ?></td>
                    <td><?= $g['year'] ?> <?= $g['type'] ?> - <?= htmlspecialchars($g['city'] ?? '') ?> (<?= htmlspecialchars($g['country_name'] ?? '') ?>)</td>
                    <td><a href="/project1/admin/game/delete?id=<?= $g['id'] ?>">Vymazať</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <!-- ATHLETE MEDALS (RELATIONSHIP) -->
        <section>
            <h2>Priradenie medailí</h2>
            <form action="/project1/admin/medal/create" method="POST">
                <select name="athleteId" required>
                    <option value="">Vyberte športovca</option>
                    <?php foreach ($data['athletes'] as $a): ?>
                        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="gameId" required>
                    <option value="">Vyberte hry</option>
                    <?php foreach ($data['games'] as $g): ?>
                        <option value="<?= $g['id'] ?>"><?= $g['year'] ?> <?= $g['type'] ?> (<?= htmlspecialchars($g['city'] ?? '') ?>)</option>
                    <?php endforeach; ?>
                </select>
                <select name="disciplineId" required>
                    <option value="">Vyberte disciplínu</option>
                    <?php foreach ($data['disciplines'] as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="medalTypeId" required>
                    <option value="">Vyberte medailu</option>
                    <?php foreach ($data['medalTypes'] as $mt): ?>
                        <option value="<?= $mt['id'] ?>"><?= htmlspecialchars($mt['name']) ?> (<?= $mt['placing'] ?>.)</option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Priradiť medailu</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Športovec</th>
                        <th>Hry</th>
                        <th>Disciplína</th>
                        <th>Medaila</th>
                        <th>Akcia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['athleteMedals'] as $am): ?>
                    <tr>
                        <td><?= htmlspecialchars($am['first_name'] . ' ' . $am['last_name']) ?></td>
                        <td><?= $am['year'] ?></td>
                        <td><?= htmlspecialchars($am['discipline_name']) ?></td>
                        <td><?= htmlspecialchars($am['medal_name']) ?></td>
                        <td><a href="/project1/admin/medal/delete?id=<?= $am['id'] ?>">Vymazať</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

    </main>
</body>
</html>
