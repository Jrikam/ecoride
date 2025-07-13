<?php
session_start();
session_destroy(); // détruit toutes les données de session
header('Location: login.php'); // redirige vers la page de connexion
exit;
?>
