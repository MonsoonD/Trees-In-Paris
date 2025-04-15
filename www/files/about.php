<?php include '../files/header.php'; ?>

<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-5">
                    <h1 class="text-center text-success mb-5">Trees-In-Paris</h1>
                    
                    <!-- Version française -->
                    <div class="mb-5">
                        <h2 class="border-bottom pb-2 text-success">🇫🇷 Français</h2>
                        
                        <h3 class="mt-4">À propos</h3>
                        <p>Un site web interactif permettant d'explorer et de visualiser les données sur les arbres plantés à Paris. Cette application web offre une interface intuitive pour découvrir l'évolution de la forêt urbaine parisienne à travers des cartes interactives, des statistiques détaillées et des informations sur les projets de plantation.</p>
                        
                        <h3 class="mt-4">Fonctionnalités</h3>
                        <ul>
                            <li><strong>Carte interactive</strong> : Explorez la répartition des arbres dans les 20 arrondissements de Paris</li>
                            <li><strong>Statistiques détaillées</strong> : Visualisez les tendances de plantation d'arbres au fil du temps avec des graphiques dynamiques</li>
                            <li><strong>Filtrage avancé</strong> : Filtrez les données par arrondissement et par année pour une analyse personnalisée</li>
                            <li><strong>Projets de plantation</strong> : Découvrez les initiatives récentes qui contribuent à l'augmentation de la canopée urbaine</li>
                        </ul>
                        
                        <h3 class="mt-4">Technologies utilisées</h3>
                        <ul>
                            <li><strong>Langages</strong> : HTML/CSS, JavaScript, PHP, SQL</li>
                            <li><strong>Bibliothèques</strong> : Bootstrap 5, Chart.js, Font Awesome</li>
                            <li><strong>Base de données</strong> : MySQL</li>
                            <li><strong>Convention de nommage</strong> : camelCase</li>
                        </ul>
                        
                        <h3 class="mt-4">Source des données</h3>
                        <p>Les données utilisées dans cette application proviennent du portail Open Data de la Ville de Paris :</p>
                        <p><a href="https://opendata.paris.fr/explore/dataset/arbres-plantes-par-projet/" target="_blank" class="text-success">https://opendata.paris.fr/explore/dataset/arbres-plantes-par-projet/</a></p>
                        
                        <h3 class="mt-4">Installation</h3>
                        <ol>
                            <li>Clonez ce dépôt</li>
                            <li>Configurez votre serveur web (Apache, Nginx) pour pointer vers le dossier <code>www</code></li>
                            <li>Importez la base de données à partir des fichiers SQL dans le dossier <code>admin/database</code></li>
                            <li>Configurez les paramètres de connexion à la base de données dans le fichier <code>.env</code></li>
                            <li>Accédez à l'application via votre navigateur</li>
                        </ol>
                    </div>
                    
                    <hr class="my-5">
                    
                    <!-- Version anglaise -->
                    <div>
                        <h2 class="border-bottom pb-2 text-success">🇬🇧 English</h2>
                        
                        <h3 class="mt-4">About</h3>
                        <p>An interactive website that allows users to explore and visualize data about trees planted in Paris. This web application offers an intuitive interface to discover the evolution of Paris's urban forest through interactive maps, detailed statistics, and information about planting projects.</p>
                        
                        <h3 class="mt-4">Features</h3>
                        <ul>
                            <li><strong>Interactive Map</strong>: Explore tree distribution across all 20 arrondissements of Paris</li>
                            <li><strong>Detailed Statistics</strong>: Visualize tree planting trends over time with dynamic charts</li>
                            <li><strong>Advanced Filtering</strong>: Filter data by district and year for customized analysis</li>
                            <li><strong>Planting Projects</strong>: Discover recent initiatives contributing to the increase of urban tree canopy</li>
                        </ul>
                        
                        <h3 class="mt-4">Technologies Used</h3>
                        <ul>
                            <li><strong>Languages</strong>: HTML/CSS, JavaScript, PHP, SQL</li>
                            <li><strong>Libraries</strong>: Bootstrap 5, Chart.js, Font Awesome</li>
                            <li><strong>Database</strong>: MySQL</li>
                            <li><strong>Naming Convention</strong>: camelCase</li>
                        </ul>
                        
                        <h3 class="mt-4">Data Source</h3>
                        <p>The data used in this application comes from the Paris Open Data portal:</p>
                        <p><a href="https://opendata.paris.fr/explore/dataset/arbres-plantes-par-projet/" target="_blank" class="text-success">https://opendata.paris.fr/explore/dataset/arbres-plantes-par-projet/</a></p>
                        
                        <h3 class="mt-4">Installation</h3>
                        <ol>
                            <li>Clone this repository</li>
                            <li>Configure your web server (Apache, Nginx) to point to the <code>www</code> folder</li>
                            <li>Import the database from SQL files in the <code>admin/database</code> folder</li>
                            <li>Configure database connection settings in the <code>.env</code> file</li>
                            <li>Access the application through your browser</li>
                        </ol>
                    </div>
                    
                    <div class="mt-5 text-center">
                        <h3 class="text-success">Contact</h3>
                        <p>GitHub: <a href="https://github.com/MonsoonD" target="_blank" class="text-success">MonsoonD</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../files/footer.php'; ?>
