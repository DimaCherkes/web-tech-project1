<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import CSV - Olympijskí športovci</title>
    <link rel="stylesheet" href="/project1/view/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>

    <main>
        <h1>Import údajov o športovcoch z CSV</h1>

        <?php if (!empty($errors)): ?>
            <div class="error-messages" style="margin-bottom: 20px; padding: 15px; background-color: #ffebee; color: #c62828; border-radius: 4px;">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($successMessage)): ?>
            <div class="success-message" style="margin-bottom: 20px; padding: 15px; background-color: #e8f5e9; color: #2e7d32; border-radius: 4px;">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <div class="details-card">
            <form method="POST" action="/project1/import" enctype="multipart/form-data" class="register-form" style="max-width: 100%;">
                <div class="form-group">
                    <label for="csv_file">Vyberte CSV súbor:</label>
                    <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
                </div>
                <button type="submit">Nahrať a spracovať</button>
            </form>
        </div>

        <?php if (isset($importStats) && !empty($importStats)): ?>
            <div class="details-card" style="margin-top: 20px;">
                <h3>Výsledky importu:</h3>
                <p>Spracovanie bolo dokončené.</p>
                <ul style="line-height: 1.6;">
                    <li><strong>Celkový počet riadkov v súbore:</strong> <?php echo (int)($importStats['total_rows'] ?? 0); ?></li>
                    <li><strong>Úspešne spracované záznamy:</strong> <?php echo (int)($importStats['processed'] ?? 0); ?></li>
                    <li><strong>Preskočené riadky (chyby):</strong> <?php echo (int)($importStats['skipped'] ?? 0); ?></li>
                </ul>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>