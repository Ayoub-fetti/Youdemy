document.addEventListener('DOMContentLoaded', function() {
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

        cours.forEach(cours => {
            const date = new Date(cours.date_creation);
            const dateFormatted = date.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });

            const courseCard = `
                <div id="hover" class="bg-violet-300 rounded-lg shadow-md p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-500 text-sm flex items-center space-x-1">
                            <i class="fas fa-id-card-alt text-gray-700"></i>
                            <span class="creerPar">
                                creer par : ${cours.enseignant_nom} le 
                                <span>${dateFormatted}</span>
                            </span>
                        </span>
                    </div>
                    <div class="mb-2">
                        <span class="text-purple-600 text-sm">${cours.categorie_nom}</span>
                    </div>
                    <h2 class="text-lg font-semibold mb-2">${cours.titre}</h2>
                    <p class="text-gray-700 text-sm">${cours.description}</p>
                </div>
            `;
            coursContainer.innerHTML += courseCard;
        });
    }

    // Fonction pour mettre à jour la pagination
    function updatePagination(totalPages, currentPage) {
        paginationContainer.innerHTML = '';
        
        // Bouton précédent
        if (currentPage > 1) {
            const prevButton = createPaginationButton(currentPage - 1, '&laquo; Précédent');
            paginationContainer.appendChild(prevButton);
        }

        // Pages numérotées
        for (let i = 1; i <= totalPages; i++) {
            if (
                i === 1 || // Première page
                i === totalPages || // Dernière page
                (i >= currentPage - 1 && i <= currentPage + 1) // Pages autour de la page courante
            ) {
                const pageButton = createPaginationButton(i, i.toString(), i === currentPage);
                paginationContainer.appendChild(pageButton);
            } else if (
                i === currentPage - 2 ||
                i === currentPage + 2
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
            const nextButton = createPaginationButton(currentPage + 1, 'Suivant &raquo;');
            paginationContainer.appendChild(nextButton);
        }
    }

    // Fonction pour créer un bouton de pagination
    function createPaginationButton(page, text, isActive = false) {
        const button = document.createElement('button');
        button.innerHTML = text;
        button.className = `px-3 py-2 rounded-md ${
            isActive
                ? 'bg-purple-600 text-white'
                : 'text-gray-700 hover:bg-purple-100'
        }`;
        
        button.addEventListener('click', () => {
            currentPage = page;
            loadCours(currentPage, searchTerm);
        });
        
        return button;
    }

    // Charger la première page au chargement
    loadCours(currentPage, searchTerm);
});