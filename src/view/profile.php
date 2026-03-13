<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Môj profil - Olympijskí športovci</title>
    <link rel="stylesheet" href="/project1/view/css/style.css">
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>

<main>
    <h1>Môj profil</h1>

    <?php if ($success): ?>
        <div class="success-message">
            <p><?php echo htmlspecialchars($success); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li class="error"><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="details-card">
        <h2>Osobné údaje</h2>
        <form method="post" action="/project1/profile" class="register-form" style="max-width: 100%;">
            <div class="form-group">
                <label for="firstName">Meno:</label>
                <input type="text" name="firstName" id="firstName" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="lastName">Priezvisko:</label>
                <input type="text" name="lastName" id="lastName" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            </div>
            <div class="form-group">
                <label>E-mail:</label>
                <input type="text" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background: #eee; cursor: not-allowed;">
                <small>E-mail nie je možné zmeniť.</small>
            </div>
            <button type="submit" name="update_profile">Uložiť zmeny</button>
        </form>
    </div>

    <?php if (empty($user['google_id'])): // Смена пароля только для локальных пользователей ?>
    <div class="details-card" style="margin-top: 20px;">
        <h2>Zmena hesla</h2>
        <form method="post" action="/project1/profile" class="register-form" style="max-width: 100%;">
            <div class="form-group">
                <label for="password">Nové heslo:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="password_repeat">Zopakujte nové heslo:</label>
                <input type="password" name="password_repeat" id="password_repeat" required>
            </div>
            <button type="submit" name="change_password">Zmeniť heslo</button>
        </form>
    </div>
    <?php else: ?>
        <div class="details-card" style="margin-top: 20px;">
            <p>Ste prihlásený cez Google. Správa hesla prebieha vo vašom Google účte.</p>
        </div>
    <?php endif; ?>

</main>

</body>
</html>
