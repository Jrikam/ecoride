<?php
session_start();
require_once 'pdo.php';

$id_user = $_SESSION['id'];
$covoiturage_id = $_POST['covoiturage_id'];
$valider = $_POST['valider'] ?? 1;
$note = $_POST['note'] ?? null;
$commentaire = trim($_POST['commentaire'] ?? '');

$stmt = $pdo->prepare("INSERT INTO validations (utilisateur_id, covoiturage_id, note, commentaire, valider) 
                       VALUES (:uid, :cid, :note, :com, :val)");
$stmt->execute([
    'uid' => $id_user,
    'cid' => $covoiturage_id,
    'note' => $note,
    'com' => $commentaire,
    'val' => $valider
]);

// Vérifier si tous ont validé
$stmt = $pdo->prepare("SELECT COUNT(*) FROM participations WHERE covoiturage_id = :id");
$stmt->execute(['id' => $covoiturage_id]);
$total = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM validations WHERE covoiturage_id = :id AND valider = 1");
$stmt->execute(['id' => $covoiturage_id]);
$valides = $stmt->fetchColumn();

if ($valides == $total) {
    // Tous ont validé, créditer le chauffeur
    $stmt = $pdo->prepare("SELECT user_id, prix FROM covoiturages WHERE id = :id");
    $stmt->execute(['id' => $covoiturage_id]);
    $trajet = $stmt->fetch();

    $nb_passagers = $total;
    $gain = $trajet['prix'] * $nb_passagers;

    $pdo->prepare("UPDATE utilisateurs SET credit = credit + :gain WHERE id = :uid")
        ->execute(['gain' => $gain, 'uid' => $trajet['user_id']]);
}

header('Location: historique.php');
exit;
