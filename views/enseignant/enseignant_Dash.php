<?php 
    require_once __DIR__ . '/../../models/CoursPDF.php';
    require_once __DIR__ . '/../../models/CoursVideo.php';
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Enseignant.php';
    require_once __DIR__ . '/../../models/User.php';
    session_start();
  
    // verification de la session enseignant
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'enseignant') {
        header('Location: ../user/login.php');
        exit();
    }

$db = new Database();
$pdo = $db->connect();
$user = new User($pdo);
$errors = [];

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Enseignant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl text-indigo-600 font-bold text-gray-800">Tableau de bord de <span><?php echo htmlspecialchars(ucfirst($_SESSION['user_nom'])); ?></span></h1>
            <div>
                <a href="../user/logout.php" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors duration-200">
                    Déconnexion
                </a>
            </div>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="p-4 mb-4 rounded-lg <?php echo strpos($message, 'Erreur') === false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Card pour Ajouter un cours -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Ajouter un cours</h2>
                <p class="text-gray-600 mb-4">Créez un nouveau cours en format PDF ou vidéo et partagez votre savoir avec vos étudiants.</p>
                <a href="ajouter_cours.php" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Ajouter un cours
                </a>
            </div>
            
            <!-- Card pour Voir mes cours -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Mes cours</h2>
                <p class="text-gray-600 mb-4">Consultez, modifiez ou supprimez vos cours existants.</p>
                <a href="mes_cours.php" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Voir mes cours
                </a>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Mes statistiques</h2>
                <p class="text-gray-600 mb-4">Consultez les statistiques de vos cours existants.</p>
                <a href="statistique.php" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Voir les statistiques
                </a>
            </div>
        </div>
    </div>
</body>
</html>