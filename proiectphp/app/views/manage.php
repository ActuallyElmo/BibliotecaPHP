<?php
session_start();

$isAdmin = isset($_SESSION['status_membru']) && $_SESSION['status_membru'] === 'Admin';

if ($isAdmin === false) {
    header("Location: catalog.php");
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
</body>
</html>