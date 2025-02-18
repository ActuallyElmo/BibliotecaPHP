<?php
session_start(); // Pornim sesiunea
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");

$isAdmin = isset($_SESSION['status_membru']) && $_SESSION['status_membru'] === 'Admin';

?>


<nav>
    <ul>
        <li><a href="/proiectphp/index.php">Bibliotecă Online</a></li>
        <li><a href="/proiectphp/app/views/catalog.php">Catalogul de cărți</a></li>
        <li><a href="/proiectphp/app/views/imprumuturi.php">Împrumuturi</a></li>
        <li><a href="/proiectphp/app/views/Contact.php">Contact</a></li>

        <?php if ($isAdmin): ?>
            <!-- pagina de management pentru admin -->
            <li><a href="/proiectphp/app/views/manage.php">Manage</a></li>
        <?php endif; ?>

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
