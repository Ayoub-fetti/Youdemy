<?php 
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Cours.php';
require_once __DIR__ . '/../../models/Etudiant.php';
require_once __DIR__ . '/../../models/Enseignant.php';
session_start();

// verifier l'utilisatuer est connecter 
if (!isset($_SESSION['user_id'])) {
    header('Location: ../user/login.php');
    exit;
}
$db = new Database();
$pdo = $db->connect();
$etudiant = new Etudiant($pdo);
$cours = $etudiant->getCoursInscrit();




?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Cours - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <header class="flex flex-col sm:flex-row items-center justify-between mb-6 gap-4">
            <h1 class="text-xl sm:text-2xl font-semibold text-center sm:text-left">
                Mes cours sur <span class="text-purple-500">Youdemy</span>
            </h1>
            <a href="../user/logout.php" class="bg-violet-500 hover:bg-violet-700 text-white text-sm px-4 py-2 rounded-md transition duration-300 w-full sm:w-auto text-center">
                <i class="fas fa-sign-out-alt mr-1"></i>Déconnexion
            </a>
        </header>

        <nav class="flex flex-wrap space-x-4 mb-6 justify-center sm:justify-start">
            <a class="text-gray-500 pb-2 hover:text-gray-700 transition duration-300" href="catalogue.php">Toutes les Cours</a>
            <a class="text-purple-600 border-b-2 border-purple-600 pb-2" href="mes_cours.php">Mes Cours</a>
        </nav>

        <?php if (empty($cours)): ?>
            <div class="text-center py-8">
                <p class="text-gray-600 mb-4">Vous n'êtes inscrit à aucun cours pour le moment.</p>
                <a href="catalogue.php" class="inline-block bg-purple-500 hover:bg-purple-700 text-white px-6 py-2 rounded-md transition duration-300">
                    Découvrir les cours
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($cours as $course): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold mb-2 text-gray-800"><?php echo htmlspecialchars($course['titre']); ?></h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2"><?php echo htmlspecialchars($course['description']); ?></p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    <?php 
                                        $date = isset($course['date_inscription']) && !empty($course['date_inscription']) 
                                            ? date('d/m/Y', strtotime($course['date_inscription'])) 
                                            : 'Date non disponible';
                                        echo $date;
                                    ?>
                                </span>
                                <a href="details.php?id=<?php echo $course['id']; ?>" 
                                    class="<?php echo ($course['inscription_status'] === 'terminer') 
                                        ? 'bg-purple-500 hover:bg-purple-700' 
                                        : 'bg-green-500 hover:bg-green-700'; ?> text-white text-sm px-4 py-2 rounded-md transition duration-300">
                                    <?php echo ($course['inscription_status'] === 'terminer') ? 'Cours terminé' : 'Voir le cours'; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>