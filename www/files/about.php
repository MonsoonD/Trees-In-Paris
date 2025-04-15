<?php include 'header-about.php'; ?>

<div class="content-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="about-card p-5">
                    <h1 class="text-center text-success mb-5">Trees-In-Paris</h1>
                    
                    <!-- Version française -->
                    <div class="mb-5">
                        <h2 class="border-bottom pb-2 text-success">🇫🇷 Français</h2>
                        
                        <h3 class="mt-4 text-dark">À propos</h3>
                        <p class="text-dark">Un site web interactif permettant d'explorer et de visualiser les données sur les arbres plantés à Paris. Cette application web offre une interface intuitive pour découvrir l'évolution de la forêt urbaine parisienne à travers des cartes interactives, des statistiques détaillées et des informations sur les projets de plantation.</p>
                        
                        <h3 class="mt-4 text-dark">Fonctionnalités</h3>
                        <ul class="text-dark">
                            <li><strong>Carte interactive</strong> : Explorez la répartition des arbres dans les 20 arrondissements de Paris</li>
                            <li><strong>Statistiques détaillées</strong> : Visualisez les tendances de plantation d'arbres au fil du temps avec des graphiques dynamiques</li>
                            <li><strong>Filtrage avancé</strong> : Filtrez les données par arrondissement et par année pour une analyse personnalisée</li>
                            <li><strong>Projets de plantation</strong> : Découvrez les initiatives récentes qui contribuent à l'augmentation de la canopée urbaine</li>
                        </ul>
                        
                        <h3 class="mt-4 text-dark">Technologies utilisées</h3>
                        <ul class="text-dark">
                            <li><strong>Langages</strong> : HTML/CSS, JavaScript, PHP, SQL</li>
                            <li><strong>Bibliothèques</strong> : Bootstrap 5, Chart.js, Font Awesome</li>
                            <li><strong>Base de données</strong> : MySQL</li>
                            <li><strong>Convention de nommage</strong> : camelCase</li>
                        </ul>
                        
                        <h3 class="mt-4 text-dark">Source des données</h3>
                        <p class="text-dark">Les données utilisées dans cette application proviennent du portail Open Data de la Ville de Paris :</p>
                        <p><a href="https://opendata.paris.fr/explore/dataset/arbres-plantes-par-projet/" target="_blank" class="text-success">https://opendata.paris.fr/explore/dataset/arbres-plantes-par-projet/</a></p>
                        
                        <h3 class="mt-4 text-dark">Installation</h3>
                        <ol class="text-dark">
                            <li>Clonez ce dépôt</li>
                            <li>Configurez votre serveur web (Apache, Nginx) pour pointer vers le dossier <code>www</code></li>
                            <li>Importez la base de données à partir des fichiers SQL dans le dossier <code>admin/database</code></li>
                            <li>Configurez les paramètres de connexion à la base de données dans le fichier <code>.env</code></li>
                            <li>Accédez à l'application via votre navigateur</li>
                        </ol>
                    </div>
                    
                    <div class="section-divider"></div>
                    
                    <!-- Version anglaise -->
                    <div>
                        <h2 class="border-bottom pb-2 text-success">🇬🇧 English</h2>
                        
                        <h3 class="mt-4 text-dark">About</h3>
                        <p class="text-dark">An interactive website that allows users to explore and visualize data about trees planted in Paris. This web application offers an intuitive interface to discover the evolution of Paris's urban forest through interactive maps, detailed statistics, and information about planting projects.</p>
                        
                        <h3 class="mt-4 text-dark">Features</h3>
                        <ul class="text-dark">
                            <li><strong>Interactive Map</strong>: Explore tree distribution across all 20 arrondissements of Paris</li>
                            <li><strong>Detailed Statistics</strong>: Visualize tree planting trends over time with dynamic charts</li>
                            <li><strong>Advanced Filtering</strong>: Filter data by district and year for customized analysis</li>
                            <li><strong>Planting Projects</strong>: Discover recent initiatives contributing to the increase of urban tree canopy</li>
                        </ul>
                        
                        <h3 class="mt-4 text-dark">Technologies Used</h3>
                        <ul class="text-dark">
                            <li><strong>Languages</strong>: HTML/CSS, JavaScript, PHP, SQL</li>
                            <li><strong>Libraries</strong>: Bootstrap 5, Chart.js, Font Awesome</li>
                            <li><strong>Database</strong>: MySQL</li>
                            <li><strong>Naming Convention</strong>: camelCase</li>
                        </ul>
                        
                        <h3 class="mt-4 text-dark">Data Source</h3>
                        <p class="text-dark">The data used in this application comes from the Paris Open Data portal:</p>
                        <p><a href="https://opendata.paris.fr/explore/dataset/arbres-plantes-par-projet/" target="_blank" class="text-success">https://opendata.paris.fr/explore/dataset/arbres-plantes-par-projet/</a></p>
                        
                        <h3 class="mt-4 text-dark">Installation</h3>
                        <ol class="text-dark">
                            <li>Clone this repository</li>
                            <li>Configure your web server (Apache, Nginx) to point to the <code>www</code> folder</li>
                            <li>Import the database from SQL files in the <code>admin/database</code> folder</li>
                            <li>Configure database connection settings in the <code>.env</code> file</li>
                            <li>Access the application through your browser</li>
                        </ol>
                    </div>
                    
                    <div class="section-divider"></div>
                    
                    <div class="mt-5 text-center">
                        <h3 class="text-success">Contact</h3>
                        <p class="text-dark">GitHub: <a href="https://github.com/MonsoonD" target="_blank" class="text-success">MonsoonD</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ajout d'un style spécifique pour cette page -->
