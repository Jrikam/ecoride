<?php
session_start();

$utilisateurs = [
    ['email' => 'evamiolard@gmail.com', 'password' => '$2y$10$.GWVD2KaxO/BzBE/qcfbjOyYjuATfwHbk/caiyvcLoMGsHx137aAW'], 
    ['email' => 'jaaderikam@gmail.com', 'password' => '$2y$10$Snx1SGuoYexlXenuGU.As.8u.nO21AcEPv4Ja7Gy/QXq0Z5H.meyq'],
    ['email' => 'monadmin@test.com', 'password' => '$2y$10$aohPSr/X.5Zf1WnP2jUSy.YsqP9l9NfnSngMQ7CRFhOHsyfXpJFn.']
];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $trouve = false;
    foreach ($utilisateurs as $user) {
        if ($user['email'] === $email && $user['password'] === $password) {
            $_SESSION['utilisateur'] = ['email' => $email];
            header('Location: accueil.php');
            exit;
        }
    }
    $message = "Email ou mot de passe incorrect.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Test Login</title>
</head>
<body>
    <h1>Test Login</h1>
    <?php if ($message): ?>
        <p style="color:red;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <label>Email : </label>
        <input type="email" name="email" required><br><br>
        <label>Mot de passe : </label>
        <input type="password" name="password" required><br><br>
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
