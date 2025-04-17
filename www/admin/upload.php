<?php
// Vérification de l'authentification admin
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
                    require_once __DIR__ . '/../includes/dbConnect.php';
                    
                    // Chemin où sauvegarder le fichier
                    $uploadDir = 'database/';
                    
                    // Générer un nom de fichier unique basé sur le timestamp pour éviter les doublons
                    $uniqueFileName = time() . '_' . basename($fileName);
                    $uploadPath = $uploadDir . $uniqueFileName;
                    
                    // Déplacer le fichier vers le répertoire cible
                    if (move_uploaded_file($fileTmpName, $uploadPath)) {
                        // Inclure le script d'importation
                        require_once 'import-json.php';
                        
                        // Importer les données dans la base
                        $result = importJsonToTable($uploadPath, $targetTable);
                        
                        if ($result['success']) {
                            $message = "Fichier importé avec succès! " . $result['message'];
                            $messageType = "success";
                            
                            // Rediriger vers une autre page ou ajouter un paramètre pour éviter la réimportation
                            // en cas de rafraîchissement de la page
                            header("Location: upload.php?success=1&file=" . urlencode($uniqueFileName));
                            exit;
                        } else {
                            $message = "Erreur lors de l'importation: " . $result['message'];
                            $messageType = "danger";
                            
                            // Supprimer le fichier en cas d'échec d'importation
                            if (file_exists($uploadPath)) {
                                unlink($uploadPath);
                            }
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

// Afficher un message de succès si on revient après une redirection
if (isset($_GET['success']) && $_GET['success'] == 1 && isset($_GET['file'])) {
    $message = "Fichier " . htmlspecialchars($_GET['file']) . " importé avec succès!";
    $messageType = "success";
}

require_once __DIR__ . '/../includes/dbConnect.php';
$tablesQuery = $db->query("SHOW TABLES");
$tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);

// Récupérer la liste des fichiers JSON déjà importés
$uploadedFiles = [];
if (is_dir('database/')) {
    $files = scandir('database/');
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
            $uploadedFiles[] = [
                'name' => $file,
                'date' => date('Y-m-d H:i:s', filemtime('database/' . $file)),
                'size' => filesize('database/' . $file)
            ];
        }
    }
    // Trier par date de modification (le plus récent en premier)
    usort($uploadedFiles, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
}
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
            background-color: #f8f9fa;
        }
        .sidebar {
            background-color: #198754;
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.5rem 1rem;
            margin-bottom: 0.2rem;
            border-radius: 0.25rem;
        }
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
        }
        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }
        .main-content {
            padding: 2rem;
        }
        .card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            margin-bottom: 2rem;
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
        .table-responsive {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            padding: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="d-flex align-items-center justify-content-center mb-4">
                        <i class="fas fa-tree fa-2x me-2"></i>
                        <h5 class="mb-0">Trees In Paris</h5>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="admin-index.php">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage-data.php">
                                <i class="fas fa-database"></i>
                                Gérer les données
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="upload.php">
                                <i class="fas fa-file-import"></i>
                                Importer des données
                            </a>
                        </li>
                        <li class="nav-item mt-5">
                            <a class="nav-link" href="../index.php">
                                <i class="fas fa-home"></i>
                                Retour au site
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i>
                                Se déconnecter
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><i class="fas fa-file-import me-2"></i> Importation de données JSON</h1>
                </div>
                
                <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Formulaire d'upload -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-upload me-2"></i> Importer un fichier JSON</h5>
                    </div>
                    <div class="card-body">
                        <form action="upload.php" method="post" enctype="multipart/form-data" id="uploadForm">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="targetTable" class="form-label">Table cible</label>
                                    <select class="form-select" id="targetTable" name="targetTable" required>
                                        <option value="" selected disabled>Sélectionnez une table</option>
                                        <?php foreach ($tables as $table): ?>
                                        <option value="<?php echo $table; ?>"><?php echo $table; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Sélectionnez la table dans laquelle importer les données JSON.</div>
                                </div>
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
                
                <!-- Fichiers JSON importés -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i> Fichiers JSON importés</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($uploadedFiles)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Aucun fichier JSON n'a encore été importé.
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom du fichier</th>
                                        <th>Date d'importation</th>
                                        <th>Taille</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($uploadedFiles as $file): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($file['name']); ?></td>
                                        <td><?php echo $file['date']; ?></td>
                                        <td><?php echo formatFileSize($file['size']); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="database/<?php echo urlencode($file['name']); ?>" class="btn btn-outline-primary" title="Télécharger" download>
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" title="Supprimer" 
                                                        onclick="confirmDelete('<?php echo htmlspecialchars($file['name']); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Instructions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Instructions</h5>
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
                        
                        <h5 class="mt-4">Processus d'importation</h5>
                        <ol>
                            <li>Sélectionnez la table cible dans laquelle vous souhaitez importer les données.</li>
                            <li>Glissez-déposez votre fichier JSON ou cliquez pour le sélectionner.</li>
                            <li>Cliquez sur le bouton "Importer les données" pour lancer l'importation.</li>
                            <li>Le système validera le format du fichier et tentera d'insérer les données dans la table.</li>
                            <li>Un message de confirmation ou d'erreur s'affichera une fois l'opération terminée.</li>
                        </ol>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-lightbulb me-2"></i> <strong>Conseil :</strong> Assurez-vous que votre fichier JSON est correctement formaté avant de l'importer. Vous pouvez utiliser des outils en ligne comme <a href="https://jsonlint.com/" target="_blank" class="alert-link">JSONLint</a> pour valider votre JSON.
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer le fichier <strong id="deleteFileName"></strong> ?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form action="delete-file.php" method="post" id="deleteForm">
                        <input type="hidden" name="file" id="deleteFileInput">
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
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
        
        // Fonction pour confirmer la suppression
        function confirmDelete(fileName) {
            document.getElementById('deleteFileName').textContent = fileName;
            document.getElementById('deleteFileInput').value = fileName;
            
            // Afficher la modal de confirmation
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
</body>
</html>

<?php
// Fonction pour formater la taille du fichier
function formatFileSize($bytes) {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>
