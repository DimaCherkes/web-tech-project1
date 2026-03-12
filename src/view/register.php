<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Olympics Athletes</title>
    <link rel="stylesheet" href="/project1/view/css/style.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="/project1/">Home</a></li>
            <li><a href="/project1/register">Register</a></li>
        </ul>
    </nav>
</header>

<main>
    <h1>Register form</h1>

    <?php if ($success): ?>
        <div class="success-message">
            <p>Registration successful! Please set up Two-Factor Authentication.</p>
            <div class="tfa-setup" style="text-align: center; margin: 20px 0; padding: 20px; border: 1px solid #ddd; background: #fff; border-radius: 8px;">
                <p>Scan this QR code with your authenticator app (e.g., Google Authenticator):</p>
                <img src="<?php echo $qrCode; ?>" alt="2FA QR Code" style="margin: 15px 0;">
                <p>Or enter this code manually: <strong><?php echo $tfaSecret; ?></strong></p>
                <p style="margin-top: 15px;"><a href="/project1/login" class="button">Go to Login</a></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <!-- ... -->
    <?php endif; ?>

    <form method="post" action="/project1/register" class="register-form">

                    <li class="error"><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="/register" class="register-form">
        <div class="form-group">
            <label for="firstName">First Name:</label>
            <input type="text" name="firstName" id="firstName" placeholder="e.g. John" value="<?php echo htmlspecialchars($_POST['firstName'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="lastName">Last Name:</label>
            <input type="text" name="lastName" id="lastName" placeholder="e.g. Doe" value="<?php echo htmlspecialchars($_POST['lastName'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" placeholder="e.g. johndoe@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password">
        </div>

        <div class="form-group">
            <label for="password_repeat">Repeat Password:</label>
            <input type="password" name="password_repeat" id="password_repeat">
        </div>

        <button type="submit">Create account</button>
    </form>

</main>

</body>
</html>
