<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login History - Olympics Athletes</title>
    <link rel="stylesheet" href="/project1/view/css/style.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="/project1/">Home</a></li>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <li><a href="/project1/history">My History</a></li>
                <li><a href="/project1/logout">Logout</a></li>
            <?php else: ?>
                <li><a href="/project1/login">Login</a></li>
                <li><a href="/project1/register">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <h1>My Login History</h1>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Login Type</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($history)): ?>
                    <tr>
                        <td colspan="2" style="text-align: center;">No history found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($history as $row): ?>
                        <tr>
                            <td>
                                <span class="badge <?php echo $row['login_type'] === 'OAUTH' ? 'badge-oauth' : 'badge-local'; ?>">
                                    <?php echo htmlspecialchars($row['login_type']); ?>
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
        <a href="/project1/" class="button">Back to Home</a>
    </p>
</main>

</body>
</html>
