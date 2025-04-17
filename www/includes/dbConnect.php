<?php
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
$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'mysql-trees-in-paris.alwaysdata.net';
$dbname = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'trees-in-paris_db';
$username = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? '409531';
$password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? 'Boubou78#64845236';

try {
    // Connexion directe sans utiliser .env
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
