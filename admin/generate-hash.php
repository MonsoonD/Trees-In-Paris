<?php
$password = 'admin2222'; // Remplacez par le mot de passe souhaité
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Mot de passe: $password<br>";
echo "Hachage: $hash<br>";
echo "<br>Utilisez ce hachage dans votre requête SQL pour créer un utilisateur.";
