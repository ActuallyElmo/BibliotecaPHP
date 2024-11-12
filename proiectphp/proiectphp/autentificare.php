<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Autentificare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Biblioteca Online</h1>
        <p>Descoperă cărți, rezervă și împrumută online!</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="catalog.php">Catalogul de cărți</a></li>
            <li><a href="imprumuturi.php">Împrumuturi</a></li>
            <li><a href="autentificare.php">Autentificare</a></li>
            <li><a href="inregistrare.php">Înregistrare</a></li>
        </ul>
    </nav>

    <link rel="stylesheet" href="/biblioteca/style.css">

    <div class="container">
        <h2>Autentificare utilizator</h2>
        <form method="POST" action="autentificare.php">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="parola">Parola:</label>
            <input type="password" id="parola" name="parola" required>
            
            <button type="submit">Autentificare</button>
            <?php if (isset($mesaj)) { echo "<p class='error'>$mesaj</p>"; } ?>
        </form>
    </div>

</body>
</html>