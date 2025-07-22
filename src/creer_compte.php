<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'pdo.php'; // adapte le chemin si besoin

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim($_POST['pseudo'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if ($pseudo === '' || $email === '' || $mot_de_passe === '') {
        $message = "❌ Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ L'email n'est pas valide.";
    } else {
        try {
            // Vérifier si email ou pseudo existe déjà
            $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ? OR pseudo = ?");
            $stmt->execute([$email, $pseudo]);
            if ($stmt->fetch()) {
                $message = "⚠️ Un compte avec ce pseudo ou email existe déjà.";
            } else {
                $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO utilisateurs (pseudo, email, mot_de_passe, credits, role) VALUES (?, ?, ?, 20, 'passager')");
                $stmt->execute([$pseudo, $email, $hash]);

                // Redirection immédiate sans message (ne pas faire echo avant header)
                header("Location: login.php");
                exit;
            }
        } catch (PDOException $e) {
            $message = "Erreur SQL : " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Créer un compte</title>
</head>
<body>
    <h1>Créer un compte</h1>

    <?php if ($message): ?>
        <p style="color:red;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="pseudo">Pseudo :</label><br>
        <input type="text" name="pseudo" id="pseudo" required value="<?= htmlspecialchars($_POST['pseudo'] ?? '') ?>"><br><br>

        <label for="email">Email :</label><br>
        <input type="email" name="email" id="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"><br><br>

        <label for="mot_de_passe">Mot de passe :</label><br>
        <input type="password" name="mot_de_passe" id="mot_de_passe" required><br><br>

        <button type="submit">Créer mon compte</button>
    </form>
</body>
</html>
