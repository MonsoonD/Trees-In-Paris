<?php
// Vérification de l'authentification admin
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit;
}

// Connexion à la base de données
require_once __DIR__ . '/../includes/dbConnect.php';

// Récupérer la liste des tables
$tablesQuery = $db->query("SHOW TABLES");
$tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);

// Déterminer la table à afficher (par défaut: première table)
$currentTable = isset($_GET['table']) ? $_GET['table'] : $tables[0] ?? '';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
$offset = ($page - 1) * $perPage;

// Recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchColumn = isset($_GET['search_column']) ? $_GET['search_column'] : '';

// Tri
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sortOrder = isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'DESC' : 'ASC';

// Récupérer les colonnes de la table actuelle
$columns = [];
if (!empty($currentTable)) {
    $columnsQuery = $db->query("SHOW COLUMNS FROM `$currentTable`");
    $columns = $columnsQuery->fetchAll(PDO::FETCH_COLUMN);
}

// Construire la requête SQL
$sql = "SELECT * FROM `$currentTable`";
$countSql = "SELECT COUNT(*) as total FROM `$currentTable`";

// Ajouter la condition de recherche si nécessaire
if (!empty($search) && !empty($searchColumn) && in_array($searchColumn, $columns)) {
    $sql .= " WHERE `$searchColumn` LIKE :search";
    $countSql .= " WHERE `$searchColumn` LIKE :search";
}

// Ajouter le tri
if (in_array($sortColumn, $columns)) {
    $sql .= " ORDER BY `$sortColumn` $sortOrder";
}

// Ajouter la pagination
$sql .= " LIMIT :offset, :perPage";

// Exécuter la requête de comptage
$countStmt = $db->prepare($countSql);
if (!empty($search) && !empty($searchColumn)) {
    $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$countStmt->execute();
$totalRows = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRows / $perPage);

// Exécuter la requête principale
$stmt = $db->prepare($sql);
if (!empty($search) && !empty($searchColumn)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement des actions (suppression, édition)
$message = '';
$messageType = '';

// Suppression d'un enregistrement
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    try {
        $deleteStmt = $db->prepare("DELETE FROM `$currentTable` WHERE id = :id");
        $deleteStmt->bindValue(':id', $id, PDO::PARAM_INT);
        $deleteStmt->execute();
        $message = "Enregistrement #$id supprimé avec succès.";
        $messageType = "success";
        
        // Rediriger pour éviter la résoumission du formulaire
        header("Location: manage-data.php?table=$currentTable&page=$page&per_page=$perPage&sort=$sortColumn&order=$sortOrder&message=$message&message_type=$messageType");
        exit;
    } catch (PDOException $e) {
        $message = "Erreur lors de la suppression: " . $e->getMessage();
        $messageType = "danger";
    }
}

