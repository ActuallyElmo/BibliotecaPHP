<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

require '../fpdf186/fpdf.php';



$mail = new PHPMailer(true);

/*try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'contacttinygamestudios@gmail.com'; 
    $mail->Password   = 'qbum xmzc itjd rdhl';  
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Setări email
    $mail->setFrom('contul-tau@gmail.com', 'Nume Expeditor');
    $mail->addAddress('contacttinygamestudios@gmail.com', 'Nume Destinatar');
    
    $mail->Subject = 'Subiectul mesajului';
    $mail->Body    = 'Acesta este corpul mesajului.';
    
    // Trimite emailul
    $mail->send();
    echo 'Mesaj trimis cu succes!';
} catch (Exception $e) {
    echo "Mesajul nu a fost trimis. Eroare: {$mail->ErrorInfo}";
}*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $name    = htmlspecialchars($_POST['nume']);
    $subject = htmlspecialchars($_POST['titlu']);
    $message = htmlspecialchars($_POST['mesaj']);

    if ($email && $name && $subject && $message) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'contacttinygamestudios@gmail.com';        
            $mail->Password   = 'qbum xmzc itjd rdhl';      
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom($email, $name);
            $mail->addAddress('contacttinygamestudios@gmail.com', 'Echipa Suport'); 

            $mail->Subject = $subject;
            $mail->Body    = "Nume: $name\nEmail: $email\n\nMesaj:\n$message";

            $mail->send(); // mail catre admin


            $subject = 'Confirmare';

            $mail->setFrom('contacttinygamestudios@gmail.com', $subject);
            $mail->clearAddresses();
            $mail->addAddress($email, 'Echipa Suport');

            $mail->Subject = $subject;
            $mail->Body    = "Aceasta este o confirmare al contactarii noastre! \n Vom reveni curand cu un mesaj \n\n Echipa Bibliotecasa";

            $mail->send(); // mail catre utilizator

            //echo "<p>Mesajul a fost trimis cu succes! Va vom contacta in curand.</p>";
        } catch (Exception $e) {
            //echo "<p>Eroare la trimiterea mesajului: {$mail->ErrorInfo}</p>";
            header("Location: error.php");
        }
    } else {
        //echo "<p>Toate câmpurile sunt obligatorii și trebuie completate corect.</p>";
    }
}
?>


<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Catalogul de cărți</title>
    <link rel="stylesheet" href="../../public/style.css">
</head>
<body>
    <?php include '../views/partials/header.php'; ?>
    <h1>Contactează-ne</h1>

    <form action="contact.php" method="POST">
        <label for="email">Adresă de email:</label>
        <input type="email" id="email" name="email" required>

        <label for="nume">Nume:</label>
        <input type="text" id="nume" name="nume" required>

        <label for="titlu">Titlu mesaj:</label>
        <input type="text" id="titlu" name="titlu" required>

        <label for="mesaj">Mesaj:</label>
        <textarea id="mesaj" name="mesaj" rows="6" required></textarea>

        <button type="submit">Trimite mesaj</button>
    </form>

</body>
</html>