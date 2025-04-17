<?php
// Vérifier si l'utilisateur est connecté en tant qu'admin
session_start();
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos - Trees In Paris</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'], 'files/') !== false) ? '../assets/css/style.css' : 'assets/css/style.css'; ?>">
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
        
        /* Nouvelle classe pour quand le header est au-dessus du contenu */
        .navbar-content-overlap {
            background-color: rgba(25, 135, 84, 0.95) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-scrolled.navbar-transparent {
            background-color: transparent !important;
            box-shadow: none;
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
            background: url('../img/bg.png') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            min-height: 100vh;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: -1;
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
            color: white !important;
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
        
        .content-container {
            position: relative;
            z-index: 1;
            padding-top: 80px;
            padding-bottom: 80px;
        }
        
        .about-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-transparent fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo (strpos($_SERVER['REQUEST_URI'], 'files/') !== false) ? '../index.php' : 'index.php'; ?>">
                <i class="fas fa-tree logo-icon me-2"></i>
                <span class="fw-bold">Trees In Paris</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Basculer la navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo (strpos($_SERVER['REQUEST_URI'], 'files/') !== false) ? '../index.php' : 'index.php'; ?>">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo (strpos($_SERVER['REQUEST_URI'], 'files/') !== false) ? 'about.php' : 'files/about.php'; ?>">À propos</a>
                    </li>
                    <?php if ($isAdmin): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-shield me-1"></i> Admin (<?php echo htmlspecialchars($_SESSION['admin_username']); ?>)
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="<?php echo (strpos($_SERVER['REQUEST_URI'], 'files/') !== false) ? '../admin/admin-index.php' : 'admin/admin-index.php'; ?>"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo (strpos($_SERVER['REQUEST_URI'], 'files/') !== false) ? '../admin/logout.php' : 'admin/logout.php'; ?>"><i class="fas fa-sign-out-alt me-2"></i>Se déconnecter</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link admin-link" href="<?php echo (strpos($_SERVER['REQUEST_URI'], 'files/') !== false) ? '../admin/login.php' : 'admin/login.php'; ?>">
                            <i class="fas fa-user-shield me-1"></i> Admin
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <main>