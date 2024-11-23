<?php
session_start(); // Pornim sesiunea
?>
<header>
    <h1>Biblioteca Online</h1>
    <p>Descoperă cărți, rezervă și împrumută online!</p>
</header>

<nav>
    <ul>
        <li><a href="catalog.php">Catalogul de cărți</a></li>
        <li><a href="imprumuturi.php">Împrumuturi</a></li>

        <?php if (isset($_SESSION['user_first_name'])): ?>
            <!-- prenumele utilizatorului logat -->
            <li style="color: white;"><?php echo htmlspecialchars($_SESSION['user_first_name']); ?></li>
            <li><a href="logout.php">Deconectare</a></li>
        <?php else: ?>
            <!-- login/register -->
            <li><a href="autentificare.php">Autentificare</a></li>
            <li><a href="inregistrare.php">Înregistrare</a></li>
        <?php endif; ?>
    </ul>
</nav>