<style>
    /* Style pour les éléments de code */
    code {
        background-color: #f8f9fa;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.875em;
        color: #d63384;
    }
    
    /* Améliorer l'apparence des liens */
    a.text-success {
        text-decoration: none;
        border-bottom: 1px dotted #198754;
    }
    
    a.text-success:hover {
        border-bottom: 1px solid #198754;
    }
    
    /* Styles supplémentaires pour améliorer l'apparence */
    .about-card {
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .about-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
    }
    
    h1.text-success {
        position: relative;
        display: inline-block;
        padding-bottom: 15px;
    }
    
    h1.text-success::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: linear-gradient(to right, #198754, #20c997);
        border-radius: 3px;
    }
    
    .section-divider {
        height: 2px;
        background: linear-gradient(to right, transparent, #198754, transparent);
        margin: 3rem 0;
    }
    
    /* Animation pour les éléments au chargement */
    .fade-in {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.8s ease, transform 0.8s ease;
    }
    
    .fade-in.visible {
        opacity: 1;
        transform: translateY(0);
    }
    
    /* Délais d'animation pour créer un effet en cascade */
    .delay-1 { transition-delay: 0.1s; }
    .delay-2 { transition-delay: 0.2s; }
    .delay-3 { transition-delay: 0.3s; }
    .delay-4 { transition-delay: 0.4s; }
</style>

<!-- Script pour l'effet de transparence au défilement -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    const aboutCard = document.querySelector('.about-card');
    
    function checkScroll() {
        // Obtenir la position du haut de la carte about
        const aboutCardTop = aboutCard.getBoundingClientRect().top;
        const navbarHeight = navbar.offsetHeight;
        
        // Si le bas de la navbar est proche ou dépasse le haut de la carte
        if (aboutCardTop <= navbarHeight + 20) {
            // Quand le header est au-dessus du contenu, on le rend vert
            navbar.classList.add('navbar-content-overlap');
            navbar.classList.remove('navbar-transparent');
        } else if (window.scrollY > 50) {
            // Comportement normal quand on défile mais pas encore au contenu
            navbar.classList.add('navbar-scrolled');
            navbar.classList.remove('navbar-transparent');
            navbar.classList.remove('navbar-content-overlap');
        } else {
            // Au sommet de la page, on revient à l'état initial transparent
            navbar.classList.remove('navbar-scrolled');
            navbar.classList.remove('navbar-content-overlap');
            navbar.classList.add('navbar-transparent');
        }
    }
    
    // Vérification initiale
    checkScroll();
    
    // Vérification au défilement
    window.addEventListener('scroll', checkScroll);
    
    // Animation des éléments au défilement
    // Ajouter les classes fade-in aux éléments
    const sections = document.querySelectorAll('h2, h3, p, ul, ol');
    sections.forEach((section, index) => {
        section.classList.add('fade-in');
        section.classList.add(`delay-${index % 4 + 1}`);
    });
    
    // Observer les éléments pour les animer au défilement
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });
    
    // Observer tous les éléments avec la classe fade-in
    document.querySelectorAll('.fade-in').forEach(element => {
        observer.observe(element);
    });
});
</script>

<?php include 'footer.php'; ?>
