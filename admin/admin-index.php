<?php
// Vérification de l'authentification admin
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit;
}

// Récupérer les statistiques de la base de données
require_once __DIR__ . '/../includes/dbConnect.php';

// Nombre total d'arbres
$totalTreesStmt = $db->query("SELECT SUM(nombre_arbres_plantes) as total FROM planting_projects");
$totalTrees = $totalTreesStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Nombre total de projets
$totalProjectsStmt = $db->query("SELECT COUNT(*) as total FROM planting_projects");
$totalProjects = $totalProjectsStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Nombre d'arrondissements
$arrondStmt = $db->query("SELECT COUNT(*) as total FROM arrondissements");
$totalArrond = $arrondStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Dernière mise à jour
$lastUpdateStmt = $db->query("SELECT MAX(date_fin) as last_update FROM planting_projects");
$lastUpdate = $lastUpdateStmt->fetch(PDO::FETCH_ASSOC)['last_update'] ?? 'N/A';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Les Arbres à Paris</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }
        .card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background-color: #198754;
            color: white;
        }
        .stat-card {
            text-align: center;
            padding: 20px;
        }
        .stat-icon {
            font-size: 48px;
            color: #198754;
            margin-bottom: 15px;
        }
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #198754;
        }
        .action-card {
            text-align: center;
            padding: 30px 20px;
        }
        .action-icon {
            font-size: 48px;
            color: #198754;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container admin-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-tree text-success me-2"></i> Administration - Les Arbres à Paris</h1>
            <div>
                <a href="../www/files/index.php" class="btn btn-outline-success me-2" target="_blank">
                    <i class="fas fa-external-link-alt me-1"></i> Voir le site
                </a>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
                </a>
            </div>
        </div>
        
        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Statistiques</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <i class="fas fa-tree stat-icon"></i>
                                    <div class="stat-value"><?php echo number_format($totalTrees); ?></div>
                                    <div class="stat-label">Arbres plantés</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <i class="fas fa-project-diagram stat-icon"></i>
                                    <div class="stat-value"><?php echo number_format($totalProjects); ?></div>
                                    <div class="stat-label">Projets</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <i class="fas fa-map-marker-alt stat-icon"></i>
                                    <div class="stat-value"><?php echo number_format($totalArrond); ?></div>
                                    <div class="stat-label">Arrondissements</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <i class="fas fa-calendar-alt stat-icon"></i>
                                    <div class="stat-value"><?php echo date('d/m/Y', strtotime($lastUpdate)); ?></div>
                                    <div class="stat-label">Dernière mise à jour</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0"><i class="fas fa-tools me-2"></i> Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="action-card">
                                    <i class="fas fa-upload action-icon"></i>
                                    <h4>Importer des données</h4>
                                    <p>Téléverser un fichier JSON pour mettre à jour la base de données</p>
                                    <a href="upload.php" class="btn btn-success">
                                        <i class="fas fa-upload me-1"></i> Importer
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="action-card">
                                    <i class="fas fa-database action-icon"></i>
                                    <h4>Gérer les données</h4>
                                    <p>Visualiser et modifier les données existantes</p>
                                    <a href="manage-data.php" class="btn btn-success">
                                        <i class="fas fa-table me-1"></i> Gérer
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="action-card">
                                    <i class="fas fa-cog action-icon"></i>
                                    <h4>Paramètres</h4>
                                    <p>Configurer les paramètres de l'application</p>
                                    <a href="settings.php" class="btn btn-success">
                                        <i class="fas fa-cog me-1"></i> Configurer
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Activité récente -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0"><i class="fas fa-history me-2"></i> Activité récente</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Action</th>
                                        <th>Utilisateur</th>
                                        <th>Détails</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i'); ?></td>
                                        <td>Connexion</td>
                                        <td>Admin</td>
                                        <td>Connexion réussie</td>
                                    </tr>
                                    <!-- Ajouter d'autres entrées d'activité ici -->
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime('-1 day')); ?></td>
                                        <td>Import de données</td>
                                        <td>Admin</td>
                                        <td>Importation de 150 enregistrements</td>
                                    </tr>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime('-3 day')); ?></td>
                                        <td>Mise à jour</td>
                                        <td>Système</td>
                                        <td>Mise à jour automatique des statistiques</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle avec Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
