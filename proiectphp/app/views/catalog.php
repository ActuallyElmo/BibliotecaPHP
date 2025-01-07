<?php
session_start();
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once '../controllers/CarteController.php';
require_once '../controllers/ImprumutController.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
//require '../fpdf/fpdf.php';
function trimiteEmailImprumut($emailUtilizator, $numeUtilizator, $detaliiCarte) {
    $mail = new PHPMailer(true);

    try {
        // Configurare SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'contacttinygamestudios@gmail.com';        
        $mail->Password   = 'qbum xmzc itjd rdhl';      
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('contacttinygamestudios@gmail.com', 'Bibliotecasa');
        $mail->addAddress($emailUtilizator, $numeUtilizator); 
        $mail->addAddress('contacttinygamestudios@gmail.com', 'Administrator'); 

        $mail->isHTML(true);
        $mail->Subject = 'Confirmare imprumut carte';
        $mail->Body = "<h3>Detalii imprumut</h3>
                       <p>Utilizator: <strong>{$numeUtilizator}</strong></p>
                       <p>Carte: <strong>{$detaliiCarte}</strong></p>
                       <p>Data imprumutului: <strong>" . date('Y-m-d') . "</strong></p>";

        $mail->send();
    } catch (Exception $e) {
        echo "Eroare la trimiterea emailului: {$mail->ErrorInfo}";
    }
}

$carteController = new CarteController();
$imprumutController = new ImprumutController();

$gen = $_GET['gen'] ?? null;
$an = $_GET['an'] ?? null;
$disponibilitate = $_GET['disponibilitate'] ?? null;
$disponibilitateNoua = 1;

$carti = $carteController->getCarti($gen, $an, $disponibilitate);

$isAdmin = isset($_SESSION['status_membru']) && $_SESSION['status_membru'] === 'Admin';
$isLogged = false;

if(isset($_SESSION['user_id'])) {
    $isLogged = true;
}

if ($isAdmin) {
    //echo 'Sesiune de administrator';
}

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Token unic
    }
    return $_SESSION['csrf_token'];
}

