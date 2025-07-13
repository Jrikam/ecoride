<?php
$hash = '$2y$10$rD2KMw/W2jRZKPVPURQYbO9HFeOBE9FCcSHa6aKzAqX/mTnMWqCVq';
$password = 'admin123';

if (password_verify($password, $hash)) {
    echo "✅ Le mot de passe admin123 correspond bien au hash.";
} else {
    echo "❌ Échec : admin123 ne correspond pas au hash.";
}