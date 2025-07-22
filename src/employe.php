<?php
session_start();
require_once 'pdo.php';

// Vérification connexion + rôle employé
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['id'];
$stmt = $pdo->prepare("SELECT role FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'employe') {
    die("Accès refusé.");
}

// Gestion des actions POST (valider/refuser avis)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    $avis_id = $_POST['avis_id'] ?? null;

    if ($action && $avis_id) {
        if (in_array($action, ['valider', 'refuser'])) {
            // Valeurs exactes correspondant à la colonne ENUM en base
            $nouveau_statut = $action === 'valider' ? 'valid' : 'refus';

            $stmtUpdate = $pdo->prepare("UPDATE avis SET statut = ? WHERE id = ?");
            $stmtUpdate->execute([$nouveau_statut, $avis_id]);

            // Message plus lisible pour l'utilisateur avec accents
            $_SESSION['message'] = "Avis #$avis_id " . ($action === 'valider' ? 'validé' : 'refusé') . " avec succès.";
            header('Location: employe.php');
            exit;
        }
    }
}


// Récupération des avis en attente
$stmtAvis = $pdo->query("
    SELECT a.id, a.covoiturage_id, a.note, a.commentaire, u.pseudo AS passager
    FROM avis a
    JOIN utilisateurs u ON a.utilisateur_id = u.id
    WHERE a.statut = 'en_attente'
    ORDER BY a.date_avis DESC
");
$avisEnAttente = $stmtAvis->fetchAll(PDO::FETCH_ASSOC);

// Récupération des trajets signalés
$stmtSignales = $pdo->query("
    SELECT t.id AS trajet_id, t.depart, t.arrivee, t.date_trajet,
           c.pseudo AS chauffeur_pseudo, c.email AS chauffeur_email,
           p.pseudo AS passager_pseudo, p.email AS passager_email
    FROM trajets t
    JOIN utilisateurs c ON t.utilisateur_id = c.id
    JOIN participations pa ON pa.covoiturage_id = t.id
    JOIN utilisateurs p ON pa.utilisateur_id = p.id
    WHERE t.signale = 1
    ORDER BY t.date_trajet DESC
");
$trajetsSignales = $stmtSignales->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Espace Employé</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 30px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #eee; }
        button { padding: 5px 10px; margin-right: 5px; }
        .message { padding: 10px; margin-bottom: 20px; background-color: #dff0d8; color: #3c763d; border-radius: 4px; }
    </style>
</head>
<body>
    <nav>
  <a href="deposer_avis.php">Déposer un avis</a> |
  <a href="avis.php">Signalements</a> |
  <a href="historique.php">Retour à l'historique</a>
</nav>

<h1>Espace Employé</h1>

<?php if (isset($_SESSION['message'])): ?>
    <div class="message"><?= htmlspecialchars($_SESSION['message']) ?></div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<h2>Avis en attente de validation</h2>

<?php if (empty($avisEnAttente)): ?>
    <p>Aucun avis en attente.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Covoiturage</th>
                <th>Passager</th>
                <th>Note</th>
                <th>Commentaire</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($avisEnAttente as $avis): ?>
                <tr>
                    <td><?= $avis['id'] ?></td>
                    <td><?= $avis['covoiturage_id'] ?></td>
                    <td><?= htmlspecialchars($avis['passager']) ?></td>
                    <td><?= $avis['note'] ?>/5</td>
                    <td><?= nl2br(htmlspecialchars($avis['commentaire'])) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="avis_id" value="<?= $avis['id'] ?>" />
                            <button type="submit" name="action" value="valider">Valider</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="avis_id" value="<?= $avis['id'] ?>" />
                            <button type="submit" name="action" value="refuser">Refuser</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<h2>Covoiturages signalés comme mal passés</h2>

<?php if (empty($trajetsSignales)): ?>
    <p>Aucun covoiturage signalé.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID trajet</th>
                <th>Départ</th>
                <th>Arrivée</th>
                <th>Date</th>
                <th>Chauffeur (Pseudo / Mail)</th>
                <th>Passager (Pseudo / Mail)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trajetsSignales as $t): ?>
                <tr>
                    <td><?= $t['trajet_id'] ?></td>
                    <td><?= htmlspecialchars($t['depart']) ?></td>
                    <td><?= htmlspecialchars($t['arrivee']) ?></td>
                    <td><?= htmlspecialchars($t['date_trajet']) ?></td>
                    <td><?= htmlspecialchars($t['chauffeur_pseudo']) ?> / <?= htmlspecialchars($t['chauffeur_email']) ?></td>
                    <td><?= htmlspecialchars($t['passager_pseudo']) ?> / <?= htmlspecialchars($t['passager_email']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>