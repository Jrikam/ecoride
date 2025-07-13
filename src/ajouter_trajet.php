<?php
session_start();
require_once 'pdo.php'; // connexion PDO

// Vérification si utilisateur connecté
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['id'];
$error = null;

try {
    // Vérifier crédits de l'utilisateur
    $stmtCredit = $pdo->prepare("SELECT credits FROM utilisateurs WHERE id = :id");
    $stmtCredit->execute([':id' => $userId]);
    $user = $stmtCredit->fetch();

    if (!$user) {
        $error = "Utilisateur non trouvé.";
    } elseif ($user['credits'] < 2) {
        $error = "Vous devez avoir au moins 2 crédits pour proposer un trajet.";
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_trajet'])) {
        // Récupérer et nettoyer les données du formulaire
        $depart = trim($_POST['depart'] ?? '');
        $arrivee = trim($_POST['arrivee'] ?? '');
        $prix = floatval($_POST['prix'] ?? 0);
        $vehicule_id = intval($_POST['vehicule_id'] ?? 0);
        $places_disponibles = intval($_POST['places_disponibles'] ?? 0);
        $date_trajet = $_POST['date_trajet'] ?? '';

        // Validation des champs
        if (!$depart || !$arrivee || $prix <= 0 || $vehicule_id <= 0 || $places_disponibles <= 0 || !$date_trajet) {
            $error = "Merci de remplir tous les champs correctement.";
        } else {
            $commission = 2.0;
            $prix_net = $prix - $commission;

            if ($prix_net < 0) {
                $error = "Le prix doit être supérieur à la commission (2 crédits).";
            } else {
                // Démarrer la transaction
                $pdo->beginTransaction();

                // Insertion du trajet
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

                // Déduction des crédits
                $updateCredit = $pdo->prepare("UPDATE utilisateurs SET credits = credits - 2 WHERE id = :id");
                $updateCredit->execute([':id' => $userId]);

                // Commit transaction
                $pdo->commit();

                // Redirection vers la liste des trajets
                header('Location: liste_trajets.php');
                exit;
            }
        }
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $error = "Erreur base de données : " . $e->getMessage();
}

// Récupérer la liste des véhicules de l'utilisateur
$stmtVehicules = $pdo->prepare("SELECT * FROM vehicules WHERE utilisateur_id = :user_id");
$stmtVehicules->execute([':user_id' => $userId]);
$vehicules = $stmtVehicules->fetchAll();
?>

<?php include 'header.php'; ?>

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

    <form method="post" action="">
        <label>Adresse de départ : <input type="text" name="depart" required value="<?= htmlspecialchars($_POST['depart'] ?? '') ?>"></label><br>
        <label>Adresse d'arrivée : <input type="text" name="arrivee" required value="<?= htmlspecialchars($_POST['arrivee'] ?? '') ?>"></label><br>
        <label>Prix (en crédits) : <input type="number" step="0.01" name="prix" min="2" required value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>"></label><br>
        <label>Nombre de places disponibles : <input type="number" name="places_disponibles" min="1" required value="<?= htmlspecialchars($_POST['places_disponibles'] ?? '') ?>"></label><br>
        <label>Date du trajet : <input type="datetime-local" name="date_trajet" required value="<?= htmlspecialchars($_POST['date_trajet'] ?? '') ?>"></label><br>

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
        </select><br>

        <?php if (empty($vehicules)): ?>
            <p><a href="ajouter_vehicule.php">➕ Ajouter un véhicule</a></p>
        <?php endif; ?>

        <button type="submit" name="submit_trajet">Enregistrer le trajet</button>
    </form>
</body>
</html>
