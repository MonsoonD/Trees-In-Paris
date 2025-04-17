<?php
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorer les commentaires
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Supprimer les guillemets si présents
        if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
            $value = substr($value, 1, -1);
        }
        
        define($name, $value);
    }
}

// Définir le hash du mot de passe admin
if (defined('ADMIN_PASSWORD') && !defined('ADMIN_PASSWORD_HASH')) {
    define('ADMIN_PASSWORD_HASH', password_hash(ADMIN_PASSWORD, PASSWORD_DEFAULT));
}

// Définir l'environnement par défaut si non défini
if (!defined('APP_ENV')) {
    define('APP_ENV', 'production');
}

// Définir le mode debug par défaut si non défini
if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', false);
}

// Paramètres de connexion à la base de données
$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'mysql-trees-in-paris.alwaysdata.net';
$dbname = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'trees-in-paris_db';
$username = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? '409531';
$password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? 'Boubou78#64845236';

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
