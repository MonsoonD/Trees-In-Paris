<?php
session_start();

// Mettre à jour la dernière connexion si l'utilisateur était connecté
if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    require_once __DIR__ . '/../includes/dbConnect.php';
    
    try {
        // Rien à faire ici, la dernière connexion est déjà enregistrée lors du login
    } catch (PDOException $e) {
        // Log l'erreur pour l'administrateur
        error_log('Erreur lors de la déconnexion: ' . $e->getMessage());
    }
}

// Détruire toutes les variables de session
$_SESSION = array();

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion
header('Location: login.php');
exit;
