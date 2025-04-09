<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les Arbres à Paris</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        /* Styles personnalisés pour l'en-tête transparent */
        .navbar-transparent {
            background-color: transparent !important;
            transition: background-color 0.3s ease;
            box-shadow: none;
        }
        
        .navbar-scrolled {
            background-color: rgba(25, 135, 84, 0.95) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .logo-icon {
            font-size: 2rem;
            color: #fff;
            transition: all 0.3s ease;
        }
        
        .navbar-scrolled .logo-icon {
            font-size: 1.75rem;
        }
        
        body {
            padding-top: 0;
        }
        
        .navbar {
            padding-top: 15px;
            padding-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .navbar-scrolled {
            padding-top: 10px;
            padding-bottom: 10px;
        }
        
        .nav-link {
            font-weight: 500;
            position: relative;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #fff;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-transparent fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/index.php">
                <i class="fas fa-tree logo-icon me-2"></i>
                <span class="fw-bold">Les Arbres à Paris</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Basculer la navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" 
                           href="/index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'map.php') ? 'active' : ''; ?>" 
                           href="/pages/map.php">Carte</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'statistics.php') ? 'active' : ''; ?>" 
                           href="/pages/statistics.php">Statistiques</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>" 
                           href="/pages/about.php">À propos</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main>
