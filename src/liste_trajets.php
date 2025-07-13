<?php
session_start();
require_once 'pdo.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

try {
    $stmt = $pdo->query("
        SELECT 
            t.id, t.depart, t.arrivee, t.date_trajet, t.prix, t.places_disponibles,
            u.pseudo AS conducteur
        FROM trajets t
        JOIN utilisateurs u ON t.utilisateur_id = u.id
        WHERE t.places_disponibles > 0
        ORDER BY t.date_trajet ASC
    ");
    $trajets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur BDD : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Liste des trajets</title></head>
<body>
<nav>
  <a href="ajouter_trajet.php">Ajouter un trajet</a> |
  <a href="historique.php">Historique</a> |
  <a href="logout.php">Déconnexion</a>
</nav>
<h1>Trajets disponibles</h1>
<?php if (empty($trajets)): ?>
  <p>Aucun trajet disponible.</p>
<?php else: ?>
  <table border="1" cellpadding="5">
    <thead>
      <tr>
        <th>Départ</th><th>Arrivée</th><th>Date</th>
        <th>Prix</th><th>Places</th><th>Conducteur</th><th>Réserver</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($trajets as $t): ?>
      <tr>
        <td><?= htmlspecialchars($t['depart']) ?></td>
        <td><?= htmlspecialchars($t['arrivee']) ?></td>
        <td><?= htmlspecialchars($t['date_trajet']) ?></td>
        <td><?= number_format($t['prix'], 2) ?></td>
        <td><?= (int)$t['places_disponibles'] ?></td>
        <td><?= htmlspecialchars($t['conducteur']) ?></td>
        <td>
          <form action="reserver.php" method="POST">
            <input type="hidden" name="covoiturage_id" value="<?= (int)$t['id'] ?>">
            <button type="submit">Réserver</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
</body>
</html>
