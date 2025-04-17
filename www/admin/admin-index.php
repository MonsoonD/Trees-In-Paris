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

// Récupérer les 5 derniers projets
$recentProjectsStmt = $db->query("SELECT p.*, a.name as arrond_name 
                                 FROM planting_projects p 
                                 LEFT JOIN arrondissements a ON p.arrondissement_id = a.id 
                                 ORDER BY p.date_fin DESC LIMIT 5");
$recentProjects = $recentProjectsStmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le nombre d'arbres par arrondissement (top 5)
$treesByArrondStmt = $db->query("SELECT a.name, SUM(p.nombre_arbres_plantes) as total_trees
                                FROM planting_projects p
                                JOIN arrondissements a ON p.arrondissement_id = a.id
                                GROUP BY p.arrondissement_id
                                ORDER BY total_trees DESC
                                LIMIT 5");
$treesByArrond = $treesByArrondStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Admin - Trees in Paris </title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.12);
        }
        .card-header {
            background-color: #198754;
            color: white;
            border-bottom: none;
            padding: 1rem 1.5rem;
        }
        .stat-card {
            text-align: center;
            padding: 1.5rem;
            height: 100%;
        }
        .stat-icon {
            font-size: 2.5rem;
            width: 70px;
            height: 70px;
            line-height: 70px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .bg-gradient-green {
            background: linear-gradient(45deg, #198754, #20c997);
        }
        .bg-gradient-blue {
            background: linear-gradient(45deg, #0d6efd, #6610f2);
        }
        .bg-gradient-orange {
            background: linear-gradient(45deg, #fd7e14, #ffc107);
        }
        .bg-gradient-purple {
            background: linear-gradient(45deg, #6f42c1, #e83e8c);
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #343a40;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .action-card {
            text-align: center;
            padding: 2rem 1.5rem;
            height: 100%;
        }
        .action-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #198754;
        }
        .table th {
            font-weight: 600;
            color: #495057;
        }
        .table td {
            vertical-align: middle;
        }
        .badge-pill {
            padding: 0.35em 0.65em;
            border-radius: 50rem;
        }
        .welcome-banner {
            background: linear-gradient(45deg, #198754, #20c997);
            color: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .welcome-banner h2 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .welcome-banner p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
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
                        <h5 class="mb-0">Trees in Paris</h5>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="admin-index.php">
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
                            <a class="nav-link" href="upload.php">
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
                <!-- Welcome Banner -->
                <div class="welcome-banner d-flex justify-content-between align-items-center">
                    <div>
                        <h2>Bienvenue sur l'administration des Arbres à Paris</h2>
                        <p>Gérez et suivez les projets de plantation d'arbres à travers Paris</p>
                    </div>
                    <div>
                        <a href="../index.php" class="btn btn-outline-light">
                            <i class="fas fa-external-link-alt me-2"></i> Voir le site
                        </a>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-6 col-xl-3 mb-4">
                        <div class="card h-100">
                            <div class="stat-card">
                                <div class="stat-icon bg-gradient-green">
                                    <i class="fas fa-tree"></i>
                                </div>
                                <div class="stat-value"><?php echo number_format($totalTrees); ?></div>
                                <div class="stat-label">Arbres plantés</div>
                                <div class="progress mt-3" style="height: 5px;">
                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 mb-4">
                        <div class="card h-100">
                            <div class="stat-card">
                                <div class="stat-icon bg-gradient-blue">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                                <div class="stat-value"><?php echo number_format($totalProjects); ?></div>
                                <div class="stat-label">Projets</div>
                                <div class="progress mt-3" style="height: 5px;">
                                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 mb-4">
                        <div class="card h-100">
                            <div class="stat-card">
                                <div class="stat-icon bg-gradient-orange">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="stat-value"><?php echo number_format($totalArrond); ?></div>
                                <div class="stat-label">Arrondissements</div>
                                <div class="progress mt-3" style="height: 5px;">
                                    <div class="progress-bar bg-warning" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 mb-4">
                        <div class="card h-100">
                            <div class="stat-card">
                                <div class="stat-icon bg-gradient-purple">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="stat-value"><?php echo date('d/m/Y', strtotime($lastUpdate)); ?></div>
                                <div class="stat-label">Dernière mise à jour</div>
                                <div class="progress mt-3" style="height: 5px;">
                                    <div class="progress-bar bg-info" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Arbres par arrondissement</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="treesByArrondChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-tools me-2"></i> Actions rapides</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="action-card">
                                            <i class="fas fa-upload action-icon"></i>
                                            <h5>Importer des données</h5>
                                            <p class="text-muted">Télécharger un fichier JSON pour mettre à jour la base de données</p>
                                            <a href="upload.php" class="btn btn-success">
                                                <i class="fas fa-upload me-1"></i> Importer
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="action-card">
                                            <i class="fas fa-database action-icon"></i>
                                            <h5>Gérer les données</h5>
                                            <p class="text-muted">Consulter, modifier et supprimer les enregistrements</p>
                                            <a href="manage-data.php" class="btn btn-primary">
                                                <i class="fas fa-table me-1"></i> Gérer
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="action-card">
                                            <i class="fas fa-download action-icon"></i>
                                            <h5>Exporter les données</h5>
                                            <p class="text-muted">Exporter les tables de la base de données au format CSV</p>
                                            <a href="manage-data.php" class="btn btn-info">
                                                <i class="fas fa-file-export me-1"></i> Exporter
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Projects -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-leaf me-2"></i> Projets de plantation récents</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Nom du projet</th>
                                        <th>Arrondissement</th>
                                        <th>Arbres plantés</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentProjects)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Aucun projet trouvé</td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentProjects as $project): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($project['nom_projet'] ?? 'Projet sans nom'); ?></strong>
                                            </td>
                                            <td>
                                                <?php if (isset($project['arrond_name']) && !empty($project['arrond_name'])): ?>
                                                    <span class="badge bg-info"><?php echo htmlspecialchars($project['arrond_name']); ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inconnu</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo number_format($project['nombre_arbres_plantes'] ?? 0); ?></strong> arbres
                                            </td>
                                            <td>
                                                <?php 
                                                    $date = isset($project['date_fin']) ? date('d M Y', strtotime($project['date_fin'])) : 'N/A';
                                                    echo $date;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                    $status = $project['statut'] ?? '';
                                                    $statusClass = 'bg-secondary';
                                                    $statusText = 'Inconnu';
                                                    
                                                    if (stripos($status, 'complete') !== false || stripos($status, 'terminé') !== false) {
                                                        $statusClass = 'bg-success';
                                                        $statusText = 'Terminé';
                                                    } elseif (stripos($status, 'progress') !== false || stripos($status, 'cours') !== false) {
                                                        $statusClass = 'bg-warning';
                                                        $statusText = 'En cours';
                                                    } elseif (stripos($status, 'plan') !== false || stripos($status, 'prévu') !== false) {
                                                        $statusClass = 'bg-info';
                                                        $statusText = 'Planifié';
                                                    }
                                                ?>
                                                <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="manage-data.php?table=planting_projects&action=edit&id=<?php echo $project['id']; ?>" class="btn btn-outline-primary" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="manage-data.php?table=planting_projects" class="btn btn-outline-success">
                                <i class="fas fa-list me-1"></i> Voir tous les projets
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- System Info -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Informations système</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Version PHP</th>
                                            <td><?php echo phpversion(); ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Type de base de données</th>
                                            <td><?php echo $db->getAttribute(PDO::ATTR_DRIVER_NAME); ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Logiciel serveur</th>
                                            <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Heure actuelle</th>
                                            <td><?php echo date('Y-m-d H:i:s'); ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Utilisateur admin</th>
                                            <td><?php echo $_SESSION['username'] ?? 'Inconnu'; ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Environnement</th>
                                            <td>
                                                <?php 
                                                    $env = getenv('APP_ENV') ?: 'production';
                                                    echo ucfirst($env);
                                                ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Graphique des arbres par arrondissement
            const arrondCtx = document.getElementById('treesByArrondChart').getContext('2d');
            
            const arrondData = {
                labels: [
                    <?php foreach ($treesByArrond as $item): ?>
                    '<?php echo addslashes($item['name']); ?>',
                    <?php endforeach; ?>
                ],
                datasets: [{
                    label: 'Arbres plantés',
                    data: [
                        <?php foreach ($treesByArrond as $item): ?>
                        <?php echo $item['total_trees']; ?>,
                        <?php endforeach; ?>
                    ],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.7)',
                        'rgba(13, 110, 253, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(220, 53, 69, 0.7)',
                        'rgba(111, 66, 193, 0.7)'
                    ],
                    borderColor: [
                        'rgb(25, 135, 84)',
                        'rgb(13, 110, 253)',
                        'rgb(255, 193, 7)',
                        'rgb(220, 53, 69)',
                        'rgb(111, 66, 193)'
                    ],
                    borderWidth: 1
                }]
            };
            
            new Chart(arrondCtx, {
                type: 'bar',
                data: arrondData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y.toLocaleString() + ' arbres';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
