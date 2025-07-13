<?php
// auto_login.php
session_start();

// Remplacez par l'ID exact de votre administrateur dans la table `utilisateurs`
$_SESSION['id']     = 5;                    // ID admin
$_SESSION['role']   = 'administrateur';     // Rôle admin
$_SESSION['email']  = 'monadmin@test.com';   // Email admin
$_SESSION['credit'] = 100;                  // Crédit facultatif

// Redirection vers l'espace administrateur
header('Location: r_u.php');
exit;
