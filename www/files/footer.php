</main>
    <!-- Pied de page -->
    <footer class="bg-success bg-gradient text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-tree fa-2x text-white me-2"></i>
                        <h5 class="mb-0 fw-bold">Les Arbres à Paris</h5>
                    </div>
                    <p class="text-white text-opacity-75">Explorer et documenter la forêt urbaine de Paris, un arbre à la fois. Notre mission est de sensibiliser à l'importance des arbres urbains.</p>
                    <div class="mt-4">
                        <a href="https://github.com/MonsoonD" class="btn btn-outline-light btn-sm rounded-circle" target="_blank" title="GitHub">
                            <i class="fab fa-github"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="fw-bold mb-3 text-white">Liens Rapides</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="<?php echo (strpos($_SERVER['REQUEST_URI'], 'pages/') !== false) ? '.index.php' : 'index.php'; ?>" class="text-white text-opacity-75 text-decoration-none hover-bright">
                                <i class="fas fa-home me-2"></i> Accueil
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo (strpos($_SERVER['REQUEST_URI'], 'pages/') !== false) ? 'about.php' : 'about.php'; ?>" class="text-white text-opacity-75 text-decoration-none hover-bright">
                                <i class="fas fa-info-circle me-2"></i> À Propos du Projet
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="fw-bold mb-3 text-white">Contactez-Nous</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="mailto:contact@arbres-paris.org" class="text-white text-opacity-75 text-decoration-none hover-bright">
                                <i class="fas fa-envelope me-2"></i>contact@arbres-paris.org
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="tel:+33123456789" class="text-white text-opacity-75 text-decoration-none hover-bright">
                                <i class="fas fa-phone me-2"></i>+33 (0)1 23 45 67 89
                            </a>
                        </li>
                        <li>
                            <a href="https://goo.gl/maps/Paris" class="text-white text-opacity-75 text-decoration-none hover-bright" target="_blank">
                                <i class="fas fa-map-marker-alt me-2"></i>Paris, France
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 border-light opacity-25">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="small text-white text-opacity-75 mb-0">© <?php echo date('Y'); ?> Les Arbres à Paris. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="small text-white text-opacity-75 mb-0">Source des données : <a href="https://opendata.paris.fr" class="text-white" target="_blank">Paris Open Data</a></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle avec Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript personnalisé -->
    <script src="<?php echo (strpos($_SERVER['REQUEST_URI'], 'pages/') !== false) ? '../assets/js/main.js' : 'assets/js/main.js'; ?>"></script>
    
    <!-- Ajout d'effets de survol pour les cartes et liens du pied de page -->
    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }
        
        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }
        
        .hover-lift {
            transition: all 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-5px);
        }
        
        .transition-all {
            transition: all 0.3s ease;
        }
        
        .rounded-4 {
            border-radius: 0.75rem !important;
        }
        
        /* Effet de survol des liens du pied de page */
        .hover-bright {
            transition: all 0.3s ease;
        }
        
        .hover-bright:hover {
            opacity: 1 !important;
            text-decoration: underline !important;
        }
        
        /* Ajout de classes d'animation */
        .animate__animated {
            animation-duration: 1s;
            animation-fill-mode: both;
        }
        
        .animate__fadeInDown {
            animation-name: fadeInDown;
        }
        
        .animate__fadeInUp {
            animation-name: fadeInUp;
        }
        
        .animate__bounce {
            animation-name: bounce;
            transform-origin: center bottom;
        }
        
        .animate__infinite {
            animation-iteration-count: infinite;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -50px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 50px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        
        @keyframes bounce {
            from, 20%, 53%, 80%, to {
                animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                transform: translate3d(0, 0, 0);
            }
            40%, 43% {
                animation-timing-function: cubic-bezier(0.755, 0.05, 0.855, 0.06);
                transform: translate3d(0, -15px, 0);
            }
            70% {
                animation-timing-function: cubic-bezier(0.755, 0.05, 0.855, 0.06);
                transform: translate3d(0, -10px, 0);
            }
            90% {
                transform: translate3d(0, -4px, 0);
            }
        }
        
        /* Styles spécifiques au pied de page */
        footer {
            background: linear-gradient(to right, #198754, #20c997);
        }
        
        footer a.btn-outline-light:hover {
            background-color: white;
            color: #198754;
        }
    </style>
    
    <!-- Script d'animation au défilement -->
    <script>
        // Animation simple au défilement
        document.addEventListener('DOMContentLoaded', function() {
            const animateElements = document.querySelectorAll('.card, .feature-icon-square, .stat-icon');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });
            
            animateElements.forEach(element => {
                observer.observe(element);
            });
        });
    </script>
</body>
</html>
