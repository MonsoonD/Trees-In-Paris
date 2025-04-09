<?php include 'header.php'; ?>
<?php require_once '../../includes/dbConnect.php';

$stmt = $db->query("SELECT * FROM planting_projects LIMIT 10");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC); 

// Récupérer les statistiques globales
$totalTreesStmt = $db->query("SELECT SUM(nombre_arbres_plantes) as total FROM planting_projects");
$totalTrees = $totalTreesStmt->fetch()['total'] ?? '200K+';

$totalProjectsStmt = $db->query("SELECT COUNT(*) as total FROM planting_projects");
$totalProjects = $totalProjectsStmt->fetch()['total'] ?? '50+';

$arrondStmt = $db->query("SELECT COUNT(DISTINCT arrondissement_id) as total FROM planting_projects");
$totalArrond = $arrondStmt->fetch()['total'] ?? '20';

$speciesStmt = $db->query("SELECT COUNT(*) as total FROM (SELECT DISTINCT nom_projet FROM planting_projects) as temp");
$totalSpecies = $speciesStmt->fetch()['total'] ?? '150+';

// Récupérer les derniers projets
$latestProjectsStmt = $db->query("SELECT p.*, a.name as arrond_name 
                                 FROM planting_projects p 
                                 JOIN arrondissements a ON p.arrondissement_id = a.id 
                                 ORDER BY p.date_fin DESC LIMIT 2");
$latestProjects = $latestProjectsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Section Héro avec Image de Fond -->
<div class="hero-section position-relative d-flex align-items-center" style="min-height: 100vh; background: url('../img/bg.png') no-repeat center center; background-size: cover;">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.5;"></div>
    <div class="container position-relative text-white text-center py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-3 fw-bold mb-4 animate__animated animate__fadeInDown">Découvrez la Forêt Urbaine de Paris</h1>
                <p class="lead mb-5 fs-4 animate__animated animate__fadeInUp">Explorez l'évolution des plantations d'arbres à travers la ville de Paris</p>
                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center animate__animated animate__fadeInUp">
                    <a href="/pages/map.php" class="btn btn-success btn-lg px-5 py-3 rounded-pill shadow-sm">
                        <i class="fas fa-map-marked-alt me-2"></i> Explorer la Carte
                    </a>
                    <a href="/pages/statistics.php" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill shadow-sm">
                        <i class="fas fa-chart-bar me-2"></i> Voir les Statistiques
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="position-absolute bottom-0 start-50 translate-middle-x pb-4 animate__animated animate__bounce animate__infinite">
        <a href="#features" class="text-white">
            <i class="fas fa-chevron-down fa-2x"></i>
        </a>
    </div>
</div>

<!-- Section Fonctionnalités -->
<div class="container px-4 py-5" id="features">
    <h2 class="pb-2 border-bottom text-success text-center mb-5">Explorez Nos Fonctionnalités</h2>
    <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
        <div class="col d-flex align-items-start">
            <div class="feature-icon-square bg-success text-white flex-shrink-0 me-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 64px; height: 64px;">
                <i class="fas fa-map-marked-alt fa-2x"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-3 fs-4 text-success">Carte Interactive</h3>
                <p class="text-muted">Explorez les arbres dans les 20 arrondissements de Paris avec notre carte interactive. Découvrez la répartition de la forêt urbaine à travers la ville.</p>
                <a href="/pages/map.php" class="btn btn-outline-success rounded-pill mt-2">
                    <i class="fas fa-arrow-right me-2"></i> Voir la Carte
                </a>
            </div>
        </div>
        <div class="col d-flex align-items-start">
            <div class="feature-icon-square bg-success text-white flex-shrink-0 me-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 64px; height: 64px;">
                <i class="fas fa-chart-bar fa-2x"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-3 fs-4 text-success">Statistiques des Arbres</h3>
                <p class="text-muted">Découvrez les tendances de plantation et la répartition au fil du temps. Analysez comment la forêt urbaine de Paris a évolué au cours des années.</p>
                <a href="/pages/statistics.php" class="btn btn-outline-success rounded-pill mt-2">
                    <i class="fas fa-arrow-right me-2"></i> Voir les Statistiques
                </a>
            </div>
        </div>
        <div class="col d-flex align-items-start">
            <div class="feature-icon-square bg-success text-white flex-shrink-0 me-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 64px; height: 64px;">
                <i class="fas fa-leaf fa-2x"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-3 fs-4 text-success">Projets de Plantation</h3>
                <p class="text-muted">Découvrez les diverses initiatives de plantation d'arbres qui composent la canopée urbaine de Paris et contribuent à la biodiversité de la ville.</p>
                <a href="/pages/projects.php" class="btn btn-outline-success rounded-pill mt-2">
                    <i class="fas fa-arrow-right me-2"></i> Explorer les Projets
                </a>
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
                        <a href="/pages/project-details.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-outline-success rounded-pill">
                            <i class="fas fa-info-circle me-1"></i> En savoir plus
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="/pages/projects.php" class="btn btn-success rounded-pill px-4 py-2">
                <i class="fas fa-list me-2"></i> Voir tous les projets
            </a>
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

<!-- Section Chronologie -->
<div class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-md-8 text-center">
                <h2 class="text-success">Chronologie de la Végétalisation de Paris</h2>
                <p class="lead">L'évolution des initiatives de plantation d'arbres à Paris</p>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="timeline position-relative">
                    <!-- Éléments de la chronologie -->
                    <div class="timeline-item mb-5 position-relative ps-5">
                        <div class="timeline-marker position-absolute start-0 top-0 bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-leaf text-white"></i>
                        </div>
                        <div class="timeline-content bg-white p-4 rounded-3 shadow-sm">
                            <h4 class="text-success">2018-2020 : Initiative Forêt Urbaine</h4>
                            <p class="text-muted">Paris a lancé une initiative majeure pour augmenter la couverture arborée dans tous les arrondissements, en mettant l'accent sur la création de micro-forêts urbaines.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item mb-5 position-relative ps-5">
                        <div class="timeline-marker position-absolute start-0 top-0 bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-tree text-white"></i>
                        </div>
                        <div class="timeline-content bg-white p-4 rounded-3 shadow-sm">
                            <h4 class="text-success">2020-2022 : Projets de Végétalisation de Quartiers</h4>
                            <p class="text-muted">L'accent a été mis sur des projets au niveau des quartiers, avec la participation des communautés dans la sélection des espèces d'arbres et des lieux de plantation.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item position-relative ps-5">
                        <div class="timeline-marker position-absolute start-0 top-0 bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-seedling text-white"></i>
                        </div>
                        <div class="timeline-content bg-white p-4 rounded-3 shadow-sm">
                            <h4 class="text-success">2022-Présent : Amélioration de la Biodiversité</h4>
                            <p class="text-muted">Les projets actuels se concentrent sur l'augmentation de la biodiversité, avec la sélection d'espèces indigènes et la création de corridors écologiques à travers la ville.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Appel à l'Action -->
<div class="bg-success text-white text-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="mb-3">Vous souhaitez en savoir plus sur les arbres à Paris ?</h2>
                <p class="lead mb-4">Explorez notre carte interactive et nos statistiques détaillées</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="/pages/map.php" class="btn btn-light btn-lg px-4 rounded-pill">
                        <i class="fas fa-map-marked-alt me-2"></i> Explorer la Carte
                    </a>
                    <a href="/pages/statistics.php" class="btn btn-outline-light btn-lg px-4 rounded-pill">
                        <i class="fas fa-chart-bar me-2"></i> Voir les Statistiques
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ajout de JavaScript personnalisé pour l'effet de défilement de la barre de navigation -->
<script>
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