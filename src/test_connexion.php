<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=ecoride;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie à la base ecoride !";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