// Récupérer les messages de la redirection
if (isset($_GET['message'])) {
    $message = $_GET['message'];
    $messageType = $_GET['message_type'] ?? 'info';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des données - Trees In Paris</title>
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
        .table-container {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            padding: 1.5rem;
        }
        .table th {
            position: relative;
        }
        .sort-icon {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
        }
        .pagination {
            margin-bottom: 0;
        }
        .actions-column {
            width: 120px;
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
                            <a class="nav-link active" href="manage-data.php">
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
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Gestion des données</h1>
                </div>
                
                <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Sélection de table -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-table me-2"></i> Sélectionner une table</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-select" id="tableSelect" onchange="changeTable(this.value)">
                                    <?php foreach ($tables as $table): ?>
                                    <option value="<?php echo $table; ?>" <?php echo ($table === $currentTable) ? 'selected' : ''; ?>>
                                        <?php echo $table; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <form action="manage-data.php" method="get" class="d-flex">
                                    <input type="hidden" name="table" value="<?php echo $currentTable; ?>">
                                    <input type="hidden" name="page" value="1">
                                    <input type="hidden" name="per_page" value="<?php echo $perPage; ?>">
                                    <input type="hidden" name="sort" value="<?php echo $sortColumn; ?>">
                                    <input type="hidden" name="order" value="<?php echo $sortOrder; ?>">
                                    
                                    <select name="search_column" class="form-select me-2">
                                        <option value="">Colonne...</option>
                                        <?php foreach ($columns as $column): ?>
                                        <option value="<?php echo $column; ?>" <?php echo ($column === $searchColumn) ? 'selected' : ''; ?>>
                                            <?php echo $column; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" name="search" class="form-control me-2" placeholder="Rechercher..." value="<?php echo htmlspecialchars($search); ?>">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tableau des données -->
                <div class="table-container mb-4">
                    <?php if (empty($currentTable)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Veuillez sélectionner une table pour afficher les données.
                    </div>
                    <?php elseif (empty($rows)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Aucune donnée trouvée dans la table "<?php echo $currentTable; ?>".
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <?php foreach ($columns as $column): ?>
                                    <th>
                                        <a href="manage-data.php?table=<?php echo $currentTable; ?>&page=<?php echo $page; ?>&per_page=<?php echo $perPage; ?>&sort=<?php echo $column; ?>&order=<?php echo ($column === $sortColumn && $sortOrder === 'ASC') ? 'desc' : 'asc'; ?>&search=<?php echo urlencode($search); ?>&search_column=<?php echo urlencode($searchColumn); ?>" class="text-decoration-none text-dark">
                                            <?php echo $column; ?>
                                            <?php if ($column === $sortColumn): ?>
                                            <span class="sort-icon">
                                                <i class="fas fa-sort-<?php echo ($sortOrder === 'ASC') ? 'up' : 'down'; ?>"></i>
                                            </span>
                                            <?php endif; ?>
                                        </a>
                                    </th>
                                    <?php endforeach; ?>
                                    <th class="actions-column">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $row): ?>
                                <tr>
                                    <?php foreach ($columns as $column): ?>
                                    <td>
                                        <?php 
                                        // Limiter l'affichage des valeurs longues
                                        $value = $row[$column] ?? ''; // Utiliser l'opérateur de fusion null pour fournir une chaîne vide par défaut
                                        if (is_string($value) && strlen($value) > 100) {
                                            echo htmlspecialchars(substr($value, 0, 100)) . '...';
                                        } else {
                                            echo htmlspecialchars((string)$value); // Convertir explicitement en chaîne
                                        }
                                        ?>
                                    </td>
                                    <?php endforeach; ?>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="edit-record.php?table=<?php echo $currentTable; ?>&id=<?php echo $row['id']; ?>" class="btn btn-outline-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" title="Supprimer" 
                                                    onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo addslashes($row[$columns[1]] ?? $row['id']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            Affichage de <?php echo min($offset + 1, $totalRows); ?> à <?php echo min($offset + count($rows), $totalRows); ?> sur <?php echo $totalRows; ?> enregistrements
                        </div>
                        <div>
                            <nav aria-label="Pagination">
                                <ul class="pagination">
                                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="manage-data.php?table=<?php echo $currentTable; ?>&page=<?php echo $page - 1; ?>&per_page=<?php echo $perPage; ?>&sort=<?php echo $sortColumn; ?>&order=<?php echo $sortOrder; ?>&search=<?php echo urlencode($search); ?>&search_column=<?php echo urlencode($searchColumn); ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                    
                                    <?php
                                    // Afficher un nombre limité de pages
                                    $startPage = max(1, $page - 2);
                                    $endPage = min($totalPages, $page + 2);
                                    
                                    // Toujours afficher la première page
                                    if ($startPage > 1) {
                                        echo '<li class="page-item">';
                                        echo '<a class="page-link" href="manage-data.php?table=' . $currentTable . '&page=1&per_page=' . $perPage . '&sort=' . $sortColumn . '&order=' . $sortOrder . '&search=' . urlencode($search) . '&search_column=' . urlencode($searchColumn) . '">1</a>';
                                        echo '</li>';
                                        
                                        if ($startPage > 2) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                    }
                                    
                                    // Pages autour de la page actuelle
                                    for ($i = $startPage; $i <= $endPage; $i++) {
                                        echo '<li class="page-item ' . (($i == $page) ? 'active' : '') . '">';
                                        echo '<a class="page-link" href="manage-data.php?table=' . $currentTable . '&page=' . $i . '&per_page=' . $perPage . '&sort=' . $sortColumn . '&order=' . $sortOrder . '&search=' . urlencode($search) . '&search_column=' . urlencode($searchColumn) . '">' . $i . '</a>';
                                        echo '</li>';
                                    }
                                    
                                    // Toujours afficher la dernière page
                                    if ($endPage < $totalPages) {
                                        if ($endPage < $totalPages - 1) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                        
                                        echo '<li class="page-item">';
                                        echo '<a class="page-link" href="manage-data.php?table=' . $currentTable . '&page=' . $totalPages . '&per_page=' . $perPage . '&sort=' . $sortColumn . '&order=' . $sortOrder . '&search=' . urlencode($search) . '&search_column=' . urlencode($searchColumn) . '">' . $totalPages . '</a>';
                                        echo '</li>';
                                    }
                                    ?>
                                    
                                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="manage-data.php?table=<?php echo $currentTable; ?>&page=<?php echo $page + 1; ?>&per_page=<?php echo $perPage; ?>&sort=<?php echo $sortColumn; ?>&order=<?php echo $sortOrder; ?>&search=<?php echo urlencode($search); ?>&search_column=<?php echo urlencode($searchColumn); ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        <div>
                            <select class="form-select" onchange="changePerPage(this.value)">
                                <option value="10" <?php echo ($perPage == 10) ? 'selected' : ''; ?>>10 par page</option>
                                <option value="25" <?php echo ($perPage == 25) ? 'selected' : ''; ?>>25 par page</option>
                                <option value="50" <?php echo ($perPage == 50) ? 'selected' : ''; ?>>50 par page</option>
                                <option value="100" <?php echo ($perPage == 100) ? 'selected' : ''; ?>>100 par page</option>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Informations sur la table -->
                <?php if (!empty($currentTable)): ?>
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Informations sur la table "<?php echo $currentTable; ?>"</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Structure de la table</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Colonne</th>
                                                <th>Type</th>
                                                <th>Null</th>
                                                <th>Clé</th>
                                                <th>Défaut</th>
                                                <th>Extra</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $structureQuery = $db->query("DESCRIBE `$currentTable`");
                                            $structure = $structureQuery->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($structure as $column):
                                            ?>
                                            <tr>
                                                <td><?php echo $column['Field']; ?></td>
                                                <td><?php echo $column['Type']; ?></td>
                                                <td><?php echo $column['Null']; ?></td>
                                                <td><?php echo $column['Key']; ?></td>
                                                <td><?php echo $column['Default'] ?? 'NULL'; ?></td>
                                                <td><?php echo $column['Extra']; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Statistiques</h6>
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Nombre total d'enregistrements
                                        <span class="badge bg-success rounded-pill"><?php echo $totalRows; ?></span>
                                    </li>
                                    <?php
                                    // Statistiques supplémentaires pour certaines colonnes
                                    foreach ($columns as $column) {
                                        // Ignorer les colonnes de type texte long pour les statistiques
                                        $skipTypes = ['text', 'longtext', 'mediumtext', 'blob', 'longblob', 'mediumblob'];
                                        $skipColumn = false;
                                        
                                        foreach ($structure as $col) {
                                            if ($col['Field'] === $column) {
                                                foreach ($skipTypes as $type) {
                                                    if (strpos(strtolower($col['Type']), $type) !== false) {
                                                        $skipColumn = true;
                                                        break;
                                                    }
                                                }
                                                break;
                                            }
                                        }
                                        
                                        if ($skipColumn) continue;
                                        
                                        // Statistiques pour les colonnes numériques
                                        if (strpos(strtolower($col['Type']), 'int') !== false || 
                                            strpos(strtolower($col['Type']), 'decimal') !== false || 
                                            strpos(strtolower($col['Type']), 'float') !== false || 
                                            strpos(strtolower($col['Type']), 'double') !== false) {
                                            
                                            $statQuery = $db->query("SELECT MIN(`$column`) as min, MAX(`$column`) as max, AVG(`$column`) as avg FROM `$currentTable`");
                                            $stat = $statQuery->fetch(PDO::FETCH_ASSOC);
                                            
                                            if ($stat['min'] !== null) {
                                                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                                echo "Min $column";
                                                echo '<span class="badge bg-secondary rounded-pill">' . number_format($stat['min'], 2) . '</span>';
                                                echo '</li>';
                                                
                                                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                                echo "Max $column";
                                                echo '<span class="badge bg-secondary rounded-pill">' . number_format($stat['max'], 2) . '</span>';
                                                echo '</li>';
                                                
                                                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                                echo "Moyenne $column";
                                                echo '<span class="badge bg-secondary rounded-pill">' . number_format($stat['avg'], 2) . '</span>';
                                                echo '</li>';
                                            }
                                        }
                                        
                                        // Valeurs distinctes pour les colonnes de type enum ou petites chaînes
                                        if (strpos(strtolower($col['Type']), 'enum') !== false || 
                                            (strpos(strtolower($col['Type']), 'varchar') !== false && intval(preg_replace('/[^0-9]/', '', $col['Type'])) <= 50)) {
                                            
                                            $distinctQuery = $db->query("SELECT COUNT(DISTINCT `$column`) as count FROM `$currentTable`");
                                            $distinct = $distinctQuery->fetch(PDO::FETCH_ASSOC)['count'];
                                            
                                            echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                            echo "Valeurs distinctes pour $column";
                                            echo '<span class="badge bg-secondary rounded-pill">' . $distinct . '</span>';
                                            echo '</li>';
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
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
                    <p>Êtes-vous sûr de vouloir supprimer l'enregistrement <strong id="deleteRecordName"></strong> ?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form action="manage-data.php" method="post" id="deleteForm">
                        <input type="hidden" name="table" value="<?php echo htmlspecialchars($currentTable); ?>">
                        <input type="hidden" name="id" id="deleteRecordId">
                        <input type="hidden" name="page" value="<?php echo $page; ?>">
                        <input type="hidden" name="per_page" value="<?php echo $perPage; ?>">
                        <input type="hidden" name="sort" value="<?php echo $sortColumn; ?>">
                        <input type="hidden" name="order" value="<?php echo $sortOrder; ?>">
                        <button type="submit" name="delete" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle avec Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Fonction pour changer de table
        function changeTable(table) {
            window.location.href = 'manage-data.php?table=' + table;
        }
        
        // Fonction pour changer le nombre d'éléments par page
        function changePerPage(perPage) {
            window.location.href = 'manage-data.php?table=<?php echo $currentTable; ?>&page=1&per_page=' + perPage + '&sort=<?php echo $sortColumn; ?>&order=<?php echo $sortOrder; ?>&search=<?php echo urlencode($search); ?>&search_column=<?php echo urlencode($searchColumn); ?>';
        }
        
        // Fonction pour confirmer la suppression
        function confirmDelete(id, name) {
            document.getElementById('deleteRecordId').value = id;
            document.getElementById('deleteRecordName').textContent = '#' + id + ' (' + name + ')';
            
            // Afficher la modal de confirmation
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
</body>
</html>
