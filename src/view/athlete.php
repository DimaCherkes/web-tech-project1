<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Athlete Profile</title>
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

    <main id="athlete-profile">
        <h1 id="full-name">Loading...</h1>

        <section class="details-card">
            <h2>Personal Information</h2>
            <div id="personal-info">
                <!-- Data will be loaded here -->
            </div>
        </section>

        <section class="participation-history">
            <h2>Olympic History</h2>
            <table id="participationTable">
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Type</th>
                        <th>City</th>
                        <th>Discipline</th>
                        <th>Category</th>
                        <th>Medal / Place</th>
                    </tr>
                </thead>
                <tbody id="participationBody">
                    <!-- Data will be loaded here -->
                </tbody>
            </table>
        </section>
    </main>

    <script src="/project1/view/js/athlete.js"></script>
</body>
</html>
