<?php
session_start();
require_once 'pdo.php'; // Connexion PDO

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim($_POST['pseudo']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    if (empty($pseudo) || empty($email) || empty($mot_de_passe)) {
        $erreur = "Tous les champs sont obligatoires.";
    } else {
        // Vérifier si l'email est déjà utilisé
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erreur = "Cet email est déjà utilisé.";
        } else {
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (pseudo, email, mot_de_passe, credit) VALUES (?, ?, ?, 20)");
            $stmt->execute([$pseudo, $email, $hash]);
            // Après l'insertion du nouvel utilisateur
             header("Location: login.php");
            exit;

            $succes = "Compte créé avec succès. Vous pouvez maintenant vous connecter.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un compte</title>
</head>
<body>
    <h1>Créer un compte</h1>
    <?php if ($erreur): ?>
        <p style="color:red"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>
    <?php if ($succes): ?>
        <p style="color:green"><?= htmlspecialchars($succes) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Pseudo : <input type="text" name="pseudo" required></label><br><br>
        <label>Email : <input type="email" name="email" required></label><br><br>
        <label>Mot de passe : <input type="password" name="mot_de_passe" required></label><br><br>
        <button type="submit">Créer mon compte</button>
    </form>
</body>
</html>
