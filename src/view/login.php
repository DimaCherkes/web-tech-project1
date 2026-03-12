<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Olympics Athletes</title>
    <link rel="stylesheet" href="/view/css/style.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/register">Register</a></li>
        </ul>
    </nav>
</header>

<main>
    <h1>Login</h1>

    <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li class="error"><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="/login" class="register-form">
        <?php if ($requires2FA): ?>
            <div class="success-message">
                <p>Please enter the 6-digit code from your authenticator app.</p>
            </div>
            
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            <input type="hidden" name="password" value="<?php echo htmlspecialchars($_POST['password'] ?? ''); ?>">
            
            <div class="form-group">
                <label for="tfaCode">2FA Code:</label>
                <input type="text" name="tfaCode" id="tfaCode" required placeholder="123456" autofocus autocomplete="one-time-code">
            </div>
            
            <button type="submit">Verify 2FA Code</button>
        <?php else: ?>
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit">Log In</button>
        <?php endif; ?>
    </form>

    <p style="text-align: center; margin-top: 20px;">
        Don't have an account? <a href="/register">Register here.</a>
    </p>

</main>

</body>
</html>
