<?php
session_start();

// Rediriger si déjà connecté
if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header('Location: admin-index.php');
    exit;
}

// Initialiser les variables
$error = '';
$username = '';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require_once __DIR__ . '/../includes/dbConnect.php';
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Vérifier que les champs ne sont pas vides
        if (empty($username) || empty($password)) {
            $error = 'Veuillez remplir tous les champs.';
        } else {
            // Récupérer l'utilisateur depuis la base de données
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin && ($admin['role'] == 'admin' || $admin['role'] == 'super_admin') && password_verify($password, $admin['password'])) {
                // Connexion réussie
                $_SESSION['admin'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_role'] = $admin['role'];
                $_SESSION['admin_last_login'] = date('Y-m-d H:i:s');
                
                // Mettre à jour la date de dernière connexion
                $updateStmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$admin['id']]);
                
                // Rediriger vers le tableau de bord
                header('Location: admin-index.php');
                exit;
            } else {
                $error = 'Identifiants incorrects. Veuillez réessayer.';
            }
        }
    } catch (PDOException $e) {
        // En mode développement, afficher l'erreur réelle
        $error = 'Erreur de base de données: ' . $e->getMessage();
        // Log l'erreur pour l'administrateur
        error_log('Erreur de connexion: ' . $e->getMessage());
    } catch (Exception $e) {
        $error = 'Une erreur inattendue est survenue. Veuillez réessayer plus tard.';
        error_log('Erreur générale: ' . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - Trees In Paris</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .card-header {
            background-color: #198754;
            color: white;
            text-align: center;
            padding: 2rem 1.5rem;
            position: relative;
        }
        .card-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20px;
            background: linear-gradient(to right bottom, transparent 49%, white 50%);
        }
        .login-icon {
            font-size: 60px;
            margin-bottom: 15px;
        }
        .btn-success {
            background-color: #198754;
            border-color: #198754;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-success:hover {
            background-color: #157347;
            border-color: #157347;
        }
        .form-control {
            padding: 12px;
            border-radius: 8px;
        }
        .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
        }
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }
        .form-control {
            border-left: none;
        }
        .password-toggle {
            cursor: pointer;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .back-link:hover {
            transform: translateX(-5px);
            color: white;
        }
        .alert {
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <a href="../index.php" class="back-link mb-4 d-block">
            <i class="fas fa-arrow-left me-2"></i> Retour au site
        </a>
        
        <div class="card">
            <div class="card-header">
                <i class="fas fa-tree login-icon"></i>
                <h2 class="mb-0">Administration</h2>
                <p class="mb-0 fs-5">Trees In Paris</p>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <h4 class="text-center mb-4">Connexion</h4>
                
                <form action="login.php" method="post">
                    <div class="mb-4">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required autofocus>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <span class="input-group-text password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Se souvenir de moi</label>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i> Se connecter
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center bg-white py-3">
                <a href="#" class="text-muted text-decoration-none small">Mot de passe oublié ?</a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle avec Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
