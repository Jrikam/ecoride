<?php
session_start();
require_once 'pdo.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['id'];
$covoiturage_id = $_POST['covoiturage_id'] ?? null;

// Vérifie que l’ID est bien reçu
if (!$covoiturage_id) {
    exit('Requête invalide : ID manquant');
}

// Utilise bien la table trajets ici (pas covoiturages)
$stmt = $pdo->prepare("SELECT * FROM trajets WHERE id = :id AND utilisateur_id = :user");
$stmt->execute([
    'id' => $covoiturage_id,
    'user' => $id
]);

$trajet = $stmt->fetch();

if ($trajet && $trajet['etat'] === 'en_attente') {
    $update = $pdo->prepare("UPDATE trajets SET etat = 'en_cours' WHERE id = :id");
    $update->execute(['id' => $covoiturage_id]);

    header('Location: historique.php');
    exit;
} else {
    exit('Requête invalide ou trajet déjà démarré/terminé.');
}
