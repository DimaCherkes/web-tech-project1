<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrácia - Olympijskí športovci</title>
    <link rel="stylesheet" href="/project1/view/css/style.css">
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>

<main>
    <h1>Registračný formulár</h1>

    <?php if ($success): ?>
        <div class="success-message">
            <p>Registrácia prebehla úspešne! Nastavte si, prosím, dvojfaktorovú autentifikáciu (2FA).</p>
            <div class="tfa-setup" style="text-align: center; margin: 20px 0; padding: 20px; border: 1px solid #ddd; background: #fff; border-radius: 8px;">
                <p>Naskenujte tento QR kód pomocou vašej autentifikačnej aplikácie (napr. Google Authenticator):</p>
                <img src="<?php echo $qrCode; ?>" alt="2FA QR kód" style="margin: 15px 0;">
                <p>Alebo zadajte tento kód manuálne: <strong><?php echo $tfaSecret; ?></strong></p>
                <p style="margin-top: 15px;"><a href="/project1/login" class="button">Prejsť na prihlásenie</a></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <ul>
                <?php foreach ($errors as $error): 
                    // Simple mapping for common errors to Slovak
                    $msg = $error;
                    if ($error == "Email is required.") $msg = "E-mail je povinný.";
                    if ($error == "Passwords do not match.") $msg = "Heslá sa nezhodujú.";
                    if (str_contains($error, "already exists")) $msg = "Používateľ s týmto e-mailom už existuje.";
                ?>
                    <li class="error"><?php echo htmlspecialchars($msg); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="/project1/register" class="register-form">
        <div class="form-group">
            <label for="firstName">Meno:</label>
            <input type="text" name="firstName" id="firstName" placeholder="napr. Ján" value="<?php echo htmlspecialchars($_POST['firstName'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="lastName">Priezvisko:</label>
            <input type="text" name="lastName" id="lastName" placeholder="napr. Novák" value="<?php echo htmlspecialchars($_POST['lastName'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" placeholder="napr. jan.novak@priklad.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="password">Heslo:</label>
            <input type="password" name="password" id="password">
        </div>

        <div class="form-group">
            <label for="password_repeat">Zopakujte heslo:</label>
            <input type="password" name="password_repeat" id="password_repeat">
        </div>

        <button type="submit">Vytvoriť účet</button>

        <div style="margin-top: 20px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
            <a href="/project1/auth/google" class="google-btn" style="display: inline-block; background: #4285F4; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: bold;">
                Registrácia cez Google
            </a>
        </div>
    </form>

</main>

</body>
</html>
