<?php
session_start();
require_once 'pdo.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['id'];

// On récupère le covoiturage_id soit par GET, soit par POST
$covoiturage_id = $_POST['covoiturage_id'] ?? ($_GET['covoiturage_id'] ?? null);

// Si aucun trajet n’est fourni, on affiche un formulaire de sélection
if (!$covoiturage_id && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    // On récupère les trajets auxquels l'utilisateur a participé
    $stmt = $pdo->prepare("
        SELECT t.id, t.depart, t.arrivee, t.date_trajet
        FROM trajets t
        JOIN participations p ON t.id = p.covoiturage_id
        WHERE p.utilisateur_id = ?
        ORDER BY t.date_trajet DESC
    ");
    $stmt->execute([$userId]);
    $trajets = $stmt->fetchAll();
}
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = isset($_POST['note']) ? (int)$_POST['note'] : null;
    $commentaire = $_POST['commentaire'] ?? '';

    if ($covoiturage_id && $note >= 1 && $note <= 5) {
        $stmt = $pdo->prepare("INSERT INTO avis (covoiturage_id, utilisateur_id, note, commentaire) VALUES (?, ?, ?, ?)");
        $stmt->execute([$covoiturage_id, $userId, $note, $commentaire]);
        $_SESSION['message'] = "Avis envoyé et en attente de validation.";
        header('Location: historique.php');
        exit;
    } else {
        $error = "Note invalide ou covoiturage manquant.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Déposer un avis</title>
</head>
<body>
    <h1>Déposer un avis</h1>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if (!$covoiturage_id && !empty($trajets)): ?>
        <form method="GET" action="deposer_avis.php">
            <label for="covoiturage_id">Sélectionner un trajet :</label>
            <select name="covoiturage_id" id="covoiturage_id" required>
                <?php foreach ($trajets as $t): ?>
                    <option value="<?= $t['id'] ?>">
                        <?= htmlspecialchars($t['depart']) ?> → <?= htmlspecialchars($t['arrivee']) ?> (<?= $t['date_trajet'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Continuer</button>
        </form>

    <?php elseif ($covoiturage_id): ?>
        <form method="POST">
            <input type="hidden" name="covoiturage_id" value="<?= (int)$covoiturage_id ?>">

            <label for="note">Note (1 à 5) :</label>
            <select name="note" id="note" required>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select><br><br>

            <label for="commentaire">Commentaire :</label><br>
            <textarea name="commentaire" id="commentaire" rows="5" cols="40"></textarea><br><br>

            <button type="submit">Envoyer l'avis</button>
        </form>

    <?php else: ?>
        <p style="color:red;">Aucun trajet disponible pour déposer un avis.</p>
        <a href="historique.php">Retour</a>
    <?php endif; ?>
</body>
</html>
