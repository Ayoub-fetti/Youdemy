<?php 
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Cours.php';
session_start();

// Instancier la classe Cours
$coursObj = new Cours();
$cours = $coursObj->getAllCours();

// Récupérer les cours par catégorie si un filtre est appliqué
if (isset($_GET['categorie'])) {
    $cours = $coursObj->getCoursByCategorie($_GET['categorie']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Catalogue des Cours - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>
<body>
    <div class="container mx-auto p-4">
        <header class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold">
                Les cours de <span class="text-purple-500">Youdemy</span>
            </h1>
        </header>

        <nav class="flex space-x-4 mb-6">
            <a class="text-purple-600 border-b-2 border-purple-600 pb-2" href="catalogue.php">Toutes les Cours</a>
            <a class="text-gray-500 pb-2" href="mes_cours.php">Mes Cours</a>
        </nav>

        <div class="flex items-center mb-6">
            <div class="relative w-full max-w-xs">
                <input id="searchCours" class="w-full pl-10 pr-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600" placeholder="Rechercher un cours..." type="text"/>
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <div class="flex justify-between items-center mb-6">
            <span id="totalCours" class="text-gray-500"><?= count($cours) ?> Total des cours</span>
            <div class="relative">
                <select class="flex items-center space-x-2 px-4 py-2 bg-white border rounded-md text-gray-700">
                    <option value="new">Nouveaux cours</option>
                    <option value="popular">Cours populaires</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($cours as $course): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img alt="<?= htmlspecialchars($course['titre']) ?>" class="w-full h-40 object-cover" src="https://placehold.co/600x400"/>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-500 text-sm flex items-center space-x-1">
                            <i class="fas fa-id-card-alt text-gray-400"></i>
                            <span class="creerPar">
                                creer par : <?= htmlspecialchars($course['enseignant_nom']) ?> le 
                                <span><?= date('d/m/Y', strtotime($course['date_creation'])) ?></span>
                            </span>
                        </span>
                    </div>
                    <div class="mb-2">
                        <span class="text-purple-600 text-sm"><?= htmlspecialchars($course['categorie_nom']) ?></span>
                    </div>
                    <h2 class="text-lg font-semibold mb-2">
                        <?= htmlspecialchars($course['titre']) ?>
                    </h2>
                    <p class="text-gray-600 text-sm">
                        <?= htmlspecialchars($course['description']) ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="/Youdemy/public/assets/js/Cours.js"></script>
</body>
</html>
