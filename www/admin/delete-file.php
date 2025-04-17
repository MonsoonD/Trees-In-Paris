<?php
// Vérification de l'authentification admin
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit;
}

// Vérifier si un fichier a été spécifié
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file'])) {
    $fileName = $_POST['file'];
    $filePath = 'database/' . $fileName;
    
    // Vérifier que le fichier existe et est dans le dossier database
    if (file_exists($filePath) && is_file($filePath) && pathinfo($fileName, PATHINFO_EXTENSION) === 'json') {
        // Supprimer le fichier
        if (unlink($filePath)) {
            $message = "Le fichier $fileName a été supprimé avec succès.";
            $messageType = "success";
        } else {
            $message = "Erreur lors de la suppression du fichier $fileName.";
            $messageType = "danger";
        }
    } else {
        $message = "Le fichier spécifié n'existe pas ou n'est pas un fichier JSON valide.";
        $messageType = "danger";
    }
    
    // Rediriger vers la page d'upload avec un message
    header("Location: upload.php?message=" . urlencode($message) . "&message_type=$messageType");
    exit;
} else {
    // Rediriger si aucun fichier n'est spécifié
    header("Location: upload.php");
    exit;
}
?>
