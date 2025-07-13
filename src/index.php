<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=db;dbname=ecoride;charset=utf8', 'ecoride_user', 'ecoride_pass');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
$utilisateur_id = 1;
$depart = trim($_POST['depart']);
$arrivee = trim($_POST['arrivee']);
$date_depart = $_POST['date_depart'];
$date_arrive = isset($_POST['date_arrivee']) ? $_POST['date_arrivee'] : null;
$prix= $_POST['prix'];  
$places_disponibles = $_POST['places'];
$type_vehicule = trim($_POST['type_vehicule']);
$est_ecologique = isset($_POST['ecologique']) ? 1 : 0;

    if (
        empty($depart) || empty($arrivee) || empty($date_depart) ||
        empty($date_arrive) || empty($prix) || empty($places_disponibles) || empty($type_vehicule)
    ) {
        die('Tous les champs obligatoires doivent être remplis.');
    }

    $date_depart_obj = DateTime::createFromFormat('Y-m-d\TH:i', $date_depart);
    $date_arrive_obj = DateTime::createFromFormat('Y-m-d\TH:i', $date_arrive);
    if (!$date_depart_obj || !$date_arrive_obj) {
        die("Format de date/heure invalide.");
    }

    $date_depart_sql = $date_depart_obj->format('Y-m-d H:i:s');
    $date_arrive_sql = $date_arrive_obj->format('Y-m-d H:i:s');

    try {
        $sql = "INSERT INTO covoiturages (utilisateur_id, lieu_depart, lieu_arrivee, date_depart, date_arrive, prix, places_disponibles, type_vehicule, est_ecologique)
                VALUES (:utilisateur_id, :lieu_depart, :lieu_arrivee, :date_depart, :date_arrive, :prix, :places_disponibles, :type_vehicule, :est_ecologique)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':utilisateur_id' => $utilisateur_id,
            ':lieu_depart' => $depart,
            ':lieu_arrivee' => $arrivee,
            ':date_depart' => $date_depart_sql,
            ':date_arrive' => $date_arrive_sql,
            ':prix' => $prix,
            ':places_disponibles' => $places_disponibles,
            ':type_vehicule' => $type_vehicule,
            ':est_ecologique' => $est_ecologique,
        ]);
        echo "Covoiturage enregistré avec succès.";
    } catch (Exception $e) {
        die("Erreur lors de l'enregistrement : " . $e->getMessage());
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>EcoRide - Covoiturage Écologique</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <img src="src/image/ecoride-high-resolution-logo.png" alt="Logo EcoRide" class="logo">
  <h1>Bienvenue sur Eco Ride</h1>
</header>

<main>
  <p><strong>Qui sommes-nous ?</strong><br>
  Nous facilitons le covoiturage pour réduire l’impact environnemental des déplacements.</p>

  <section class="presentation">
    <h2>Notre Vision</h2>
    <div class="contenu">
      <p>
        ÉcoRide promeut une mobilité durable à travers le covoiturage. Nous croyons en une planète plus verte, des trajets plus partagés, et une communauté solidaire.
      </p>
    </div>
  </section>

  <img src="image/covoiturage.jpg" alt="Illustration Covoiturage" class="illustration">

  <!-- Formulaire de recherche d'itinéraire -->
  <section class="formulaire">
  <h3>Rechercher un itinéraire</h3>
  <form action="index.php" method="POST">
    <label for="depart">Départ :</label>
    <input type="text" id="depart" name="depart" required><br><br>

    <label for="arrivee">Arrivée :</label>
    <input type="text" id="arrivee" name="arrivee" required><br><br>

    <label for="date_depart">Date et heure de départ :</label>
    <input type="datetime-local" id="date_depart" name="date_depart" required><br><br>

    <label for="date_arrive">Date et heure d'arrivée :</label>
    <input type="datetime-local" id="date_arrive" name="date_arrive" required><br><br>

    <label for="prix">Prix (€) :</label>
    <input type="number" step="0.01" id="prix" name="prix" required><br><br>

    <label for="places">Nombre de places :</label>
    <input type="number" id="places" name="places" required><br><br>

    <label for="type_vehicule">Type de véhicule :</label>
    <input type="text" id="type_vehicule" name="type_vehicule" required><br><br>

    <label for="ecologique">Covoiturage écologique ?</label>
    <input type="checkbox" id="ecologique" name="ecologique" value="1"><br><br>

    <input type="submit" value="Rechercher">
  </form>
</section>


  <!-- Formulaire de filtres de recherche -->
<section class="filtres">
  <?php
    // Exemple d'affichage des covoiturages déjà créés
    $stmt = $pdo->query("SELECT * FROM covoiturages ORDER BY date_depart ASC");
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($resultats)):
        foreach ($resultats as $row):
    ?>
      <div>
        <p><strong><?= htmlspecialchars($row['lieu_depart']) ?> → <?= htmlspecialchars($row['lieu_arrivee']) ?></strong></p>
        <p>Départ : <?= htmlspecialchars($row['date_depart']) ?></p>
        <p>Prix : <?= htmlspecialchars($row['prix']) ?> €</p>
        <p>Places : <?= htmlspecialchars($row['places_disponibles']) ?></p>
        <p>Écologique : <?= ($row['est_ecologique']) ? 'Oui' : 'Non' ?></p>

        <a href="detail_covoiturage.php?id=<?= $row['id'] ?>">Détail</a>

      </div>
      <hr>
    <?php
        endforeach;
    elseif ($_SERVER["REQUEST_METHOD"] == "POST"):
        echo "<p>Aucun résultat trouvé avec ces filtres.</p>";
    endif;
    ?>
</section>


</main>

<footer>
  Contactez-nous : <a href="mailto:contact@ecoride.com" style="color:white;">contact@ecoride.com</a><br>
  <a href="#" style="color:white;">Mentions légales</a>
</footer>

</body>
</html>