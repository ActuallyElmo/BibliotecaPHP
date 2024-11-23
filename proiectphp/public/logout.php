<?php
session_start();
session_destroy(); // inchidem sesiunea
header('Location: index.php'); 
exit();
?>