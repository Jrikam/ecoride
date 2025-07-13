<?php
session_start();
require_once 'pdo.php'; // Connexion PDO dans $pdo

// Vérification utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_trajet'])) {
    // Récupération et nettoyage des données du formulaire
    $depart = trim($_POST['depart'] ?? '');
    $arrivee = trim($_POST['arrivee'] ?? '');
    $prix = floatval($_POST['prix'] ?? 0);
    $vehicule_id = intval($_POST['vehicule_id'] ?? 0);
    $places_disponibles = intval($_POST['places_disponibles'] ?? 0);
    $date_trajet = $_POST['date_trajet'] ?? '';

    // Validation simple
    if (!$depart || !$arrivee || $prix <= 0 || $vehicule_id <= 0 || $places_disponibles <= 0 || !$date_trajet) {
        $error = "Merci de remplir tous les champs correctement.";
    } else {
        // Calcul prix net en retirant 2 crédits de commission
        $commission = 2.0;
        $prix_net = $prix - $commission;
        if ($prix_net < 0) {
            $error = "Le prix doit être supérieur à la commission (2 crédits).";
        } else {
            // Insertion en base
            $sql = "INSERT INTO trajets 
                    (utilisateur_id, depart, arrivee, prix, prix_net, commission, vehicule_id, places_disponibles, date_trajet)
                    VALUES 
                    (:utilisateur_id, :depart, :arrivee, :prix, :prix_net, :commission, :vehicule_id, :places_disponibles, :date_trajet)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':utilisateur_id' => $userId,
                ':depart' => $depart,
                ':arrivee' => $arrivee,
                ':prix' => $prix,
                ':prix_net' => $prix_net,
                ':commission' => $commission,
                ':vehicule_id' => $vehicule_id,
                ':places_disponibles' => $places_disponibles,
                ':date_trajet' => $date_trajet
            ]);

            // Redirection après succès
            header('Location: ajouter_trajet.php');
            exit;


        }
    }
}

// Récupérer les véhicules de l’utilisateur pour le select
$sqlVehicules = "SELECT * FROM vehicules WHERE utilisateur_id = :user_id";
$stmtVehicules = $pdo->prepare($sqlVehicules);
$stmtVehicules->execute([':user_id' => $userId]);
$vehicules = $stmtVehicules->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Ajouter un trajet</title>
</head>
<body>
    <h1>Ajouter un trajet</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Adresse de départ : 
            <input type="text" name="depart" required value="<?= htmlspecialchars($_POST['depart'] ?? '') ?>">
        </label><br>

        <label>Adresse d'arrivée : 
            <input type="text" name="arrivee" required value="<?= htmlspecialchars($_POST['arrivee'] ?? '') ?>">
        </label><br>

        <label>Prix (en crédits) : 
            <input type="number" step="0.01" name="prix" min="2" required value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>">
        </label><br>

        <label>Nombre de places disponibles : 
            <input type="number" name="places_disponibles" min="1" required value="<?= htmlspecialchars($_POST['places_disponibles'] ?? '') ?>">
        </label><br>

        <label>Date du trajet : 
            <input type="datetime-local" name="date_trajet" required value="<?= htmlspecialchars($_POST['date_trajet'] ?? '') ?>">
        </label><br>

        <label for="vehicule_id">Véhicule :</label>
        <select name="vehicule_id" id="vehicule_id" required>
            <option value="">-- Choisissez un véhicule --</option>
            <?php if (!empty($vehicules)): ?>
                <?php foreach ($vehicules as $vehicule): ?>
                    <option value="<?= (int)$vehicule['id'] ?>" <?= (isset($_POST['vehicule_id']) && $_POST['vehicule_id'] == $vehicule['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele'] . ' - ' . $vehicule['immatriculation']) ?>
                    </option>
                <?php endforeach; ?>
            <?php else: ?>
                <option disabled>Aucun véhicule enregistré</option>
            <?php endif; ?>
        </select>

        <?php if (empty($vehicules)): ?>
            <p><a href="ajouter_vehicule.php">➕ Ajouter un véhicule</a></p>
        <?php endif; ?>
        <br>

        <button type="submit" name="submit_trajet">Enregistrer le trajet</button>
    </form>
</body>
</html>