$csrfToken = generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "Cererea a fost invalidata din motive de securitate.";
        exit();
    }
    if($isLogged === false){
        die("Sesiune invalida, autentificare necesara");
    }
    if (isset($_POST['add_carte'])) {
        if ($isAdmin === true){

            $idCarte = $_POST['id_carte'];
            $titlu = $_POST['titlu'];
            $autor = $_POST['autor'];
            $genNou = $_POST['gen'];
            $anNou = $_POST['an'];
            $disponibilitateNoua = (int)$_POST['disponibilitate'];

            if (!is_numeric($anNou)) {
                echo "Anul introdus nu este valid.";
                exit;
            }

            // debug
            //var_dump($titlu, $autor, $genNou, $anNou, $disponibilitateNoua);
            
            $carteController->addBook($titlu, $autor, $genNou, $anNou, $disponibilitateNoua);

            
            // redirectionare
            header("Location: catalog.php");
            exit;
        }
    }

    if (isset($_POST['delete_carte'])) {
        if ($isAdmin === true){

            $carteId = $_POST['id_carte'];
            $carteController->deleteBook($carteId);

            // Redirecționează la catalog.php după ștergere
            header("Location: catalog.php");
            exit;
        }
    }

    if (isset($_POST['edit_carte'])) {
        if ($isAdmin === true){

            $idCarte = $_POST['id_carte'];
            $titlu = $_POST['titlu'];
            $autor = $_POST['autor'];
            $genNou = $_POST['gen'];
            $anNou = $_POST['an'];
            $disponibilitateNou = (int)$_POST['disponibilitate'];

            if (!is_numeric($anNou)) {
                echo "Anul introdus nu este valid.";
                exit;
            }

            $carteController->updateBook($idCarte, $titlu, $autor, $genNou, $anNou, $disponibilitateNou);

            // redirectionare
            header("Location: catalog.php");
            exit;
        }
    }

    if (isset($_POST['imprumuta_carte'])) {
        $idCarte = $_POST['id_carte'];
        $idUtilizator = $_SESSION['user_id']; 
        
        $dataImprumut = date('Y-m-d');
        
        $carteController->imprumutaCarte($idCarte, $idUtilizator);
    
        $carte = $carteController->getCarteById($idCarte); 
        $detaliiCarte = "Titlu: {$carte['titlu']},\nAutor: {$carte['autor']}}";
    
        $emailUtilizator = $_SESSION['email'];
        $numeUtilizator = $_SESSION['user_first_name'];
    
        trimiteEmailImprumut($emailUtilizator, $numeUtilizator, $detaliiCarte);
    
        header("Location: catalog.php");
        exit;
    }

    //echo "fisierul incarcat nu este un fisier JSON valid";
    if(isset($_FILES["fisier_json"])){
        if ($isAdmin === true){
            $fisier = $_FILES['fisier_json'];

            if ($fisier['type'] === 'application/json') {
                $carteController->importaCartiDinJSON($fisier['tmp_name']);

                echo "importul cartilor a fost realizat cu succes";
            } else {
                echo "fisierul incarcat nu este un fisier valid";
            }

            header("Location: catalog.php");
            exit;
        }
    }

    if (isset($_POST['export_excel'])) {
        $carti = $carteController->getAllBooks();
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=catalog.xlsx");
        echo "Titlu\tAutor\tGen\tAn\tDisponibilitate\n";
        foreach ($carti as $carte) {
            echo "{$carte['titlu']}\t{$carte['autor']}\t{$carte['gen']}\t{$carte['an']}\t";
            echo ($carte['disponibilitate'] ? 'Disponibil' : 'Împrumutat') . "\n";
        }
        exit;
    }
    
    if (isset($_POST['export_csv'])) {
        $carti = $carteController->getAllBooks();
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=catalog.csv");
        $output = fopen("php://output", "w");
        fputcsv($output, ['Titlu', 'Autor', 'Gen', 'An', 'Disponibilitate']);
        foreach ($carti as $carte) {
            fputcsv($output, [
                $carte['titlu'], 
                $carte['autor'], 
                $carte['gen'], 
                $carte['an'], 
                $carte['disponibilitate'] ? 'Disponibil' : 'Împrumutat'
            ]);
        }
        fclose($output);
        exit;
    }
    
    if (isset($_POST['export_pdf'])) {

        $carti = $carteController->getAllBooks();

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);

        $pdf->Cell(0, 10, 'Catalogul de carti', 0, 1, 'C');

        $pdf->Cell(50, 10, 'Titlu', 1);
        $pdf->Cell(40, 10, 'Autor', 1);
        $pdf->Cell(30, 10, 'Gen', 1);
        $pdf->Cell(20, 10, 'An', 1);
        $pdf->Cell(40, 10, 'Disponibilitate', 1);
        $pdf->Ln();

        foreach ($carti as $carte) {
            $pdf->Cell(50, 10, utf8_decode($carte['titlu']), 1);
            $pdf->Cell(40, 10, utf8_decode($carte['autor']), 1);
            $pdf->Cell(30, 10, utf8_decode($carte['gen']), 1);
            $pdf->Cell(20, 10, $carte['an'], 1);
            $pdf->Cell(40, 10, $carte['disponibilitate'] ? 'Disponibil' : 'Împrumutat', 1);
            $pdf->Ln();
        }

        $pdf->Output('D', 'catalog.pdf');
        exit;
    }
    
}



