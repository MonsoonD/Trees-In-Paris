<?php
require_once '../includes/dbConnect.php';

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialiser la réponse
$response = [
    'success' => false,
    'message' => '',
    'years' => [],
    'trees' => []
];

try {
    // Récupérer les paramètres de filtrage
    $arrondissement = isset($_GET['arrondissement']) ? $_GET['arrondissement'] : 'all';
    $yearStart = isset($_GET['yearStart']) ? (int)$_GET['yearStart'] : 2020; // Année par défaut: 2020
    $yearEnd = isset($_GET['yearEnd']) ? (int)$_GET['yearEnd'] : date('Y');
    
    // Journaliser les paramètres pour le débogage
    error_log("Filtres: arrondissement=$arrondissement, yearStart=$yearStart, yearEnd=$yearEnd");
    
    // Construire la requête SQL avec les filtres
    $sql = "SELECT YEAR(date_fin) as year, SUM(nombre_arbres_plantes) as trees_planted 
            FROM planting_projects 
            WHERE date_fin IS NOT NULL 
            AND YEAR(date_fin) BETWEEN :yearStart AND :yearEnd";
    
    // Ajouter le filtre d'arrondissement si nécessaire
    if ($arrondissement !== 'all') {
        $sql .= " AND arrondissement_id = :arrondissement";
    }
    
    // Grouper et ordonner les résultats
    $sql .= " GROUP BY YEAR(date_fin) ORDER BY year";
    
    // Journaliser la requête SQL pour le débogage
    error_log("Requête SQL: $sql");
    
    // Préparer et exécuter la requête
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':yearStart', $yearStart, PDO::PARAM_INT);
    $stmt->bindParam(':yearEnd', $yearEnd, PDO::PARAM_INT);
    
    if ($arrondissement !== 'all') {
        $stmt->bindParam(':arrondissement', $arrondissement, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $chartData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Journaliser les résultats pour le débogage
    error_log("Résultats: " . print_r($chartData, true));
    
    // Préparer les données pour la réponse
    $years = [];
    $trees = [];
    
    foreach ($chartData as $data) {
        $years[] = $data['year'];
        $trees[] = (int)$data['trees_planted'];
    }
    
    // Construire la réponse
    $response['success'] = true;
    $response['years'] = $years;
    $response['trees'] = $trees;
    
} catch (PDOException $e) {
    $response['message'] = 'Erreur de base de données: ' . $e->getMessage();
    error_log("Erreur PDO: " . $e->getMessage());
}

// Envoyer la réponse au format JSON
header('Content-Type: application/json');
echo json_encode($response);
