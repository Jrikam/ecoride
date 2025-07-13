<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = new PDO('mysql:host=db;dbname=ecoride;charset=utf8', 'ecoride_user', 'ecoride_pass');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user_id = $_SESSION['utilisateur_id'];
$covoiturage_id = isset($_POST['covoiturage_id']) ? intval($_POST['covoiturage_id']) : 0;

// Récupérer le covoiturage
$stmt = $pdo->prepare("SELECT * FROM covoiturages WHERE id = ?");
$stmt->execute([$covoiturage_id]);
$covoiturage = $stmt->fetch();

if (!$covoiturage) {
    die("Covoiturage introuvable");
}

if ($covoiturage['places_disponibles'] <= 0) {
    die("Aucune place disponible");
}

if ($_SESSION['credit'] < $covoiturage['prix']) {
    die("Crédit insuffisant");
}

// Vérifier s’il a déjà participé
$stmt = $pdo->prepare("SELECT COUNT(*) FROM participations WHERE utilisateur_id = ? AND covoiturage_id = ?");
$stmt->execute([$user_id, $covoiturage_id]);
if ($stmt->fetchColumn() > 0) {
    die("Vous participez déjà à ce covoiturage");
}

// Insérer la participation
$stmt = $pdo->prepare("INSERT INTO participations (utilisateur_id, covoiturage_id) VALUES (?, ?)");
$stmt->execute([$user_id, $covoiturage_id]);

// Décrémenter crédits et places
$stmt = $pdo->prepare("UPDATE utilisateurs SET credit = credit - ? WHERE id = ?");
$stmt->execute([$covoiturage['prix'], $user_id]);

$stmt = $pdo->prepare("UPDATE covoiturages SET places_disponibles = places_disponibles - 1 WHERE id = ?");
$stmt->execute([$covoiturage_id]);

// Mettre à jour la session crédit
$_SESSION['credit'] -= $covoiturage['prix'];

header("Location: espace_utilisateur.php?message=Participation confirmée");
exit;
?>
