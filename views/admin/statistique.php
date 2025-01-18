<?php 
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Admin.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../user/login.php');
    exit();
}

$db = new Database();
$pdo = $db->connect();
$admin = new Admin($pdo);

$totalCours = $admin->totalCours();
$coursParCategorie = $admin->getCoursParCategorie();
$coursPopulaire = $admin->getCoursLePlusPopulaire();
$topEnseignants = $admin->getTopEnseignants(3);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Statistiques - Administration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white h-screen shadow-md">
            <div class="flex items-center justify-center h-16 border-b">
                <div class="text-2xl font-bold text-purple-600">Bienvenue</div>
                <div class="ml-2 text-2xl font-semibold">
                    <?php echo htmlspecialchars(ucfirst($_SESSION['user_nom'])); ?>
                </div>
            </div>
            <nav class="mt-10">
                <a class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100" href="dashbord.php">
                    <i class="fas fa-home"></i>
                    <span class="ml-2">Tableau de bord</span>
                </a>
                <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-100" href="gestion_cours.php">
                        <i class="fas fa-file-alt">
                        </i>
                        <span class="ml-2">
                        Cours
                        </span>
                        </a>
                        <a class="flex items-center px-4 py-2 text-gray-700 bg-gray-100" href="statistique.php">
                        <i class="fas fa-chart-bar">
                        </i>
                        <span class="ml-2">
                        Analytics
                        </span>
                        </a>
                        <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-100" href="gestion_categorie.php">
                        <!-- <i class="fas fa-chart-bar"> -->
                        <i class="fas fa-sitemap"></i>
                        </i>
                        <span class="ml-2">
                        Catégories
                        </span>
                        </a>
                        <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-red-200" href="../user/logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="ml-2">Déconnexion</span>
                        </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <h1 class="text-3xl font-semibold mb-6">Statistiques Globales</h1>

            <!-- Statistiques Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Cours -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-book text-2xl"></i>
                        </div>
                        <div class="ml-2">
                            <h2 class="text-gray-600">Total Cours</h2>
                            <p class="text-2xl font-semibold"><?php echo $totalCours; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Cours le plus populaire -->
                <div class="bg-white rounded-lg shadow w-96 p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-star text-2xl"></i>
                        </div>
                        <div class="ml-2">
                            <h2 class="text-gray-600">Cours le plus populaire</h2>
                            <p class="text-lg font-semibold"><?php echo htmlspecialchars($coursPopulaire['titre'] ?? 'N/A'); ?></p>
                            <p class="text-sm text-gray-500"><?php echo ($coursPopulaire['nb_etudiants'] ?? 0) . ' étudiants'; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphiques et Top Enseignants -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Répartition par catégorie -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Répartition par catégorie</h2>
                    <div class="grid gap-6">
                        <?php foreach ($coursParCategorie as $categorie): ?>
                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($categorie['categorie']); ?></h3>
                                <div class="text-right">
                                    <span class="text-sm text-gray-500">Activité globale</span>
                                    <div class="text-2xl font-bold text-indigo-600"><?php echo round(($categorie['count'] / $totalCours) * 100, 1); ?>%</div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <!-- Cours -->
                                <div>
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>Cours</span>
                                        <span class="font-medium"><?php echo $categorie['count']; ?> (<?php echo round(($categorie['count'] / $totalCours) * 100, 1); ?>%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: <?php echo round(($categorie['count'] / $totalCours) * 100, 1); ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Top 3 Enseignants -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Top 3 Enseignants</h2>
                    <div class="space-y-4">
                        <?php foreach ($topEnseignants as $index => $enseignant): ?>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-purple-100 text-purple-600">
                                <?php echo $index + 1; ?>
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold"><?php echo htmlspecialchars(ucfirst($enseignant['nom'])); ?></p>
                                <p class="text-sm text-gray-500"><?php echo $enseignant['nb_cours'] . ' cours'; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>