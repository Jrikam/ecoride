<?php
session_start();
require_once 'pdo.php'; // Connexion PDO ($pdo)

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$userId    = $_SESSION['id'];
$type      = $_POST['type'] ?? null;
$trajetId  = $_POST['covoiturage_id'] ?? null; // ✅ CORRIGÉ ICI

if (!$type || !$trajetId) {
    die("Requête invalide.");
}

try {
    // Récupérer infos du trajet (prix et places dispos)
    $stmt = $pdo->prepare("SELECT prix, places_disponibles FROM trajets WHERE id = ?");
    $stmt->execute([$trajetId]);
    $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$trajet) {
        throw new Exception("Trajet introuvable.");
    }
    $prix = $trajet['prix'];

    if ($type === 'passager') {
        // 1) Supprimer la participation
        $stmt = $pdo->prepare("
            DELETE FROM participations
            WHERE utilisateur_id = ? AND covoiturage_id = ?
        ");
        $stmt->execute([$userId, $trajetId]);

        // 2) Rembourser le passager
        $stmt = $pdo->prepare("
            UPDATE utilisateurs
            SET credit = credit + ?
            WHERE id = ?
        ");
        $stmt->execute([$prix, $userId]);

        // 3) Réouvrir une place
        $stmt = $pdo->prepare("
            UPDATE trajets
            SET places_disponibles = places_disponibles + 1
            WHERE id = ?
        ");
        $stmt->execute([$trajetId]);

        $_SESSION['message'] = "Annulation confirmée. Vous avez été remboursé de {$prix} crédits.";

    } elseif ($type === 'chauffeur') {
        // 1) Récupérer tous les passagers
        $stmt = $pdo->prepare("
            SELECT u.email, p.utilisateur_id
            FROM utilisateurs u
            JOIN participations p ON u.id = p.utilisateur_id
            WHERE p.covoiturage_id = ?
        ");
        $stmt->execute([$trajetId]);
        $passagers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2) Rembourser chaque passager
        $stmtR = $pdo->prepare("
            UPDATE utilisateurs
            SET credit = credit + ?
            WHERE id = ?
        ");
        foreach ($passagers as $p) {
            $stmtR->execute([$prix, $p['utilisateur_id']]);
            // Tu peux activer ça si besoin : 
            // mail($p['email'], 'Annulation de trajet', 'Votre trajet a été annulé, vous avez été remboursé.');
        }

        // 3) Supprimer toutes les participations
        $stmt = $pdo->prepare("
            DELETE FROM participations
            WHERE covoiturage_id = ?
        ");
        $stmt->execute([$trajetId]);

        // 4) Supprimer le trajet
        $stmt = $pdo->prepare("
            DELETE FROM trajets
            WHERE id = ?
        ");
        $stmt->execute([$trajetId]);

        $_SESSION['message'] = "Trajet supprimé. Les passagers ont été remboursés.";
    } else {
        throw new Exception("Type d'annulation inconnu.");
    }

    header('Location: historique.php');
    exit;

} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
