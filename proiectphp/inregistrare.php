<?php
require_once './config/db.php';
require_once './app/controllers/UtilizatorController.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$error = "";
$success = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nume = trim($_POST['nume']);
    $prenume = trim($_POST['prenume']);
    $email = trim($_POST['email']);
    $status_membru = trim($_POST['status_membru']);
    $parola = trim($_POST['parola']);

    if (empty($nume) || empty($prenume) || empty($email) || empty($status_membru) || empty($parola)) {
        $error = "Toate câmpurile sunt obligatorii!";
    } else {

        $utilizatorController = new UtilizatorController();

        $utilizatori = $utilizatorController->getAllUsers();
        $emailExista = false;

        foreach ($utilizatori as $utilizator) {
            if ($utilizator['email'] === $email) {
                $emailExista = true;
                break;
            }
        }

        if ($emailExista) {
            $error = "Adresa de email există deja!";
        } else {

            $inregistrat = $utilizatorController->addUser($nume, $prenume, $email, $status_membru, $parola);

            if ($inregistrat) {
                $success = "Înregistrare reușită! Vă puteți autentifica.";
            } else {
                $error = "Eroare la înregistrare. Încercați din nou.";
            }
        }
    }
}
?>

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
    
    <form method="POST" action="inregistrare.php">
        <h2>Înregistrare</h2>
        
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php endif; ?>

        <label for="nume">Nume:</label>
        <input type="text" id="nume" name="nume" value="<?php echo htmlspecialchars($nume ?? ''); ?>" required>
        
        <label for="prenume">Prenume:</label>
        <input type="text" id="prenume" name="prenume" value="<?php echo htmlspecialchars($prenume ?? ''); ?>" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
        
        <label for="status_membru">Status de membru:</label>
        <select id="status_membru" name="status_membru" required>
            <option value="Standard" <?php echo (isset($status_membru) && $status_membru === 'Standard') ? 'selected' : ''; ?>>Standard</option>
            <option value="Premium" <?php echo (isset($status_membru) && $status_membru === 'Premium') ? 'selected' : ''; ?>>Premium</option>
        </select>
        
        <label for="parola">Parola:</label>
        <input type="password" id="parola" name="parola" required>
        
        <button type="submit">Înregistrare</button>
    </form>
</body>
</html>