?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Catalogul de cărți</title>
    <link rel="stylesheet" href="../../public/style.css">
    <style>
        .filters {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin: 20px 0;
            padding: 10px;
            background-color: #f4f4f4;
            border-radius: 5px;
        }
        .filters label {
            margin-right: 10px;
        }
        .filters select, .filters button {
            margin: 5px 0;
            padding: 5px;
            font-size: 16px;
        }
        .carti-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .carte-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
            width: 200px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .carte-card img {
            width: 100%;
            height: auto;
            margin-bottom: 10px;
        }
        .carte-card h3 {
            font-size: 1.2em;
            margin: 10px 0;
        }
        .carte-card p {
            margin: 5px 0;
            font-size: 0.9em;
        }
        .carte-card button {
            padding: 5px 15px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            transition: background-color 0.3s ease;
        }

        .carte-card button:hover {
            background-color: #45a049;
        }

        .carte-card button[disabled] {
            background-color: #ddd;
            cursor: not-allowed;
        }

        form .delete-button { 
            padding: 3px 10px;
            font-size: 14px; 
            background-color: #f44336 !important; 
            border: none;
            outline: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: inline-block;
        }

        form .delete-button:hover {
            background-color: #d32f2f !important; 
        }

        form .delete-button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .adauga-carte {
            margin-top: 20px;
            text-align: center;
        }

        .adauga-carte form {
            display: inline-block;
            text-align: left;
        }

        .adauga-carte form label,
        .adauga-carte form input,
        .adauga-carte form select {
            display: block;
            margin-bottom: 10px;
        }

        form.admin-actions {
            display: inline-block;
            margin: 0;
            padding: 0;
            border: none; 
            margin-top: 10px; 
        }

        form.admin-actions button.delete-button {
            padding: 10px 30px; 
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 16px;
            width: 100%; 
        }

        form.admin-actions button.delete-button:hover {
            background-color: darkred; 
        }

        form.admin-actions button.delete-button:focus {
            outline: none; 
        }
        
    </style>
