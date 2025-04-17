<?php
// Vérification de l'authentification admin (à implémenter selon votre système d'authentification)
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit;
}

// Initialisation des variables
$message = '';
$messageType = '';

// Traitement du formulaire d'upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['jsonFile'])) {
    // Validation du fichier
    $file = $_FILES['jsonFile'];
    $fileName = $file['name'];
    $fileType = $file['type'];
    $fileTmpName = $file['tmp_name'];
    $fileError = $file['error'];
    $fileSize = $file['size'];
    
    // Vérifier les erreurs d'upload
    if ($fileError === 0) {
        // Vérifier le type de fichier (doit être JSON)
        if ($fileType === 'application/json') {
            // Vérifier la taille du fichier (max 10MB)
            if ($fileSize <= 10000000) {
                // Lire le contenu du fichier
                $jsonContent = file_get_contents($fileTmpName);
                $data = json_decode($jsonContent, true);
                
                // Vérifier si le JSON est valide
                if ($data !== null) {
                    // Sélectionner la table cible
                    $targetTable = $_POST['targetTable'];
                    
                    // Connexion à la base de données
                    require_once __DIR__ . '../includes/dbConnect.php';
                    
                    // Chemin où sauvegarder le fichier
                    $uploadDir = 'database/';
                    $uploadPath = $uploadDir . basename($fileName);
                    
                    // Déplacer le fichier vers le répertoire cible
                    if (move_uploaded_file($fileTmpName, $uploadPath)) {
                        // Inclure le script d'importation
                        require_once 'import-json.php';
                        
                        // Importer les données dans la base
                        $result = importJsonToTable($uploadPath, $targetTable);
                        
                        if ($result['success']) {
                            $message = "Fichier importé avec succès! " . $result['message'];
                            $messageType = "success";
                        } else {
                            $message = "Erreur lors de l'importation: " . $result['message'];
                            $messageType = "danger";
                        }
                    } else {
                        $message = "Erreur lors du déplacement du fichier.";
                        $messageType = "danger";
                    }
                } else {
                    $message = "Le fichier JSON n'est pas valide.";
                    $messageType = "danger";
                }
            } else {
                $message = "Le fichier est trop volumineux (max 10MB).";
                $messageType = "danger";
            }
        } else {
            $message = "Seuls les fichiers JSON sont acceptés.";
            $messageType = "danger";
        }
    } else {
        $message = "Une erreur s'est produite lors de l'upload: code " . $fileError;
        $messageType = "danger";
    }
}

require_once __DIR__ . '/../includes/dbConnect.php';
$tablesQuery = $db->query("SHOW TABLES");
$tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Upload JSON</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            padding-top: 20px;
            background-color: #f8f9fa;
        }
        .admin-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .card-header {
            background-color: #198754;
            color: white;
        }
        .btn-success {
            background-color: #198754;
        }
        .upload-area {
            border: 2px dashed #198754;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            background-color: rgba(25, 135, 84, 0.1);
        }
        .upload-icon {
            font-size: 48px;
            color: #198754;
            margin-bottom: 10px;
        }
        #fileInfo {
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container admin-container">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0"><i class="fas fa-upload me-2"></i> Upload de fichier JSON</h3>
                <a href="admin-index.php" class="btn btn-light btn-sm"><i class="fas fa-arrow-left me-1"></i> Retour</a>
            </div>
            <div class="card-body">
                <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <form action="upload.php" method="post" enctype="multipart/form-data" id="uploadForm">
                    <div class="mb-4">
                        <label for="targetTable" class="form-label">Table cible</label>
                        <select class="form-select" id="targetTable" name="targetTable" required>
                            <option value="" selected disabled>Sélectionnez une table</option>
                            <?php foreach ($tables as $table): ?>
                            <option value="<?php echo $table; ?>"><?php echo $table; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Sélectionnez la table dans laquelle importer les données JSON.</div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Fichier JSON</label>
                        <div class="upload-area" id="uploadArea">
                            <i class="fas fa-file-upload upload-icon"></i>
                            <h5>Glissez-déposez votre fichier JSON ici</h5>
                            <p class="text-muted">ou cliquez pour sélectionner un fichier</p>
                            <input type="file" name="jsonFile" id="jsonFile" class="d-none" accept=".json" required>
                        </div>
                        <div id="fileInfo" class="text-muted"></div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                            <i class="fas fa-upload me-2"></i> Importer les données
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i> Instructions</h4>
            </div>
            <div class="card-body">
                <h5>Format du fichier JSON attendu</h5>
                <p>Le fichier JSON doit être un tableau d'objets, où chaque objet représente un enregistrement à insérer dans la table sélectionnée.</p>
                
                <h6>Exemple de format valide :</h6>
                <pre class="bg-light p-3 rounded">
[
  {
    "arrondissement_id": 1,
    "nom_projet": "Projet de plantation Avenue des Champs-Élysées",
    "date_debut": "2022-03-15",
    "date_fin": "2022-04-30",
    "nombre_arbres_plantes": 45
  },
  {
    "arrondissement_id": 2,
    "nom_projet": "Végétalisation Place de la République",
    "date_debut": "2022-05-10",
    "date_fin": "2022-06-20",
    "nombre_arbres_plantes": 32
  }
]</pre>
                
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i> <strong>Important :</strong> Les noms des propriétés dans le JSON doivent correspondre aux noms des colonnes dans la table cible.
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle avec Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('jsonFile');
            const fileInfo = document.getElementById('fileInfo');
            const submitBtn = document.getElementById('submitBtn');
            
            // Gérer le clic sur la zone d'upload
            uploadArea.addEventListener('click', function() {
                fileInput.click();
            });
            
            // Gérer le glisser-déposer
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadArea.classList.add('bg-light');
            });
            
            uploadArea.addEventListener('dragleave', function() {
                uploadArea.classList.remove('bg-light');
            });
            
            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('bg-light');
                
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    updateFileInfo();
                }
            });
            
            // Mettre à jour les informations du fichier
            fileInput.addEventListener('change', updateFileInfo);
            
            function updateFileInfo() {
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    
                    // Vérifier si c'est un fichier JSON
                    if (file.type !== 'application/json') {
                        fileInfo.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle me-1"></i> Erreur : Seuls les fichiers JSON sont acceptés.</span>';
                        submitBtn.disabled = true;
                        return;
                    }
                    
                    // Vérifier la taille du fichier
                    const fileSize = (file.size / 1024 / 1024).toFixed(2); // en MB
                    if (file.size > 10000000) { // 10MB
                        fileInfo.innerHTML = `<span class="text-danger"><i class="fas fa-times-circle me-1"></i> Erreur : Le fichier est trop volumineux (${fileSize} MB). Maximum 10 MB.</span>`;
                        submitBtn.disabled = true;
                        return;
                    }
                    
                    fileInfo.innerHTML = `
                        <span class="text-success"><i class="fas fa-check-circle me-1"></i> Fichier sélectionné :</span>
                        <div class="mt-2">
                            <strong>Nom :</strong> ${file.name}<br>
                            <strong>Taille :</strong> ${fileSize} MB<br>
                            <strong>Type :</strong> ${file.type}
                        </div>
                    `;
                    submitBtn.disabled = false;
                } else {
                    fileInfo.innerHTML = '';
                    submitBtn.disabled = true;
                }
            }
        });
    </script>
</body>
</html>
