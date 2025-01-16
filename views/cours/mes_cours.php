<?php 
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Cours.php';
require_once __DIR__ . '/../../models/Etudiant.php';
session_start();

// verifier l'utilisatuer est connecter 
if (!isset($_SESSION['user_id'])) {
    header('Location: ../user/login.php');
    exit;
}
$etudiant = new Etudiant();
$etudiant->setId($_SESSION['user_id']);
$cours = $etudiant->getCoursInscrit();

// var_dump($cours);
// var_dump($etudiant);


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
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <header class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold">
                Mes cours sur <span class="text-purple-500">Youdemy</span>
            </h1>
            <a href="../user/logout.php" class="bg-violet-500 hover:bg-violet-700 text-white text-sm px-3 py-1 rounded-md">
                <i class="fas fa-sign-out-alt mr-1"></i>Déconnexion
            </a>
        </header>

        <nav class="flex space-x-4 mb-6">
            <a class="text-gray-500 pb-2" href="catalogue.php">Toutes les Cours</a>
            <a class="text-purple-600 border-b-2 border-purple-600 pb-2" href="mes_cours.php">Mes Cours</a>
        </nav>

        <?php if (empty($cours)): ?>
            <div class="text-center py-8">
                <p class="text-gray-600 mb-4">Vous n'êtes inscrit à aucun cours pour le moment.</p>
                <a href="catalogue.php" class="text-purple-600 hover:text-purple-700">
                    <i class="fas fa-arrow-right mr-2"></i>Découvrir les cours disponibles
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($cours as $course): ?>
                    <div class="bg-violet-300 rounded-lg shadow-md p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-500 text-sm flex items-center space-x-1">
                                <i class="fas fa-id-card-alt text-gray-700"></i>
                                <span class="creerPar">
                                    creer par : <?= htmlspecialchars($course['enseignant_nom']) ?> le 
                                    <span><?= date('d/m/Y', strtotime($course['date_creation'])) ?></span>
                                </span>
                            </span>
                        </div>
                        <div class="mb-2">
                            <span class="text-purple-600 text-sm"><?= htmlspecialchars($course['categorie_nom']) ?></span>
                        </div>
                        <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($course['titre']) ?></h3>
                        <p class="text-gray-600 mb-4"><?= htmlspecialchars($course['description']) ?></p>
                        <div class="flex justify-between items-center">
                            <a href="details.php?id=<?= $course['id'] ?>" class="bg-violet-500 text-white px-4 py-2 rounded hover:bg-violet-600">
                                Voir le cours
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>