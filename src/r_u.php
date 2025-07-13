<?php
session_start();
// Déblocage rapide : si on passe ?admin=1 dans l'URL, on force la session Admin
if (isset($_GET['admin']) && $_GET['admin'] == '1') {
    $_SESSION['id']    = 6;                     // Remplace par l'ID de ton admin
    $_SESSION['role']  = 'administrateur';
    $_SESSION['email'] = 'monadmin@test.com';
    // facultatif : $_SESSION['credit'] = 100;
}

require_once 'pdo.php';

// Vérif accès admin
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['id'];
$stmt = $pdo->prepare("SELECT role FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'administrateur') {
    die("Accès refusé.");
}

// 1. Total crédits gagnés
$stmtTotal = $pdo->query("SELECT SUM(credit) AS total_credits FROM trajets");
$totalCredits = $stmtTotal->fetchColumn() ?? 0;

// 2. Nombre de covoiturages par jour
$stmtCovoit = $pdo->query("
    SELECT DATE(date_trajet) AS jour, COUNT(*) AS nb_covoiturages
    FROM trajets
    GROUP BY jour
    ORDER BY jour ASC
");
$covoitData = $stmtCovoit->fetchAll(PDO::FETCH_ASSOC);

// 3. Crédits gagnés par jour (supposons que les crédits sont liés à trajets)
$stmtCredits = $pdo->query("
    SELECT DATE(date_trajet) AS jour, SUM(credit) AS total_credits
    FROM trajets
    GROUP BY jour
    ORDER BY jour ASC
");
$creditsData = $stmtCredits->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<title>Espace Admin - Statistiques</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  body { font-family: Arial, sans-serif; margin: 20px; }
  h1, h2 { color: #333; }
  .stats { margin-bottom: 40px; }
  canvas { max-width: 700px; margin-bottom: 40px; }
</style>
</head>
<body>

<h1>Espace Administrateur</h1>

<div class="stats">
  <h2>Total des crédits gagnés par la plateforme : <?= number_format($totalCredits, 0, ',', ' ') ?></h2>
</div>

<div>
  <h2>Nombre de covoiturages par jour</h2>
  <canvas id="covoitChart"></canvas>
</div>

<div>
  <h2>Crédits gagnés par jour</h2>
  <canvas id="creditsChart"></canvas>
</div>

<script>
  // Préparation données covoiturages
  const covoitLabels = <?= json_encode(array_column($covoitData, 'jour')) ?>;
  const covoitCounts = <?= json_encode(array_column($covoitData, 'nb_covoiturages')) ?>;

  // Préparation données crédits
  const creditsLabels = <?= json_encode(array_column($creditsData, 'jour')) ?>;
  const creditsTotals = <?= json_encode(array_column($creditsData, 'total_credits')) ?>;

  // Graphique covoiturages
  const ctxCovoit = document.getElementById('covoitChart').getContext('2d');
  new Chart(ctxCovoit, {
    type: 'line',
    data: {
      labels: covoitLabels,
      datasets: [{
        label: 'Nombre de covoiturages',
        data: covoitCounts,
        borderColor: 'blue',
        backgroundColor: 'rgba(0,0,255,0.1)',
        fill: true,
        tension: 0.3
      }]
    },
    options: {
      scales: { y: { beginAtZero: true, precision: 0 } }
    }
  });

  // Graphique crédits
  const ctxCredits = document.getElementById('creditsChart').getContext('2d');
  new Chart(ctxCredits, {
    type: 'bar',
    data: {
      labels: creditsLabels,
      datasets: [{
        label: 'Crédits gagnés',
        data: creditsTotals,
        backgroundColor: 'green'
      }]
    },
    options: {
      scales: { y: { beginAtZero: true, precision: 0 } }
    }
  });
</script>

</body>
</html>
