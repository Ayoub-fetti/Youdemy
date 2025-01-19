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
    <body data-user-logged-in="<?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="container mx-auto p-4">
            <header class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-semibold">
                    Les cours de <span class="text-purple-500">Youdemy</span>
                </h1>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="../user/logout.php" class="bg-violet-500 hover:bg-violet-700 text-white text-sm px-3 py-1 rounded-md">
                        <i class="fas fa-sign-out-alt mr-1"></i>Déconnexion
                    </a>
                <?php endif; ?>
            </header>

            <nav class="flex space-x-4 mb-6">
                <a class="text-purple-600 border-b-2 border-purple-600 pb-2" href="catalogue.php">Toutes les Cours</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a class="text-gray-500 pb-2" href="mes_cours.php">Mes Cours</a>
                <?php endif; ?>
            </nav>

            <div class="flex items-center mb-6">
                <div class="relative w-full max-w-xs">
                    <input id="searchCours" 
                           type="text" 
                           class="w-full pl-10 pr-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600" 
                           placeholder="Rechercher un cours..."/>
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>


            <!-- Container principal avec une couleur de fond pour le débogage -->
            <div class="bg-gray-100 p-4 rounded-lg">
                <!-- Container des cours -->
                <div id="coursContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Les cours seront chargés ici dynamiquement -->
                    <div class="col-span-full text-center py-8 text-gray-500">
                        Chargement des cours...
                    </div>
                </div>
            </div>
        </div>

        <script src="../../public/assets/js/Cours.js"></script>
    </body>
</html>
