<?php
// Connexion à la base de données
require_once __DIR__ . '/../includes/dbConnect.php';

// Fonction pour hacher un mot de passe
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Liste des utilisateurs à mettre à jour (username => password en clair)
$users = [
    'admin2' => 'adminaccesslimited123456789012345678900987654312',
    // Ajoutez d'autres utilisateurs si nécessaire
];

// Mettre à jour chaque utilisateur
foreach ($users as $username => $plainPassword) {
    $hashedPassword = hashPassword($plainPassword);
    
    try {
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE username = ?");
        $result = $stmt->execute([$hashedPassword, $username]);
        
        if ($result) {
            echo "Mot de passe mis à jour pour l'utilisateur: $username<br>";
        } else {
            echo "Échec de la mise à jour pour l'utilisateur: $username<br>";
        }
    } catch (PDOException $e) {
        echo "Erreur lors de la mise à jour pour $username: " . $e->getMessage() . "<br>";
    }
}

echo "<p>Processus de mise à jour terminé.</p>";
