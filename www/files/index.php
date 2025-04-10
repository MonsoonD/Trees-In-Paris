<?php include 'header.php'; ?>
<?php require_once '../../includes/dbConnect.php';

// Récupérer les statistiques globales
$totalTreesStmt = $db->query("SELECT SUM(nombre_arbres_plantes) as total FROM planting_projects");
$totalTrees = $totalTreesStmt->fetch()['total'] ?? '200K+';

$totalProjectsStmt = $db->query("SELECT COUNT(*) as total FROM planting_projects");
$totalProjects = $totalProjectsStmt->fetch()['total'] ?? '50+';

$arrondStmt = $db->query("SELECT COUNT(DISTINCT arrondissement_id) as total FROM planting_projects");
$totalArrond = $arrondStmt->fetch()['total'] ?? '20';

$speciesStmt = $db->query("SELECT COUNT(*) as total FROM (SELECT DISTINCT nom_projet FROM planting_projects) as temp");
$totalSpecies = $speciesStmt->fetch()['total'] ?? '150+';

$latestProjectsStmt = $db->query("SELECT p.*, a.name as arrond_name 
                                 FROM planting_projects p 
                                 JOIN arrondissements a ON p.arrondissement_id = a.id 
                                 ORDER BY p.date_fin DESC LIMIT 2");
$latestProjects = $latestProjectsStmt->fetchAll(PDO::FETCH_ASSOC);

$arrondissementsStmt = $db->query("SELECT id, name FROM arrondissements ORDER BY id");
$arrondissements = $arrondissementsStmt->fetchAll(PDO::FETCH_ASSOC);

$yearsStmt = $db->query("SELECT MIN(YEAR(date_fin)) as min_year, MAX(YEAR(date_fin)) as max_year FROM planting_projects WHERE date_fin IS NOT NULL");
$yearsRange = $yearsStmt->fetch(PDO::FETCH_ASSOC);
$minYear = $yearsRange['min_year'] ?? 2020;
$maxYear = $yearsRange['max_year'] ?? date('Y');

$chartDataStmt = $db->query("SELECT YEAR(date_fin) as year, SUM(nombre_arbres_plantes) as trees_planted 
                            FROM planting_projects 
                            WHERE date_fin IS NOT NULL 
                            GROUP BY YEAR(date_fin) 
                            ORDER BY year");
$chartData = $chartDataStmt->fetchAll(PDO::FETCH_ASSOC);

$chartYears = [];
$chartTrees = [];
foreach ($chartData as $data) {
    $chartYears[] = $data['year'];
    $chartTrees[] = $data['trees_planted'];
}
?>
<?php
echo "<!-- Données du graphique: ";
print_r($chartData);
echo " -->";

if (empty($chartYears) || empty($chartTrees)) {
    echo "<!-- ATTENTION: Les données du graphique sont vides! -->";
}
?>

<!-- Section Héro avec Image de Fond -->
<div class="hero-section position-relative d-flex align-items-center" style="min-height: 100vh; background: url('../img/bg.png') no-repeat center center; background-size: cover; margin-top: 0; padding-top: 0;">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.5;"></div>
    <div class="container position-relative text-white text-center py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-3 fw-bold mb-4 animate__animated animate__fadeInDown">Découvrez la Forêt Urbaine de Paris</h1>
                <p class="lead mb-5 fs-4 animate__animated animate__fadeInUp">Explorez l'évolution des plantations d'arbres à travers la ville de Paris</p>
                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center animate__animated animate__fadeInUp">
                    <a href="#tree-chart" class="btn btn-success btn-lg px-5 py-3 rounded-pill shadow-sm">
                        <i class="fas fa-chart-line me-2"></i> Explorer les Données
                    </a>
                    <a href="pages/statistics.php" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill shadow-sm">
                        <i class="fas fa-chart-bar me-2"></i> Voir les Statistiques Détaillées
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="position-absolute bottom-0 start-50 translate-middle-x pb-4 animate__animated animate__bounce animate__infinite">
        <a href="#tree-chart" class="text-white">
            <i class="fas fa-chevron-down fa-2x"></i>
        </a>
    </div>
</div>

<!-- Section Graphique des Plantations d'Arbres -->
<div class="container py-5" id="tree-chart">
    <div class="row justify-content-center mb-4">
        <div class="col-md-10 text-center">
            <h2 class="text-success">Évolution des Plantations d'Arbres à Paris</h2>
            <p class="lead">Explorez comment les plantations d'arbres ont évolué au fil des années dans les différents arrondissements</p>
        </div>
    </div>
    
    <!-- Filtres -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Filtrer les Données</h5>
                    <form id="chartFilterForm" class="row g-3">
                        <div class="col-md-4">
                            <label for="arrondissement" class="form-label">Arrondissement</label>
                            <select class="form-select" id="arrondissement" name="arrondissement">
                                <option value="all" selected>Tous les arrondissements</option>
                                <?php foreach ($arrondissements as $arrond): ?>
                                <option value="<?php echo $arrond['id']; ?>"><?php echo htmlspecialchars($arrond['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="yearStart" class="form-label">Année de début</label>
                            <select class="form-select" id="yearStart" name="yearStart">
                                <?php for ($year = $minYear; $year <= $maxYear; $year++): ?>
                                <option value="<?php echo $year; ?>" <?php echo ($year == $minYear) ? 'selected' : ''; ?>><?php echo $year; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="yearEnd" class="form-label">Année de fin</label>
                            <select class="form-select" id="yearEnd" name="yearEnd">
                                <?php for ($year = $minYear; $year <= $maxYear; $year++): ?>
                                <option value="<?php echo $year; ?>" <?php echo ($year == $maxYear) ? 'selected' : ''; ?>><?php echo $year; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-filter me-2"></i> Appliquer les filtres
                            </button>
                            <button type="button" id="resetFilters" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-undo me-2"></i> Réinitialiser
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Canvas du Graphique -->
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Vérifiez que le canvas a une hauteur et une largeur suffisantes -->
                    <canvas id="treePlantingChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques en évidence -->
<div class="container py-5">
    <div class="row justify-content-center mb-5">
        <div class="col-md-8 text-center">
            <h2 class="text-success">Statistiques des Arbres à Paris</h2>
            <p class="lead">Chiffres clés sur la forêt urbaine</p>
        </div>
    </div>
    <div class="row text-center">
        <div class="col-md-3 mb-4">
            <div class="p-4 bg-white shadow-sm rounded-4 hover-lift transition-all h-100">
                <div class="stat-icon bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="fas fa-tree fa-2x"></i>
                </div>
                <h3 class="display-4 fw-bold text-success"><?php echo number_format($totalTrees); ?></h3>
                <p class="text-muted">Arbres Plantés</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="p-4 bg-white shadow-sm rounded-4 hover-lift transition-all h-100">
                <div class="stat-icon bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="fas fa-map-marker-alt fa-2x"></i>
                </div>
                <h3 class="display-4 fw-bold text-success"><?php echo $totalArrond; ?></h3>
                <p class="text-muted">Arrondissements</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="p-4 bg-white shadow-sm rounded-4 hover-lift transition-all h-100">
                <div class="stat-icon bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="fas fa-leaf fa-2x"></i>
                </div>
                <h3 class="display-4 fw-bold text-success"><?php echo $totalSpecies; ?></h3>
                <p class="text-muted">Types de Projets</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="p-4 bg-white shadow-sm rounded-4 hover-lift transition-all h-100">
                <div class="stat-icon bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="fas fa-seedling fa-2x"></i>
                </div>
                <h3 class="display-4 fw-bold text-success"><?php echo $totalProjects; ?></h3>
                <p class="text-muted">Projets</p>
            </div>
        </div>
    </div>
</div>

<!-- Section Derniers Projets -->
<div class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-md-8 text-center">
                <h2 class="text-success">Derniers Projets de Plantation</h2>
                <p class="lead">Initiatives récentes pour augmenter la forêt urbaine de Paris</p>
            </div>
        </div>
        <div class="row">
            <?php foreach($latestProjects as $project): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0 rounded-3 hover-shadow transition-all">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-success rounded-pill px-3 py-2"><?php echo htmlspecialchars($project['arrond_name']); ?></span>
                            <span class="text-muted small"><?php echo date('Y', strtotime($project['date_fin'])); ?></span>
                        </div>
                        <h5 class="card-title text-success"><?php echo htmlspecialchars($project['nom_projet']); ?></h5>
                        <p class="card-text text-muted">
                            <?php 
                            $description = $project['type_operation'] ?? 'Projet de plantation d\'arbres à Paris';
                            echo htmlspecialchars($description); 
                            ?>
                        </p>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <span class="badge bg-light text-success border border-success">
                                <i class="fas fa-tree me-1"></i> <?php echo number_format($project['nombre_arbres_plantes']); ?> Arbres
                            </span>
                            <span class="badge bg-light text-success border border-success">
                                <i class="fas fa-calendar-alt me-1"></i> <?php echo date('M Y', strtotime($project['date_fin'])); ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="pages/project-details.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-outline-success rounded-pill">
                            <i class="fas fa-info-circle me-1"></i> En savoir plus
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="pages/projects.php" class="btn btn-success rounded-pill px-4 py-2">
                <i class="fas fa-list me-2"></i> Voir tous les projets
            </a>
        </div>
    </div>
</div>

<!-- Appel à l'Action -->
<div class="bg-success text-white text-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="mb-3">Vous souhaitez en savoir plus sur les arbres à Paris ?</h2>
                <p class="lead mb-4">Explorez nos statistiques détaillées et nos projets de plantation</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="pages/statistics.php" class="btn btn-light btn-lg px-4 rounded-pill">
                        <i class="fas fa-chart-bar me-2"></i> Voir les Statistiques
                    </a>
                    <a href="pages/projects.php" class="btn btn-outline-light btn-lg px-4 rounded-pill">
                        <i class="fas fa-seedling me-2"></i> Explorer les Projets
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript pour le graphique et les filtres -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des variables
    let treePlantingChart;
    const chartYears = <?php echo json_encode($chartYears); ?>;
    const chartTrees = <?php echo json_encode($chartTrees); ?>;
    
    // Fonction pour créer le graphique
    function createChart(years, trees) {
        const ctx = document.getElementById('treePlantingChart').getContext('2d');
        
        // Détruire le graphique existant s'il y en a un
        if (treePlantingChart) {
            treePlantingChart.destroy();
        }
        
        // Créer un nouveau graphique en ligne au lieu d'un graphique à barres
        treePlantingChart = new Chart(ctx, {
            type: 'line', // Changer le type de graphique en 'line'
            data: {
                labels: years,
                datasets: [{
                    label: 'Nombre d\'arbres plantés',
                    data: trees,
                    backgroundColor: 'rgba(25, 135, 84, 0.2)', // Couleur de remplissage sous la ligne
                    borderColor: 'rgba(25, 135, 84, 1)', // Couleur de la ligne
                    borderWidth: 2, // Épaisseur de la ligne
                    pointBackgroundColor: 'rgba(25, 135, 84, 1)', // Couleur des points
                    pointBorderColor: '#fff', // Bordure des points
                    pointRadius: 5, // Taille des points
                    pointHoverRadius: 7, // Taille des points au survol
                    fill: true, // Remplir l'espace sous la ligne
                    tension: 0.3 // Ajouter une légère courbe à la ligne (0 = ligne droite, 1 = très courbé)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12, // Réduire la taille de la légende
                            font: {
                                size: 12 // Réduire la taille de la police
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Évolution des plantations d\'arbres par année',
                        font: {
                            size: 14 // Réduire la taille du titre
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw.toLocaleString('fr-FR');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nombre d\'arbres',
                            font: {
                                size: 12 // Réduire la taille du texte
                            }
                        },
                        ticks: {
                            font: {
                                size: 11 // Réduire la taille des graduations
                            },
                            callback: function(value) {
                                return value.toLocaleString('fr-FR');
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Année',
                            font: {
                                size: 12 // Réduire la taille du texte
                            }
                        },
                        ticks: {
                            font: {
                                size: 11 // Réduire la taille des graduations
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Créer le graphique initial
    createChart(chartYears, chartTrees);
});
</script>

<!-- Ajoutez ce script juste avant l'inclusion du footer -->
<script>
// Script pour l'effet de défilement de la navbar
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    
    function checkScroll() {
        if (window.scrollY > 50) {
            navbar.classList.add('navbar-scrolled');
            navbar.classList.remove('navbar-transparent');
        } else {
            navbar.classList.add('navbar-transparent');
            navbar.classList.remove('navbar-scrolled');
        }
    }
    
    // Vérification initiale
    checkScroll();
    
    // Vérification au défilement
    window.addEventListener('scroll', checkScroll);
    
    // Défilement fluide pour les liens d'ancrage
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
});
</script>

<?php include 'footer.php'; ?>
