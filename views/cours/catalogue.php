<?php 
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Cours.php';
require_once __DIR__ . '/../../models/Etudiant.php';
session_start();

// Instancier la classe Cours
$coursObj = new Cours();
$cours = $coursObj->getAllCours();



// Traiter l'inscription au cours
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cours_id']) && isset($_SESSION['user_id'])) {
    $db = new Database();
    $pdo = $db->connect();
    $etudiant = new Etudiant($pdo);
    $result = $etudiant->inscrireAuCours($_POST['cours_id']);
    
    if ($result['success']) {
        $_SESSION['message'] = 'Inscription réussie !';
    } else {
        $_SESSION['error'] = $result['message'];
    }
    
    // Rediriger pour éviter la resoumission du formulaire
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
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
<body class="bg-gray-50 min-h-screen" data-user-logged-in="<?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="max-w-4xl mx-auto mb-6">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?= $_SESSION['message'] ?>
                </div>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="max-w-4xl mx-auto mb-6">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= $_SESSION['error'] ?>
                </div>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="max-w-7xl mx-auto">
            <header class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
                <div class="text-center sm:text-left">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        Les cours de <span class="text-purple-600">Youdemy</span>
                    </h1>
                    <p class="text-gray-600 mt-2">Découvrez notre sélection de cours de qualité</p>
                </div>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">
                            <i class="fas fa-user mr-2"></i>
                            <?= htmlspecialchars($_SESSION['user_nom']) ?>
                        </span>
                        <a href="../user/logout.php" 
                           class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition duration-300 flex items-center">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            <span>Déconnexion</span>
                        </a>
                    </div>
                <?php endif; ?>
            </header>

            <nav class="flex flex-wrap justify-center sm:justify-start gap-6 mb-8">
                <a class="text-purple-600 border-b-2 border-purple-600 pb-2 font-medium hover:text-purple-700 transition duration-300" 
                   href="catalogue.php">
                    <i class="fas fa-book-open mr-2"></i>Tous les cours
                </a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a class="text-gray-600 hover:text-purple-600 pb-2 font-medium transition duration-300" 
                       href="mes_cours.php">
                        <i class="fas fa-graduation-cap mr-2"></i>Mes cours
                    </a>
                <?php endif; ?>
            </nav>

            <div class="mb-8">
                <div class="max-w-xl mx-auto">
                    <div class="relative">
                        <input id="searchCours" 
                        type="text" 
                        class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300" 
                        placeholder="Rechercher par titre, catégorie, enseignant, description ou tags..."/>
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <p id="totalCours" class="text-gray-600 mb-3 mt-3 text-center text-gray-600"></p>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-xl p-6">
                <div id="coursContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="col-span-full flex items-center justify-center py-12">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
                            <p class="text-gray-600">Chargement des cours...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../public/assets/js/Cours.js"></script>
</body>
</html>
