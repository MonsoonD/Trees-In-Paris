<?php include 'header.php'; ?>
<?php require_once '../../includes/dbConnect.php';

$stmt = $db->query("SELECT * FROM planting_projects LIMIT 10");
$trees = $stmt->fetchAll(PDO::FETCH_ASSOC);; ?>

<!-- Hero Section -->
<div class="bg-success text-white text-center py-5 mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold">Discover Paris's Urban Forest</h1>
                <p class="lead mb-4">Explore the evolution of tree planting across the city of Paris</p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="pages/map.php" class="btn btn-light btn-lg px-4 me-sm-3">Explore Map</a>
                    <a href="pages/statistics.php" class="btn btn-outline-light btn-lg px-4">View Statistics</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="container px-4 py-5" id="features">
    <h2 class="pb-2 border-bottom text-success">Explore Our Features</h2>
    <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
        <div class="col d-flex align-items-start">
            <div class="icon-square bg-success text-white flex-shrink-0 me-3">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0 fs-4">Interactive Map</h3>
                <p>Explore trees across all 20 arrondissements of Paris with our interactive map.</p>
                <a href="pages/map.php" class="btn btn-success">
                    View Map
                </a>
            </div>
        </div>
        <div class="col d-flex align-items-start">
            <div class="icon-square bg-success text-white flex-shrink-0 me-3">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0 fs-4">Tree Statistics</h3>
                <p>Discover trends in tree planting and species distribution over time.</p>
                <a href="pages/statistics.php" class="btn btn-success">
                    View Statistics
                </a>
            </div>
        </div>
        <div class="col d-flex align-items-start">
            <div class="icon-square bg-success text-white flex-shrink-0 me-3">
                <i class="fas fa-leaf"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0 fs-4">Species Guide</h3>
                <p>Learn about the diverse tree species that make up Paris's urban canopy.</p>
                <a href="pages/species.php" class="btn btn-success">
                    Explore Species
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Latest Projects Section -->
<div class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-md-8 text-center">
                <h2 class="text-success">Latest Tree Planting Projects</h2>
                <p class="lead">Recent initiatives to increase Paris's urban forest</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-success">16th Arrondissement Greening Project</h5>
                        <h6 class="card-subtitle mb-2 text-muted">2022</h6>
                        <p class="card-text">Over 200 new trees planted along major boulevards, focusing on native species.</p>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <span class="badge bg-success">120 Oak Trees</span>
                            <span class="badge bg-success">80 Maple Trees</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="#" class="btn btn-sm btn-outline-success">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-success">Seine Riverbank Restoration</h5>
                        <h6 class="card-subtitle mb-2 text-muted">2021</h6>
                        <p class="card-text">Restoration of natural habitats along the Seine with 150 new trees planted.</p>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <span class="badge bg-success">70 Willow Trees</span>
                            <span class="badge bg-success">80 Alder Trees</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="#" class="btn btn-sm btn-outline-success">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="pages/projects.php" class="btn btn-success">View All Projects</a>
        </div>
    </div>
</div>

<!-- Statistics Highlight -->
<div class="container py-5">
    <div class="row justify-content-center mb-5">
        <div class="col-md-8 text-center">
            <h2 class="text-success">Paris Tree Statistics</h2>
            <p class="lead">Key numbers about the urban forest</p>
        </div>
    </div>
    <div class="row text-center">
        <div class="col-md-3 mb-4">
            <div class="p-3 bg-white shadow-sm rounded">
                <h3 class="display-4 fw-bold text-success">200K+</h3>
                <p class="text-muted">Trees in Paris</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="p-3 bg-white shadow-sm rounded">
                <h3 class="display-4 fw-bold text-success">20</h3>
                <p class="text-muted">Arrondissements</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="p-3 bg-white shadow-sm rounded">
                <h3 class="display-4 fw-bold text-success">150+</h3>
                <p class="text-muted">Species</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="p-3 bg-white shadow-sm rounded">
                <h3 class="display-4 fw-bold text-success">50+</h3>
                <p class="text-muted">Projects</p>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="bg-success text-white text-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2>Want to learn more about trees in Paris?</h2>
                <p class="lead mb-4">Explore our interactive map and detailed statistics</p>
                <a href="pages/map.php" class="btn btn-light btn-lg px-4">Start Exploring</a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
