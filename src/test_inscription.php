<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<pre>POST reçu : '; print_r($_POST); echo '</pre>';
} else {
    echo '<form method="post" action="">
            <input name="pseudo" placeholder="Pseudo" required><br>
            <input name="email" placeholder="Email" type="email" required><br>
            <input name="mot_de_passe" placeholder="Mot de passe" type="password" required><br>
            <button type="submit">Envoyer</button>
          </form>';
}
?>
