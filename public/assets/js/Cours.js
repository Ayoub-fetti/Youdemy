document.addEventListener('DOMContentLoaded', function() {
    console.log('JavaScript chargé !');
    console.log('coursContainer trouvé:', document.getElementById('coursContainer'));

    // Fonction pour vérifier si l'utilisateur est connecté
    function isUserLoggedIn() {
        return document.body.dataset.userLoggedIn === 'true';
    }

    const searchInput = document.getElementById('searchCours');
    const coursContainer = document.getElementById('coursContainer');
    const paginationContainer = document.createElement('div');
    paginationContainer.className = 'flex justify-center mt-6 space-x-2';
    coursContainer.parentNode.insertBefore(paginationContainer, coursContainer.nextSibling);

    let currentPage = 1;
    let searchTerm = '';
    let timeoutId;

    // Fonction pour charger les cours
    async function loadCours(page = 1, search = '') {
        try {
            console.log('Chargement des cours...');
            console.log('URL:', `../../controllers/cours/pagination.php?page=${page}&search=${encodeURIComponent(search)}`);
            
            const response = await fetch(`../../controllers/cours/pagination.php?page=${page}&search=${encodeURIComponent(search)}`);
            console.log('Status:', response.status);
            
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Données reçues (détaillées):', {
                data: data,
                type: typeof data,
                keys: Object.keys(data),
                cours: data.cours,
                total: data.total,
                pages: data.pages
            });
            
            if (!data || !data.cours) {
                throw new Error('Format de données invalide');
            }
            
            if (Array.isArray(data.cours)) {
                updateCoursList(data.cours);
                updatePagination(data.pages, data.current_page);
                
                // Mettre à jour le compteur de cours
                const coursCount = document.getElementById('totalCours');
                if (coursCount) {
                    coursCount.textContent = `${data.total} Total des cours`;
                }
            } else {
                console.error('Données invalides reçues:', data);
                coursContainer.innerHTML = `
                    <div class="col-span-full text-center py-8 text-red-500">
                        Aucune donnée de cours disponible
                    </div>
                `;
            }
        } catch (error) {
            console.error('Erreur lors du chargement des cours:', error);
            coursContainer.innerHTML = `
                <div class="col-span-full text-center py-8 text-red-500">
                    Une erreur est survenue lors du chargement des cours.
                </div>
            `;
        }
    }

    // Gestionnaire de recherche
    searchInput.addEventListener('input', function(e) {
        clearTimeout(timeoutId);
        searchTerm = e.target.value.trim();
        
        timeoutId = setTimeout(() => {
            currentPage = 1; // Réinitialiser à la première page lors d'une nouvelle recherche
            loadCours(currentPage, searchTerm);
        }, 300);
    });

    // Fonction pour mettre à jour la liste des cours
    function updateCoursList(cours) {
        console.log('Mise à jour de la liste des cours:', cours);
        
        if (!coursContainer) {
            console.error('Container des cours non trouvé!');
            return;
        }

        coursContainer.innerHTML = '';
        
        if (!Array.isArray(cours) || cours.length === 0) {
            console.log('Aucun cours à afficher');
            coursContainer.innerHTML = `
                <div class="col-span-full text-center py-8 text-gray-500">
                    Aucun cours trouvé
                </div>
            `;
            return;
        }

        console.log(`Affichage de ${cours.length} cours`);
        
        cours.forEach((course, index) => {
            console.log(`Traitement du cours ${index + 1}:`, course);
            
            const date = new Date(course.date_creation);
            const dateFormatted = date.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });

            const courseCard = `
                <div class="bg-white rounded-lg shadow-md p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-500 text-sm flex items-center space-x-1">
                            <i class="fas fa-id-card-alt text-gray-700"></i>
                            <span class="creerPar">
                                créer par : ${course.enseignant_nom || 'Anonyme'} le 
                                <span>${dateFormatted}</span>
                            </span>
                        </span>
                    </div>
                    <div class="mb-2">
                        <span class="text-purple-600 text-sm">${course.categorie_nom || 'Non catégorisé'}</span>
                    </div>
                    <h2 class="text-lg font-semibold mb-2">${course.titre || 'Sans titre'}</h2>
                    <p class="text-gray-700 text-sm">${course.description || 'Aucune description'}</p>
                    ${course.tags ? `
                    <div class="mt-2 flex flex-wrap gap-2">
                        ${course.tags.split(',').map(tag => `
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">${tag.trim()}</span>
                        `).join('')}
                    </div>
                    ` : ''}
                    <div class="mt-4">
                        ${isUserLoggedIn() ? `
                            <form action="" method="POST" class="inscription-form">
                                <input type="hidden" name="cours_id" value="${course.id}">
                                <button type="submit" class="inscription-btn w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition duration-300 flex items-center justify-center">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    S'inscrire au cours
                                </button>
                            </form>
                        ` : `
                            <a href="../user/login.php" class="w-full bg-purple-700 text-white py-2 px-4 rounded-md hover:bg-gray-700 transition duration-300 flex items-center justify-center">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Se connecter pour s'inscrire
                            </a>
                        `}
                    </div>
                </div>
            `;
            
            coursContainer.innerHTML += courseCard;
        });

        console.log('Mise à jour des cours terminée');

        // Ajouter les gestionnaires d'événements pour les formulaires d'inscription
        document.querySelectorAll('.inscription-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const button = this.querySelector('.inscription-btn');
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-check mr-2"></i>Inscrit';
                button.classList.remove('bg-purple-600', 'hover:bg-purple-700');
                button.classList.add('bg-green-600', 'cursor-not-allowed');
                this.submit();
            });
        });
    }

    // Fonction pour mettre à jour la pagination
    function updatePagination(totalPages, currentPage) {
        paginationContainer.innerHTML = '';
        
        if (totalPages <= 1) return;

        // Bouton précédent
        if (currentPage > 1) {
            paginationContainer.appendChild(
                createPaginationButton(currentPage - 1, '&laquo; Précédent')
            );
        }

        // Pages numérotées
        for (let i = 1; i <= totalPages; i++) {
            if (
                i === 1 || // Première page
                i === totalPages || // Dernière page
                (i >= currentPage - 2 && i <= currentPage + 2) // Pages autour de la page courante
            ) {
                paginationContainer.appendChild(
                    createPaginationButton(i, i.toString(), i === currentPage)
                );
            } else if (
                (i === currentPage - 3 && currentPage > 4) ||
                (i === currentPage + 3 && currentPage < totalPages - 3)
            ) {
                // Ajouter des points de suspension
                const dots = document.createElement('span');
                dots.className = 'px-3 py-2 text-gray-500';
                dots.textContent = '...';
                paginationContainer.appendChild(dots);
            }
        }

        // Bouton suivant
        if (currentPage < totalPages) {
            paginationContainer.appendChild(
                createPaginationButton(currentPage + 1, 'Suivant &raquo;')
            );
        }
    }

    // Fonction pour créer un bouton de pagination
    function createPaginationButton(page, text, isActive = false) {
        const button = document.createElement('button');
        button.innerHTML = text;
        button.className = `px-3 py-2 rounded-md ${
            isActive
                ? 'bg-purple-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-50'
        }`;
        
        if (!isActive) {
            button.addEventListener('click', () => {
                currentPage = page;
                loadCours(currentPage, searchTerm);
            });
        }
        
        return button;
    }

    // Charger la première page au chargement
    loadCours(currentPage, searchTerm);
});