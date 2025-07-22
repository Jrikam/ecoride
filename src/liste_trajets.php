<?php
session_start();
require_once 'pdo.php';

$query = $pdo->query("SELECT * FROM trajets");
$trajets = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Liste des trajets</title>
</head>
<body>
    <h1>Liste des trajets</h1>
    <?php foreach ($trajets as $trajet): ?>
        <div>
            <p><strong>Départ :</strong> <?= htmlspecialchars($trajet['depart']) ?></p>
            <p><strong>Arrivée :</strong> <?= htmlspecialchars($trajet['arrivee']) ?></p>
            <p><strong>Date :</strong> <?= date('d/m/Y', strtotime($trajet['date_trajet'])) ?></p>
            <p><strong>Heure :</strong> <?= date('H:i', strtotime($trajet['date_trajet'])) ?></p>
            <p><strong>Places dispo :</strong> <?= htmlspecialchars($trajet['places_disponibles']) ?></p>

            <form action="reserver.php" method="post">
                <input type="hidden" name="trajet_id" value="<?= (int)$trajet['id'] ?>">
                <button type="submit">Réserver</button>
            </form>
        </div>
        <hr>
    <?php endforeach; ?>
</body>
</html>
