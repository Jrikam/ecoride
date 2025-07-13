<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connexion à la base de données
$host = 'db';
$db = 'ecoride';
$user = 'ecoride_user';
$pass = 'ecoride_pass';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
