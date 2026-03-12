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

    <?php 
    $get_error = $_GET['error'] ?? null;
    if ($get_error) $errors[] = $get_error;
    
    if (!empty($errors)): 
    ?>
        <div class="error-messages">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li class="error"><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="/login" class="register-form">
        <!-- ... existing form fields ... -->
        <?php if ($requires2FA): ?>
            <!-- ... 2FA fields ... -->
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
            
            <div style="margin-top: 20px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
                <a href="/auth/google" class="google-btn" style="display: inline-block; background: #4285F4; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: bold;">
                    Sign in with Google
                </a>
            </div>
        <?php endif; ?>
    </form>

    <p style="text-align: center; margin-top: 20px;">
        Don't have an account? <a href="/register">Register here.</a>
    </p>

</main>

</body>
</html>
