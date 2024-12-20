<?php
session_start();
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once '../controllers/CarteController.php';
require_once '../controllers/ImprumutController.php';

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
    echo 'Sesiune de administrator';
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

        // debug
        //echo "Carte adăugată cu succes!";
        
        // redirectionare
        header("Location: catalog.php");
        exit;
    }

    if (isset($_POST['delete_carte'])) {
        $carteId = $_POST['id_carte'];
        $carteController->deleteBook($carteId);

        // Redirecționează la catalog.php după ștergere
        header("Location: catalog.php");
        exit;
    }

    if (isset($_POST['edit_carte'])) {
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

    if (isset($_POST['imprumuta_carte'])) {
        $idCarte = $_POST['id_carte'];
        $idUtilizator = $_SESSION['user_id']; 
    
        $dataImprumut = date('Y-m-d');
    
        $imprumutController->addImprumut($idCarte, $idUtilizator, $dataImprumut);
    
        $carteController->updateBookDisponibilitate($idCarte, 0);
    
        // redirectionare
        header("Location: catalog.php");
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
                <form method="POST" action="catalog.php">
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
