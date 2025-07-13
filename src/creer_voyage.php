<?php
session_start();
require_once 'pdo.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit;
}

$chauffeur_id = $_SESSION['utilisateur_id'];
$commission = 2;
$erreur = '';

$vehicules = $pdo->prepare("SELECT * FROM vehicules WHERE utilisateur_id = ?");
$vehicules->execute([$chauffeur_id]);
$liste_vehicules = $vehicules->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $depart = $_POST['depart'] ?? '';
    $arrivee = $_POST['arrivee'] ?? '';
    $prix = $_POST['prix'] ?? '';
    $vehicule_id = $_POST['vehicule_id'] ?? '';

    if ($depart && $arrivee && $prix && $vehicule_id) {
        $prix_net = $prix - $commission;
        $stmt = $pdo->prepare("INSERT INTO trajets (utilisateur_id, depart, arrivee, prix, prix_net, commission, vehicule_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$chauffeur_id, $depart, $arrivee, $prix, $prix_net, $commission, $vehicule_id]);
        header('Location: espace_utilisateur.php');
        exit;
    } else {
        $erreur = "Tous les champs sont obligatoires.";
    }
}
?>

<h2>Créer un trajet</h2>
<?php if ($erreur): ?><p style="color:red"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>
<form method="post">
    Départ : <input type="text" name="depart" required><br>
    Arrivée : <input type="text" name="arrivee" required><br>
    Prix (crédits) : <input type="number" name="prix" min="3" required><br>
    Véhicule :
    <select name="vehicule_id" required>
        <option value="">-- Sélectionner --</option>
        <?php foreach ($liste_vehicules as $vehicule): ?>
            <option value="<?= $vehicule['id'] ?>">
                <?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele'] . ' (' . $vehicule['immatriculation'] . ')') ?>
            </option>
        <?php endforeach; ?>
    </select><br>
    <input type="submit" value="Créer le trajet">
</form>
