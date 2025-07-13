<?php
session_start();
require_once 'pdo.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$covoiturageId = $_POST['covoiturage_id'] ?? null;
if (!$covoiturageId) {
    die("Covoiturage non sélectionné.");
}

$userId = $_SESSION['id'];

try {
    // Vérifier places dispo
    $stmt = $pdo->prepare("SELECT places_disponibles FROM trajets WHERE id = ?");
    $stmt->execute([$covoiturageId]);
    $t = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$t) {
        die("Trajet introuvable.");
    }
    if ($t['places_disponibles'] <= 0) {
        die("Plus de places disponibles.");
    }

    // Insérer participation
    $stmt = $pdo->prepare("
        INSERT INTO participations (covoiturage_id, utilisateur_id)
        VALUES (?, ?)
    ");
    $stmt->execute([$covoiturageId, $userId]);

    // Décrémenter places
    $stmt = $pdo->prepare("
        UPDATE trajets 
        SET places_disponibles = places_disponibles - 1 
        WHERE id = ?
    ");
    $stmt->execute([$covoiturageId]);

    header('Location: historique.php');
    exit;
} catch (PDOException $e) {
    die("Erreur BDD : " . $e->getMessage());
}
