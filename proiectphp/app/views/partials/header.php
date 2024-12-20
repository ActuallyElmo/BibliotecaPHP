<?php
session_start(); // Pornim sesiunea
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");

?>
<header>
    <h1>Biblioteca Online</h1>
    <p>Descoperă cărți, rezervă și împrumută online!</p>
</header>

<nav>
    <ul>
        <li><a href="/proiectphp/app/views/catalog.php">Catalogul de cărți</a></li>
        <li><a href="/proiectphp/app/views/imprumuturi.php">Împrumuturi</a></li>

        <?php if (isset($_SESSION['user_first_name'])): ?>
            <!-- prenumele utilizatorului logat -->
            <li style="color: white;"><?php echo htmlspecialchars($_SESSION['user_first_name']); ?></li>
            <li><a href="/proiectphp/app/views/logout.php">Deconectare</a></li>
        <?php else: ?>
            <!-- login/register -->
            <li><a href="/proiectphp/app/views/autentificare.php">Autentificare</a></li>
            <li><a href="/proiectphp/app/views/inregistrare.php">Înregistrare</a></li>
        <?php endif; ?>
    </ul>
</nav>
