<?php
session_start();
require_once 'pdo.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['id'];
$trajet_id = $_POST['trajet_id'] ?? $_GET['trajet_id'] ?? null;

if (!$trajet_id || !is_numeric($trajet_id)) {
    die("❌ Erreur : ID du trajet non valide.");
}

$trajetId = (int)$trajet_id;

try {
    // Vérifie que le trajet existe et a des places disponibles
    $stmt = $pdo->prepare("SELECT places_disponibles FROM trajets WHERE id = ?");
    $stmt->execute([$trajetId]);
    $trajet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$trajet) {
        die("❌ Erreur : ce trajet est introuvable.");
    }

    if ((int)$trajet['places_disponibles'] <= 0) {
        die("❌ Erreur : il n'y a plus de places disponibles.");
    }

    // Vérifie si l'utilisateur a déjà réservé ce trajet
    $stmt = $pdo->prepare("SELECT id FROM reservations WHERE trajet_id = ? AND utilisateur_id = ?");
    $stmt->execute([$trajetId, $userId]);
    if ($stmt->fetch()) {
        die("❌ Vous avez déjà réservé ce trajet.");
    }

    // Insère la réservation
    $stmt = $pdo->prepare("INSERT INTO reservations (trajet_id, utilisateur_id) VALUES (?, ?)");
    $stmt->execute([$trajetId, $userId]);

    // Décrémente les places disponibles
    $stmt = $pdo->prepare("UPDATE trajets SET places_disponibles = places_disponibles - 1 WHERE id = ?");
    $stmt->execute([$trajetId]);

    echo "✅ Réservation effectuée avec succès !<br>";
    echo "<a href='liste_trajets.php'>🔙 Retour à la liste des trajets</a>";

} catch (PDOException $e) {
    die("❌ Erreur de base de données : " . $e->getMessage());
}
