<?php
require 'pdo.php';
include 'header.php';

$stmt = $pdo->query("
    SELECT c.id, c.lieu_depart, c.lieu_arrivee, c.date_depart, c.prix, u.pseudo
    FROM covoiturages c
    JOIN utilisateurs u ON c.utilisateur_id = u.id
");
$covoiturages = $stmt->fetchAll();
?>

<h1>Liste des covoiturages</h1>
<ul>
  <?php foreach ($covoiturages as $c): ?>
    <li>
      Trajet de <?= htmlspecialchars($c['lieu_depart']) ?> à <?= htmlspecialchars($c['lieu_arrivee']) ?> -
      <?= $c['date_depart'] ?> - <?= $c['prix'] ?> €
      (Conducteur : <?= htmlspecialchars($c['pseudo']) ?>)
      <a href="index.php?id=<?= $c['id'] ?>">Détail</a>
    </li>
  <?php endforeach; ?>
</ul>
