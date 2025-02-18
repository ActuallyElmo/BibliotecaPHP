<?php
session_start();
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once '../controllers/CarteController.php';
require_once '../controllers/ImprumutController.php';

$imprumutController = new ImprumutController();
$carteController = new CarteController();
$idUtilizator = $_SESSION['user_id']; // ID-ul utilizatorului curent
$isLogged = false;

$imprumuturi = $imprumutController->getImprumuturiByUser($idUtilizator);

if(isset($_SESSION['user_id'])) {
    $isLogged = true;
}
else {
    header("Location: catalog.php");
}

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Token unic
    }
    return $_SESSION['csrf_token'];
}

$csrfToken = generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['returneaza_carte'])) {
    
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "Cererea a fost invalidata din motive de securitate.";
        exit();
    }
    if($isLogged === false){
        die("Sesiune invalida, autentificare necesara");
    }
    
    $idImprumut = $_POST['id_imprumut'];
    $idCarte = $_POST['id_carte'];

    $carteController->updateBookDisponibilitate($idCarte, 1);
    $imprumutController->markAsReturned($idImprumut);
    // redirectionare
    header("Location: imprumuturi.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Împrumuturile mele</title>
    <link rel="stylesheet" href="../../public/style.css">
    <style>
        .imprumuturi-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .imprumut-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
            width: 250px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .imprumut-card h3 {
            font-size: 1.2em;
            margin: 10px 0;
        }
        .imprumut-card p {
            margin: 5px 0;
            font-size: 0.9em;
        }
        .imprumut-card button {
            padding: 5px 15px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #4CAF50; 
            color: white;
            transition: background-color 0.3s ease;
            display: block; 
            width: 100%; 
        }
        .imprumut-card button:hover {
            background-color: #45a049; 
        }
        .imprumut-card .returnat {
            color: #999; 
            font-size: 14px;
            font-weight: bold;
        }
        form.return-button {
            margin: 0;
            padding: 0;
            display: block;
        }
    </style>
</head>
<body>
    <?php include '../views/partials/header.php'; ?>

    <h1>Împrumuturile mele</h1>
    <div class="imprumuturi-container">
        <?php foreach ($imprumuturi as $imprumut): ?>
            <div class="imprumut-card">
                <h3><?= htmlspecialchars($imprumut['titlu']) ?></h3>
                <p><strong>Autor:</strong> <?= htmlspecialchars($imprumut['autor']) ?></p>

                <p><strong>Data împrumut:</strong> <?= htmlspecialchars($imprumut['data_imprumut']) ?></p>
                
                <?php if ($imprumut['data_returnare'] !== NULL): ?>
                    <p class="returnat">Returnat</p>
                <?php else: ?>
                    <form method="POST" action="imprumuturi.php" class="return-button">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">        
                        <input type="hidden" name="id_imprumut" value="<?= htmlspecialchars($imprumut['id_imprumut']) ?>"> 
                        <input type="hidden" name="id_carte" value="<?= htmlspecialchars($imprumut['id_carte']) ?>"> 
                        <button type="submit" name="returneaza_carte">Returnează</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    </div>
</body>
</html>