<?php
session_start(); // Pornim sesiunea

require_once '../../config/db.php';
require_once '../../app/controllers/UtilizatorController.php';

$mesaj = "";

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Token unic
    }
    return $_SESSION['csrf_token'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verificarea token-ului CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $mesaj = "Cererea a fost invalidata din motive de securitate.";
        exit();
    }


    $email = trim($_POST['email']);
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
        if (empty($email) || empty($parola)) {
            $mesaj = "Toate campurile sunt obligatorii!";
        } else {
        
            $utilizatorController = new UtilizatorController();
            $utilizator = $utilizatorController->getUserByEmail($email);

            if ($utilizator && $utilizator['parola'] === $parola) { 
                // Setam sesiunea
                $_SESSION['user_id'] = $utilizator['id_utilizator'];
                $_SESSION['status_membru'] = $utilizator['status_membru'];
                $_SESSION['user_first_name'] = $utilizator['prenume'];
                $_SESSION['email'] = $utilizator['email'];

                //Resetarea tokenului CSRF pentru a preveni reutilizarea  lui
                unset($_SESSION['csrf_token']);

                // Redirectionare
                header('Location: /proiectphp/app/views/catalog.php');
                exit();
            } else {
                $mesaj = "Email sau parolă incorecte!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Autentificare</title>
    <link rel="stylesheet" href="../../public/style.css">
</head>
<body>
    <?php include '../views/partials/header.php'; ?>

    <div class="container">
        <form method="POST" action="autentificare.php">
            <h2>Autentificare</h2>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="parola">Parola:</label>
            <input type="password" id="parola" name="parola" required>
            <!-- recapcha-->
            <div class="g-recaptcha" data-sitekey="6Lc7MJ8qAAAAAEQaM63jSuBW784JhZqudNUDPf66"></div>
            
            <!-- Token CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

            <button type="submit">Autentificare</button>
            <?php if (!empty($mesaj)) { echo "<p class='error'>$mesaj</p>"; } ?>
        </form>

        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </div>

</body>
</html>
