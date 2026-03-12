<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olympics Athletes</title>
    <link rel="stylesheet" href="/view/css/style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="/">Home</a></li>
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <li><span>Welcome, <?php echo htmlspecialchars($_SESSION['fullName']); ?></span></li>
                    <li><a href="/logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="/login">Login</a></li>
                    <li><a href="/register">Register</a></li>
                <?php endif; ?>
                <li><a href="/about">About</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Athletes List</h1>

        <div class="filters">
            <div class="filter-group">
                <label for="categoryFilter">Category:</label>
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="yearFilter">Year:</label>
                <select id="yearFilter">
                    <option value="">All Years</option>
                </select>
            </div>
        </div>

        <div class="table-container">
            <table id="athletesTable">
                <thead>
                    <tr>
                        <th data-sort="id">ID</th>
                        <th data-sort="firstName">First Name</th>
                        <th data-sort="lastName">Last Name</th>
                        <th data-sort="year">Year</th>
                        <th data-sort="country">Country</th>
                        <th data-sort="sportName">Discipline</th>
                    </tr>
                </thead>
                <tbody id="athletesBody">
                    <!-- Data will be loaded here -->
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <button id="prevPage">Previous</button>
            <span id="pageInfo">Page 1 of 1</span>
            <button id="nextPage">Next</button>
        </div>
    </main>

    <script src="/view/js/app.js"></script>
</body>
</html>
