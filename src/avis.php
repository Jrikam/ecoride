<?php
session_start();
require_once 'pdo.php';

$stmt = $pdo->query("
    SELECT a.note, a.commentaire, u.pseudo, a.date_avis
    FROM avis a
    JOIN utilisateurs u ON a.utilisateur_id = u.id
    WHERE a.statut IN ('valid', 'en attente')
    ORDER BY a.date_avis DESC
");
$avisValidés = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8" /><title>Avis des passagers</title></head>
<body>
<h1>Avis des passagers validés</h1>

<?php if (empty($avisValidés)): ?>
    <p>Aucun avis disponible.</p>
<?php else: ?>
    <ul>
        <?php foreach ($avisValidés as $avis): ?>
            <li>
                <strong><?= htmlspecialchars($avis['pseudo']) ?></strong> (<?= htmlspecialchars($avis['date_avis']) ?>) : 
                Note <?= $avis['note'] ?>/5 <br>
                <?= nl2br(htmlspecialchars($avis['commentaire'])) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

</body>
</html>