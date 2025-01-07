<?php
session_start(); // Pornim sesiunea
require_once '../../config/db.php';
require_once '../controllers/UtilizatorController.php';

//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$error = "";
$success = "";

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Token unic
    }
    return $_SESSION['csrf_token'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verificarea token-ului CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $mesaj = "Cererea a fost invalidată din motive de securitate. Reîncearcă.";
        exit();
    }
    
    $nume = trim($_POST['nume']);
    $prenume = trim($_POST['prenume']);
    $email = trim($_POST['email']);
    $status_membru = trim($_POST['status_membru']);
    $parola = trim($_POST['parola']);

    // Verificare reCAPTCHA
    $recaptchaResponse = $_POST['g-recaptcha-response']; 
    $secretKey = '6Lc7MJ8qAAAAADD-RJAoVxcURpBpIPwE4FK__qa9';
    $url = "https://www.google.com/recaptcha/api/siteverify";

    $response = file_get_contents("$url?secret=$secretKey&response=$recaptchaResponse");
    $responseKeys = json_decode($response, true);

    if (!$responseKeys['success']) {
        $mesaj = "Verificare reCAPTCHA eșuată. Încearcă din nou.";
    } else {
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

                    //Resetarea tokenului CSRF pentru a preveni reutilizarea  lui
                    unset($_SESSION['csrf_token']);

                    $success = "Înregistrare reușită! Vă puteți autentifica.";
                } else {
                    $error = "Eroare la înregistrare. Încercați din nou.";
                }
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
    <link rel="stylesheet" href="../../public/style.css">
</head>
<body>
    <?php include '../views/partials/header.php'; ?>         
    
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

        <!-- recapcha-->
        <div class="g-recaptcha" data-sitekey="6Lc7MJ8qAAAAAEQaM63jSuBW784JhZqudNUDPf66"></div>
        
        <!-- Token CSRF -->
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

        <button type="submit">Înregistrare</button>  
    </form>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>