</head>
<body>
    <?php include '../views/partials/header.php'; ?>

   <div class="filters">
        <?php if ($isAdmin): ?>
            <div class="admin-section">
                <div class="export-import-buttons">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"> 
                        <button type="submit" name="export_excel">Exporta in Excel</button>
                        <button type="submit" name="export_pdf">Exporta in PDF</button>
                        <button type="submit" name="export_csv">Exporta in CSV</button>
                    </form>

                    <form method="POST" action="import.php" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"> 
                        <input type="file" name="import_file" accept=".csv, .xlsx, .xls" required>
                        <button type="submit" name="import_catalog">Importă catalog</button>
                    </form>
                </div>

                <?php if (isset($_GET['import']) && $_GET['import'] == 'success'): ?>
                    <p style="color: green; text-align: center;">Importul a fost realizat cu succes!</p>
                <?php endif; ?>

                <form action="catalog.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"> 
                    <label for="fisier_json">Adauga carti printr-un fisier JSON:</label>
                    <input type="file" name="fisier_json" id="fisier_json" accept=".json" required>
                    <button type="submit">Importa</button>
                </form>

                <form method="POST" action="catalog.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"> 
                    <h2>Adaugă o carte nouă</h2>
                    <input type="text" name="titlu" placeholder="Titlu" required>
                    <input type="text" name="autor" placeholder="Autor" required>
                    <select name="gen" required>
                        <option value="Fictiune">Ficțiune</option>
                        <option value="Non-Fictiune">Non-Ficțiune</option>
                        <option value="Roman">Roman</option>
                        <option value="Poezie">Poezie</option>
                        <option value="Tehnic">Tehnic</option>
                    </select>
                    <input type="number" name="an" placeholder="Anul" required>
                    <select name="disponibilitate">
                        <option value="1" <?= $disponibilitateNoua == 1 ? 'selected' : '' ?>>Disponibil</option>
                        <option value="0" <?= $disponibilitateNoua == 0 ? 'selected' : '' ?>>Indisponibil</option>
                    </select>
                    <button type="submit" name="add_carte">Adaugă</button>
                </form>
            </div>
        <?php endif; ?>

            <form method="GET">
                <label for="gen">Gen:</label>
                <select id="gen" name="gen">
                    <option value="">Toate</option>
                    <option value="Fictiune" <?= $gen === 'Ficțiune' ? 'selected' : '' ?>>Ficțiune</option>
                    <option value="Non-Fictiune" <?= $gen === 'Non-Ficțiune' ? 'selected' : '' ?>>Non-Ficțiune</option>
                </select>

                <label for="an">An:</label>
                <select id="an" name="an">
                    <option value="">Toți</option>
                    <?php for ($i = date('Y'); $i >= 1900; $i--): ?>
                        <option value="<?= $i ?>" <?= $an == $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>

                <label for="disponibilitate">Disponibilitate:</label>
                <select id="disponibilitate" name="disponibilitate">
                    <option value="">Toate</option>
                    <option value="1" <?= $disponibilitate === '1' ? 'selected' : '' ?>>Disponibil</option>
                    <option value="0" <?= $disponibilitate === '0' ? 'selected' : '' ?>>Indisponibil</option>
                </select>

                <button type="submit">Aplică filtre</button>
            </form>
        </div>

        <div class="carti-container">
            <?php foreach ($carti as $carte): ?>
                <div class="carte-card">
                    <img src="../../public/bookImg.png" alt="Carte">
                    <h3><?= htmlspecialchars($carte['titlu']) ?></h3>
                    
                    <?php if ($isAdmin === false): ?>
                        <p><strong>Autor:</strong> <?= htmlspecialchars($carte['autor']) ?></p>
                        <p><strong>Gen:</strong> <?= htmlspecialchars($carte['gen']) ?></p>
                        <p><strong>An:</strong> <?= htmlspecialchars($carte['an']) ?></p>
                        <p><strong>Disponibilitate:</strong> <?= $carte['disponibilitate'] ? 'Disponibil' : 'Împrumutat' ?></p>
                    <?php endif; ?>

                    <form method="POST" action="catalog.php" class="admin-actions">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">    
                    <input type="hidden" name="id_carte" value="<?= $carte['id_carte'] ?>">
                        <?php if ($isLogged === true): ?>
                            <button type="submit" name="imprumuta_carte" <?= $carte['disponibilitate'] ? '' : 'disabled' ?>>
                                <?= $carte['disponibilitate'] ? 'Împrumută' : 'Indisponibil' ?>
                            </button>
                        <?php endif; ?>
                    </form>

                    <?php if ($isAdmin): ?>
                        <form method="POST" action="catalog.php" class="admin-actions">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">    
                        <input type="hidden" name="id_carte" value="<?= $carte['id_carte'] ?>">
                            <button type="submit" name="delete_carte" class="delete-button">Șterge</button>
                        </form>

                        <form method="POST" action="catalog.php" class="admin-actions">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">    
                            <input type="hidden" name="id_carte" value="<?= $carte['id_carte'] ?>">
                            <input type="text" name="titlu" value="<?= htmlspecialchars($carte['titlu']) ?>" required>
                            <input type="text" name="autor" value="<?= htmlspecialchars($carte['autor']) ?>" required>
                            <select name="gen" required>
                                <option value="Fictiune" <?= $carte['gen'] === 'Ficțiune' ? 'selected' : '' ?>>Ficțiune</option>
                                <option value="Non-Fictiune" <?= $carte['gen'] === 'Non-Ficțiune' ? 'selected' : '' ?>>Non-Ficțiune</option>
                                <option value="Roman" <?= $carte['gen'] === 'Roman' ? 'selected' : '' ?>>Roman</option>
                                <option value="Poezie" <?= $carte['gen'] === 'Poezie' ? 'selected' : '' ?>>Poezie</option>
                                <option value="Tehnic" <?= $carte['gen'] === 'Tehnic' ? 'selected' : '' ?>>Tehnic</option>
                            </select>
                            <input type="number" name="an" value="<?= htmlspecialchars($carte['an']) ?>" required>
                            <select name="disponibilitate">
                                <option value="1" <?= $carte['disponibilitate'] == 1 ? 'selected' : '' ?>>Disponibil</option>
                                <option value="0" <?= $carte['disponibilitate'] == 0 ? 'selected' : '' ?>>Indisponibil</option>
                            </select>
                            <button type="submit" name="edit_carte" class="edit-button">Editează</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</body>
</html>
