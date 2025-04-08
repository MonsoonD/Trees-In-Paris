<?php
// C:\xampp\htdocs\Trees-In-Paris\includes\dbConnect.php

// Fonction simple pour charger les variables d'environnement depuis .env
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorer les commentaires
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Extraire la clé et la valeur
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Supprimer les guillemets si présents
        if (strpos($value, '"') === 0 || strpos($value, "'") === 0) {
            $value = substr($value, 1, -1);
        }
        
        // Définir la variable d'environnement
        $_ENV[$name] = $value;
        putenv("$name=$value");
    }
    
    return true;
}

// Chemin vers le fichier .env
$envPath = __DIR__ . '/../.env';

// Charger les variables d'environnement si le fichier existe
if (file_exists($envPath)) {
    loadEnv($envPath);
}

// Paramètres de connexion à la base de données
$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'trees_in_paris';
$username = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? '';

try {
    // Créer une connexion PDO
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Configurer PDO pour qu'il lance des exceptions en cas d'erreur
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configurer PDO pour qu'il retourne les résultats sous forme de tableau associatif
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // Afficher un message d'erreur
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
