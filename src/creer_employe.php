<?php
session_start();
require_once 'pdo.php';

// Vérification rôle admin
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}
$id = $_SESSION['id'];
$stmt = $pdo->prepare("SELECT role FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user || $user['role'] !== 'administrateur') {
    die("Accès refusé.");
}

// Traitement formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = $_POST['pseudo'] ?? '';
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if ($pseudo && $email && $mot_de_passe) {
        // Hash du mot de passe
        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO utilisateurs (pseudo, email, mot_de_passe, role, statut_compte) VALUES (?, ?, ?, 'employe', 'actif')");
        $stmt->execute([$pseudo, $email, $hash]);

        $_SESSION['message'] = "Compte employé créé avec succès.";
        header('Location: admin.php');
        exit;
    } else {
        $error = "Tous les champs sont requis.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8" /><title>Création employé</title></head>
<body>
<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<p><a href="r_u.php">Retour</a></p>
</body>
</html>
