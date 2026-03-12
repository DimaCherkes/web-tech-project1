<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Olympics Athletes</title>
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
    <h1>Register form</h1>

    <?php if ($success): ?>
        <div class="success-message">
            <p>Registration successful! You can now log in.</p>
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
