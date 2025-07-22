<?php
// Connexion à la base
$pdo = new PDO('mysql:host=db;dbname=ecoride;charset=utf8', 'ecoride_user', 'ecoride_pass');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupérer l'ID passé en URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupérer les infos du covoiturage
$stmt = $pdo->prepare("SELECT c.*, u.pseudo, v.marque, v.modele, v.energie, p.animaux_acceptes, p.musique, p.discussion
                       FROM covoiturages c
                       JOIN utilisateurs u ON c.utilisateur_id = u.id
                       LEFT JOIN vehicules v ON c.utilisateur_id = v.utilisateur_id
                       LEFT JOIN preferences p ON c.utilisateur_id = p.utilisateur_id
                       WHERE c.id = :id");
$stmt->execute([':id' => $id]);
$covoiturage = $stmt->fetch();

if (!$covoiturage) {
    die("Covoiturage introuvable");
}

// Récupérer les avis validés du covoiturage
$stmtAvis = $pdo->prepare("
    SELECT a.note, a.commentaire, u.pseudo
    FROM avis a
    JOIN utilisateurs u ON a.conducteur_id = u.id
    WHERE a.covoiturage_id = :id
      AND a.statut = 'valid'
");

$stmtAvis->execute([':id' => $id]);
$avis = $stmtAvis->fetchAll();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <style>
  body {
    font-family: Arial, sans-serif;
    max-width: 700px;
    margin: 30px auto;
    padding: 15px;
    background: #f0f4f8;
    color: #333;
  }
  h1, h2 {
    color: #2c3e50;
  }
  p, ul {
    font-size: 1rem;
  }
  ul {
    list-style-type: disc;
    padding-left: 20px;
  }
</style>
<meta charset="UTF-8">
<title>Détail du covoiturage</title>
</head>
<body>
  <h1>Détail du trajet <?= htmlspecialchars($covoiturage['lieu_depart']) ?> → <?= htmlspecialchars($covoiturage['lieu_arrivee']) ?></h1>

  <p><strong>Date de départ :</strong> <?= $covoiturage['date_depart'] ?></p>
  <p><strong>Date d’arrivée :</strong> <?= $covoiturage['date_arrive'] ?></p>
  <p><strong>Prix :</strong> <?= $covoiturage['prix'] ?> €</p>
  <p><strong>Places disponibles :</strong> <?= $covoiturage['places_disponibles'] ?></p>
  <p><strong>Conducteur :</strong> <?= htmlspecialchars($covoiturage['pseudo']) ?></p>

  <h2>Véhicule</h2>
  <p>Marque : <?= $covoiturage['marque'] ?></p>
  <p>Modèle : <?= $covoiturage['modele'] ?></p>
  <p>Énergie : <?= $covoiturage['energie'] ?></p>

  <h2>Préférences du conducteur</h2>
  <p>Animaux : <?= $covoiturage['animaux_acceptes'] ? 'Oui' : 'Non' ?></p>
  <p>Musique : <?= $covoiturage['musique'] ? 'Oui' : 'Non' ?></p>
  <p>Discussion : <?= $covoiturage['discussion'] ? 'Oui' : 'Non' ?></p>

  <h2>Avis sur le conducteur</h2>
  <?php if ($avis): ?>
    <ul>
    <?php foreach ($avis as $a): ?>
      <li>Note : <?= $a['note'] ?>/5 - <?= htmlspecialchars($a['commentaire']) ?></li>
    <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>Aucun avis pour ce conducteur.</p>
  <?php endif; ?>
   <?php if (isset($_SESSION['utilisateur_id'])): ?>
  <?php if ($covoiturage['places_disponibles'] > 0 && $_SESSION['credit'] >= $covoiturage['prix']): ?>
    <form method="POST" action="participer.php" onsubmit="return confirm('Confirmez-vous vouloir dépenser <?= $covoiturage['prix'] ?> crédits pour ce covoiturage ?');">
      <input type="hidden" name="covoiturage_id" value="<?= $covoiturage['id'] ?>">
      <button type="submit">Participer</button>
    </form>
  <?php else: ?>
    <p style="color: red;">Pas de place ou crédit insuffisant.</p>
  <?php endif; ?>
<?php else: ?>
  <p><a href="login.php">Connectez-vous</a> ou <a href="creer_compte.php">créez un compte</a> pour participer.</p>
<?php endif; ?>

</body>
</html>
