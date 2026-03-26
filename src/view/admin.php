<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Administrácia - Olympijské hry</title>
    <link rel="stylesheet" href="/project1/view/css/style.css">
    <style>
        section { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; }
        form { display: grid; gap: 10px; max-width: 500px; }
        .banner { padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid transparent; display: none; }
        .success-banner { background: #d4edda; color: #155724; border-color: #c3e6cb; }
        .error-banner { background: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        h1 { color: #333; }
        h2 { border-bottom: 2px solid #eee; padding-bottom: 10px; margin-top: 0; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>

    <main>
        <h1>Administrácia - vytvorenie entít</h1>

        <div id="statusBanner" class="banner"></div>

        <div class="admin-grid">
            <!-- ATHLETES -->
            <section>
                <h2>Pridať športovca</h2>
                <form data-api="/project1/api/athletes">
                    <input type="text" name="firstName" placeholder="Meno" required>
                    <input type="text" name="lastName" placeholder="Priezvisko" required>
                    <input type="text" name="birthDate" placeholder="Dátum narodenia (YYYY-MM-DD)">
                    <input type="text" name="birthPlace" placeholder="Miesto narodenia">
                    
                    <select name="birthCountryId">
                        <option value="">Vyberte krajinu narodenia</option>
                        <?php foreach ($data['countries'] as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <input type="text" name="deathDate" placeholder="Dátum úmrtia (YYYY-MM-DD)">
                    <input type="text" name="deathPlace" placeholder="Miesto úmrtia">
                    
                    <select name="deathCountryId">
                        <option value="">Vyberte krajinu úmrtia</option>
                        <?php foreach ($data['countries'] as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit">Vytvoriť športovca</button>
                </form>
            </section>

            <!-- COUNTRIES -->
            <section>
                <h2>Pridať krajinu</h2>
                <form data-api="/project1/api/countries">
                    <input type="text" name="name" placeholder="Názov krajiny" required>
                    <input type="text" name="code" placeholder="Kód (napr. SVK)">
                    <button type="submit">Pridať krajinu</button>
                </form>
            </section>

            <!-- DISCIPLINES -->
            <section>
                <h2>Pridať disciplínu</h2>
                <form data-api="/project1/api/disciplines">
                    <input type="text" name="name" placeholder="Názov disciplíny" required>
                    <input type="text" name="category" placeholder="Kategória">
                    <button type="submit">Pridať disciplínu</button>
                </form>
            </section>

            <!-- OLYMPIC GAMES -->
            <section>
                <h2>Pridať olympijské hry</h2>
                <form data-api="/project1/api/games">
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
            </section>

            <!-- ATHLETE MEDALS -->
            <section>
                <h2>Priradiť medailu</h2>
                <form data-api="/project1/api/medals">
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
            </section>
        </div>
    </main>

    <script>
        document.querySelectorAll('form[data-api]').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const banner = document.getElementById('statusBanner');
                const apiUrl = form.dataset.api;
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                try {
                    const response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    banner.style.display = 'block';
                    if (response.status === 201) {
                        banner.className = 'banner success-banner';
                        banner.textContent = result.message || 'Záznam úspešne vytvorený.';
                        form.reset();
                    } else {
                        banner.className = 'banner error-banner';
                        banner.textContent = result.error || 'Nastala chyba pri spracovaní.';
                    }
                } catch (error) {
                    banner.style.display = 'block';
                    banner.className = 'banner error-banner';
                    banner.textContent = 'Chyba pripojenia k serveru.';
                }

                // Hide banner after 5 seconds
                setTimeout(() => {
                    banner.style.display = 'none';
                }, 5000);
            });
        });
    </script>
</body>
</html>
