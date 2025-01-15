document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour vérifier si l'utilisateur est connecté
    function isUserLoggedIn() {
        return document.body.dataset.userLoggedIn === 'true';
    }

    const searchInput = document.getElementById('searchCours');
    const coursContainer = document.querySelector('.grid');
    const paginationContainer = document.createElement('div');
    paginationContainer.className = 'flex justify-center mt-6 space-x-2';
    coursContainer.parentNode.insertBefore(paginationContainer, coursContainer.nextSibling);

    let currentPage = 1;
    let searchTerm = '';
    let timeoutId;

    // Fonction pour charger les cours
    async function loadCours(page = 1, search = '') {
        try {
            const response = await fetch(`/Youdemy/controllers/cours/pagination.php?page=${page}&search=${encodeURIComponent(search)}`);
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            const data = await response.json();
            updateCoursList(data.cours);
            updatePagination(data.pages, data.current_page);
            
            // Mettre à jour le compteur de cours
            const coursCount = document.getElementById('totalCours');
            if (coursCount) {
                coursCount.textContent = `${data.total} cours trouvés`;
            }
        } catch (error) {
            console.error('Erreur lors du chargement des cours:', error);
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
        coursContainer.innerHTML = '';
        
        if (cours.length === 0) {
            coursContainer.innerHTML = `
                <div class="col-span-full text-center py-8 text-gray-500">
                    Aucun cours trouvé
                </div>
            `;
            return;
        }

        cours.forEach(course => {
            const date = new Date(course.date_creation);
            const dateFormatted = date.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });

            const courseCard = `
                <div class="bg-violet-300 rounded-lg shadow-md p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-500 text-sm flex items-center space-x-1">
                            <i class="fas fa-id-card-alt text-gray-700"></i>
                            <span class="creerPar">
                                creer par : ${course.enseignant_nom} le 
                                <span>${dateFormatted}</span>
                            </span>
                        </span>
                    </div>
                    <div class="mb-2">
                        <span class="text-purple-600 text-sm">${course.categorie_nom}</span>
                    </div>
                    <h2 class="text-lg font-semibold mb-2">${course.titre}</h2>
                    <p class="text-gray-700 text-sm">${course.description}</p>
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

        // Ajouter les gestionnaires d'evenements pour les formulaires d'inscription
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