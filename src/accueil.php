<?php
session_start();

if (!isset($_SESSION['utilisateur'])) {
    header('Location: testlogin.php');
    exit;
}

$email = $_SESSION['utilisateur']['email'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Accueil</title>
</head>
<body>
    <h2>Bienvenue <?= htmlspecialchars($email) ?> !</h2>
    <p>Connexion réussie.</p>
</body>
</html>
