<?php
require_once './app/controllers/CarteController.php';
require_once './app/controllers/UtilizatorController.php';
require_once './app/controllers/ImprumutController.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo "<h3>Testare CarteController</h3>";
$carteController = new CarteController();

echo "<p>Adaugare carte: ";
if ($carteController->addBook("Titlu Test", "Autor Test", "Fictiune", 2024, 1)) {
    echo "Succes!</p>";
} else {
    echo "Eroare!</p>";
}

echo "<p>Carti disponibile:</p>";
$carti = $carteController->getAllBooks();
echo "<pre>";
print_r($carti);
echo "</pre>";

echo "<p>Actualizare carte: ";
if (isset($carti[0])) {
    $primaCarte = $carti[0];
    $id_carte = $primaCarte['id_carte'];
    if ($carteController->updateBook($id_carte, "Titlu Actualizat", "Autor Actualizat", "Roman", 2025, 1)) {
        echo "Succes!</p>";
    } else {
        echo "Eroare!</p>";
    }
}

echo "<p>Stergere carte: ";
if (isset($cărți[0])) {
    if ($carteController->deleteBook($id_carte)) {
        echo "Succes!</p>";
    } else {
        echo "Eroare!</p>";
    }
}

echo "<h3>Testare UtilizatorController</h3>";
$utilizatorController = new UtilizatorController();

echo "<p>Adaugare utilizator: ";
if ($utilizatorController->addUser("Popescu", "Ion", "ion.popescu@test.com", "Standard", "parola123")) {
    echo "Succes!</p>";
} else {
    echo "Eroare!</p>";
}

echo "<p>Utilizatori Inregistrati:</p>";
$utilizatori = $utilizatorController->getAllUsers();
echo "<pre>";
print_r($utilizatori);
echo "</pre>";

echo "<h3>Testare ImprumutController</h3>";
$imprumutController = new ImprumutController();

echo "<p>Adaugare Imprumut: ";
if (isset($carti[0]) && isset($utilizatori[0])) {
    $id_carte = $carti[0]['id_carte'];
    $id_utilizator = $utilizatori[0]['id_utilizator'];
    if ($imprumutController->addImprumutTest($id_carte, $id_utilizator, date("Y-m-d"))) {
        echo "Succes!</p>";
    } else {
        echo "Eroare!</p>";
    }
}

echo "<p>Imprumuturi existente:</p>";
$imprumuturi = $imprumutController->getImprumuturi();
echo "<pre>";
print_r($imprumuturi);
echo "</pre>";
?>
