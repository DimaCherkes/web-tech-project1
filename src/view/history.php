<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>História prihlásení - Olympijskí športovci</title>
    <link rel="stylesheet" href="/project1/view/css/style.css">
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>

<main>
    <h1>Moja história prihlásení</h1>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Typ prihlásenia</th>
                    <th>Dátum a čas</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($history)): ?>
                    <tr>
                        <td colspan="2" style="text-align: center;">Nenašla sa žiadna história.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($history as $row): ?>
                        <tr>
                            <td>
                                <span class="badge <?php echo $row['login_type'] === 'OAUTH' ? 'badge-oauth' : 'badge-local'; ?>">
                                    <?php echo $row['login_type'] === 'OAUTH' ? 'Google' : 'Lokálne'; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <p style="text-align: center; margin-top: 20px;">
        <a href="/project1/" class="button">Späť na domovskú stránku</a>
    </p>
</main>

</body>
</html>
