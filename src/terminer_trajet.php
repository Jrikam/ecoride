<?php
session_start();
require_once 'pdo.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['id'];
$covoiturage_id = $_POST['covoiturage_id'] ?? 0;

// Le chauffeur uniquement peut terminer son trajet
$stmt = $pdo->prepare("SELECT * FROM trajets WHERE id = :id AND utilisateur_id = :user");
$stmt->execute(['id' => $covoiturage_id, 'user' => $id]);
$trajet = $stmt->fetch();

if ($trajet && $trajet['etat'] === 'en_cours') {
    $pdo->prepare("UPDATE trajets SET etat = 'termine' WHERE id = :id")
        ->execute(['id' => $covoiturage_id]);

    // Récupère les passagers pour notification fictive
    $stmtPassagers = $pdo->prepare("
        SELECT u.email 
        FROM participations p 
        JOIN utilisateurs u ON u.id = p.utilisateur_id 
        WHERE p.covoiturage_id = :id
    ");
    $stmtPassagers->execute(['id' => $covoiturage_id]);
    $passagers = $stmtPassagers->fetchAll();

    // Affichage confirmation
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Trajet terminé</title>
    </head>
    <body>
        <h2 style="color: green;">✅ Le trajet a bien été marqué comme terminé.</h2>
        <p>Les passagers ont été notifiés fictivement :</p>
        <ul>
            <?php foreach ($passagers as $p): ?>
                <li>✉️ Mail fictif à <strong><?= htmlspecialchars($p['email']) ?></strong></li>
            <?php endforeach; ?>
        </ul>
        <p>Redirection vers l’historique dans 5 secondes...</p>
        <script>
            setTimeout(() => {
                window.location.href = 'historique.php';
            }, 5000);
        </script>
    </body>
    </html>
    <?php
    exit;

} else {
    // Trajet introuvable ou déjà terminé
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head><meta charset="UTF-8"><title>Erreur</title></head>
    <body>
        <h2 style="color: red;">⚠️ Trajet introuvable ou déjà terminé.</h2>
        <p>Tu vas être redirigée vers l’historique dans 5 secondes...</p>
        <script>
            setTimeout(() => {
                window.location.href = 'historique.php';
            }, 5000);
        </script>
    </body>
    </html>
    <?php
    exit;
}
