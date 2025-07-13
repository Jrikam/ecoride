<?php
session_start();
require_once 'pdo.php';

$role = $_SESSION['role'] ?? null;  // ← Ici, récupère le rôle

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['id'];
$role = $_SESSION['role'] ?? null; // AJOUTÉ ICI : récupération du rôle depuis la session

// Trajets en tant que chauffeur
$stmt1 = $pdo->prepare("
    SELECT * 
    FROM trajets 
    WHERE utilisateur_id = ?
    ORDER BY date_trajet DESC
");
$stmt1->execute([$userId]);
$trajets_chauffeur = $stmt1->fetchAll(PDO::FETCH_ASSOC);

// Trajets en tant que passager
$stmt2 = $pdo->prepare("
    SELECT t.*
    FROM trajets t
    JOIN participations p ON t.id = p.covoiturage_id
    WHERE p.utilisateur_id = ?
    ORDER BY t.date_trajet DESC
");
$stmt2->execute([$userId]);
$trajets_passager = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<?php
$trajet_demarre_id = $_GET['demarre'] ?? null;
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Historique</title>
</head>
<body>
  <p>Rôle actuel : <?= htmlspecialchars($role) ?></p> <!-- ✅ AJOUT ICI -->
  

<nav>
  <a href="ajouter_trajet.php">Ajouter un trajet</a> |
  <a href="historique.php">Historique</a> |
  
  <?php if ($role === 'employe'): ?>
    <a href="employe.php">Espace Employé</a> |
  <?php endif; ?>

  <?php if ($role === 'passager' || $role === 'les deux'): ?>
    <a href="deposer_avis.php">Déposer un avis</a> |
  <?php endif; ?>

  <a href="avis.php">Avis reçus</a> |

  <a href="logout.php">Déconnexion</a>
</nav>

<h2>Mes trajets en tant que chauffeur</h2>
<?php if (empty($trajets_chauffeur)): ?>
  <p>Aucun trajet trouvé.</p>
<?php else: ?>
  <?php foreach ($trajets_chauffeur as $trajet): ?>
    <div style="margin-bottom:10px;">
      <?= htmlspecialchars($trajet['depart']) ?> → <?= htmlspecialchars($trajet['arrivee']) ?> | 
      <?= htmlspecialchars($trajet['date_trajet']) ?>

      <?php if (isset($trajet['etat'])): ?>
    <?php if ($trajet['etat'] === 'en_attente'): ?>
        <form method="POST" action="demarrer_trajet.php" style="display:inline;">
            <input type="hidden" name="covoiturage_id" value="<?= $trajet['id'] ?>">
            <button type="submit">Démarrer</button>
        </form>
    <?php elseif ($trajet['etat'] === 'en_cours'): ?>
        <form method="POST" action="terminer_trajet.php" style="display:inline;" target="_top">
            <input type="hidden" name="covoiturage_id" value="<?= $trajet['id'] ?>">
            <button type="submit">✅ Arrivée à destination</button>
        </form>
    <?php endif; ?>

          <?php else: ?>
              <strong>Trajet terminé</strong>
          <?php endif; ?>
  

      <form method="POST" action="annuler.php" style="display:inline;">
        <input type="hidden" name="type" value="chauffeur">
        <input type="hidden" name="covoiturage_id" value="<?= $trajet['id'] ?>">
        <button type="submit">Annuler</button>
      </form>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<h2>Mes trajets en tant que passager</h2>
<?php if (empty($trajets_passager)): ?>
  <p>Aucun trajet trouvé.</p>
<?php else: ?>
  <?php foreach ($trajets_passager as $trajet): ?>
    <div style="margin-bottom:10px;">
      <?= htmlspecialchars($trajet['depart']) ?> → <?= htmlspecialchars($trajet['arrivee']) ?> | 
      <?= htmlspecialchars($trajet['date_trajet']) ?>

      <?php if (isset($trajet['etat'])): ?>
          <?php if ($trajet['etat'] === 'en_attente'): ?>
              <form method="POST" action="demarrer_trajet.php" style="display:inline;">
                <input type="hidden" name="covoiturage_id" value="<?= $trajet['id'] ?>">
                <button type="submit">Démarrer</button>
              </form>
          <?php elseif ($trajet['etat'] === 'en_cours'): ?>
              <form method="POST" action="terminer_trajet.php" style="display:inline;">
                <input type="hidden" name="covoiturage_id" value="<?= $trajet['id'] ?>">
                <button type="submit">Arrivée à destination</button>
              </form>
          <?php else: ?>
              <strong>Trajet terminé</strong>
          <?php endif; ?>
      <?php endif; ?>

      <form method="POST" action="annuler.php" style="display:inline;">
        <input type="hidden" name="type" value="passager">
        <input type="hidden" name="covoiturage_id" value="<?= $trajet['id'] ?>">
        <button type="submit">Se désister</button>
      </form>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

</body>
</html>