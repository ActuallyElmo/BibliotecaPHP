<?php
session_start(); // Pornim sesiunea

require_once './config/db.php';
require_once './app/controllers/UtilizatorController.php';

$mesaj = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $parola = trim($_POST['parola']);

    if (empty($email) || empty($parola)) {
        $mesaj = "Toate câmpurile sunt obligatorii!";
    } else {
        
        $utilizatorController = new UtilizatorController();
        $utilizator = $utilizatorController->getUserByEmail($email);

        if ($utilizator && $utilizator['parola'] === $parola) { 
            // Setam sesiunea
            $_SESSION['user_id'] = $utilizator['id_utilizator'];
            $_SESSION['user_name'] = $utilizator['nume'] . ' ' . $utilizator['prenume'];
            $_SESSION['status_membru'] = $utilizator['status_membru'];
            $_SESSION['user_first_name'] = $utilizator['prenume'];

            // Redirectionare
            header('Location: catalog.php');
            exit();
        } else {
            $mesaj = "Email sau parolă incorecte!";
        }
    }
}
?>

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

    <div class="container">
        <form method="POST" action="autentificare.php">
            <h2>Autentificare</h2>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="parola">Parola:</label>
            <input type="password" id="parola" name="parola" required>
            
            <button type="submit">Autentificare</button>
            <?php if (!empty($mesaj)) { echo "<p class='error'>$mesaj</p>"; } ?>
        </form>
    </div>

</body>
</html>
