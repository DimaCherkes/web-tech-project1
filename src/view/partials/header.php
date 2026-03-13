<header>
    <nav>
        <ul>
            <li><a href="/project1/">Domov</a></li>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <li class="user-greeting">
                    <span>
                        Vitajte, <strong><?php echo htmlspecialchars($_SESSION['fullName']); ?></strong> 
                        <small>(<?php echo (isset($_SESSION['authSource']) && $_SESSION['authSource'] === 'google') ? 'Google' : 'Lokálne'; ?>)</small>
                    </span>
                </li>
                <li><a href="/project1/profile">Môj profil</a></li>
                <li><a href="/project1/history">Moja história</a></li>
                <li><a href="/project1/import">Import CSV</a></li>
                <li><a href="/project1/logout" class="logout-link">Odhlásiť sa</a></li>
            <?php else: ?>
                <li><a href="/project1/login">Prihlásenie</a></li>
                <li><a href="/project1/register">Registrácia</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
