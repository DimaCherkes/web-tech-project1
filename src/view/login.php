<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prihlásenie - Olympijskí športovci</title>
    <link rel="stylesheet" href="/project1/view/css/style.css">
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>

<main>
    <h1>Prihlásenie</h1>

    <?php 
    $get_error = $_GET['error'] ?? null;
    if ($get_error) $errors[] = $get_error;
    
    if (!empty($errors)): 
    ?>
        <div class="error-messages">
            <ul>
                <?php foreach ($errors as $error): 
                    $msg = $error;
                    if ($error == "Incorrect email or password.") $msg = "Nesprávny e-mail alebo heslo.";
                    if ($error == "Invalid 2FA code.") $msg = "Neplatný 2FA kód.";
                ?>
                    <li class="error"><?php echo htmlspecialchars($msg); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="/project1/login" class="register-form">
        <?php if ($requires2FA): ?>
            <div class="success-message">
                <p>Zadajte prosím 6-miestny kód z vašej autentifikačnej aplikácie.</p>
            </div>
            
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            <input type="hidden" name="password" value="<?php echo htmlspecialchars($_POST['password'] ?? ''); ?>">
            
            <div class="form-group">
                <label for="tfaCode">2FA kód:</label>
                <input type="text" name="tfaCode" id="tfaCode" required placeholder="123456" autofocus autocomplete="one-time-code">
            </div>
            
            <button type="submit">Overiť 2FA kód</button>
        <?php else: ?>
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Heslo:</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit">Prihlásiť sa</button>
            
            <div style="margin-top: 20px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
                <a href="/project1/auth/google" class="google-btn" style="display: inline-block; background: #4285F4; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: bold;">
                    Prihlásiť sa cez Google
                </a>
            </div>
        <?php endif; ?>
    </form>

    <p style="text-align: center; margin-top: 20px;">
        Nemáte ešte účet? <a href="/project1/register">Zaregistrujte sa tu.</a>
    </p>

</main>

</body>
</html>
