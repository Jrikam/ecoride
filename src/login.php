<?php
session_start();
require_once 'pdo.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['mot_de_passe'] ?? '');

    // Récupération de l'utilisateur par email
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        if (isset($user['statut_compte']) && $user['statut_compte'] === 'suspendu') {
            $error = 'Votre compte est suspendu.';
        } else {
            $_SESSION['id']     = $user['id'];
            $_SESSION['role']   = $user['role'];
            $_SESSION['email']  = $user['email'];
            $_SESSION['credit'] = $user['credit'] ?? 0;

            // Redirection selon le rôle
            switch ($user['role']) {
                case 'administrateur':
                    header('Location: r_u.php');
                    break;
                case 'employe':
                    header('Location: employe.php');
                    break;
                default:
                    header('Location: historique.php');
                    break;
            }
            exit;
        }
    } else {
        $error = 'Email ou mot de passe incorrect.';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 300px; margin: auto; }
        label { display: block; margin-bottom: 10px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Connexion</h1>
    <form method="POST" action="login.php">
        <label>
            Email :
            <input type="email" name="email" required>
        </label>
        <label>
            Mot de passe :
            <input type="password" name="mot_de_passe" required>
        </label>
        <button type="submit">Se connecter</button>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </form>
</body>
</html>