<?php
session_start();
require_once 'pdo.php'; // Connexion PDO

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_vehicule'])) {
    $marque = trim($_POST['marque'] ?? '');
    $modele = trim($_POST['modele'] ?? '');
    $immatriculation = trim($_POST['immatriculation'] ?? '');

    if (!$marque || !$modele || !$immatriculation) {
        $error = "Tous les champs sont obligatoires.";
    } else {
        $sql = "INSERT INTO vehicules (utilisateur_id, marque, modele, immatriculation, places_disponibles)
        VALUES (:utilisateur_id, :marque, :modele, :immatriculation, :places_disponibles)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':utilisateur_id' => $userId,
    ':marque' => $marque,
    ':modele' => $modele,
    ':immatriculation' => $immatriculation,
    ':places_disponibles' => 4 // par exemple
]);

        header('Location: creer_voyage.php'); // Redirection après ajout
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un véhicule</title>
</head>
<body>
    <h1>Ajouter un véhicule</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Marque : <input type="text" name="marque" required></label><br>
        <label>Modèle : <input type="text" name="modele" required></label><br>
        <label>Immatriculation : <input type="text" name="immatriculation" required></label><br>
        <button type="submit" name="submit_vehicule">Ajouter le véhicule</button>
    </form>
</body>
</html>