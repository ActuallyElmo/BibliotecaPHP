<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Înregistrare utilizator</title>
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
    <h2>Înregistrare utilizator</h2>
    <form method="POST" action="inregistrare.php">
        <label for="nume">Nume:</label>
        <input type="text" id="nume" name="nume" required>
        
        <label for="prenume">Prenume:</label>
        <input type="text" id="prenume" name="prenume" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="status_membru">Status de membru:</label>
        <select id="status_membru" name="status_membru" required>
            <option value="Standard">Standard</option>
            <option value="Premium">Premium</option>
        </select>
        
        <label for="parola">Parola:</label>
        <input type="password" id="parola" name="parola" required>
        
        <button type="submit">Înregistrare</button>
    </form>
</body>
</html>