<?php
    require_once __DIR__ . '/../../models/CoursPDF.php';
    require_once __DIR__ . '/../../models/CoursVideo.php';
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Enseignant.php';
    require_once __DIR__ . '/../../models/User.php';
    
    session_start();
    
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'enseignant') {
        header('Location: ../user/login.php');
        exit();
    }

    $db = new Database();
    $pdo = $db->connect();
    $enseignant = new Enseignant($pdo);
    $enseignant->loadById($_SESSION['user_id']);
    $enseignant_id = $_SESSION['user_id'];

    // Récupérer les statistiques
    $total_cours = $enseignant->getTotalCours($enseignant_id);
    $cours_populaires = $enseignant->getCoursLesPlusUtilises($enseignant_id);
    $inscriptions_par_mois = $enseignant->getInscriptionsParMois($enseignant_id);
    $stats_categories = $enseignant->getStatistiquesParCategorie($enseignant_id);

    // Calculer le total des inscriptions
    $total_inscriptions = 0;
    foreach ($cours_populaires as $cours) {
        $total_inscriptions += $cours['nombre_inscriptions'];
    }

    // Calculer le total des catégories
    $total_categories = count($stats_categories);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Cours</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl text-indigo-600 font-bold text-gray-800">Statistiques de mes cours</h1>
            <a href="enseignant_Dash.php" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Tableau de bord</a>
        </div>

        <!-- Résumé des statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Total des cours</h3>
                <p class="text-4xl font-bold text-indigo-600"><?php echo $total_cours; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Total des inscriptions</h3>
                <p class="text-4xl font-bold text-indigo-600"><?php echo $total_inscriptions; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Catégories</h3>
                <p class="text-4xl font-bold text-indigo-600"><?php echo $total_categories; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Moyenne inscriptions/cours</h3>
                <p class="text-4xl font-bold text-indigo-600">
                    <?php echo $total_cours > 0 ? round($total_inscriptions / $total_cours, 1) : 0; ?>
                </p>
            </div>
        </div>

        <!-- Cours les plus populaires -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Cours les plus populaires</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre du cours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inscriptions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Popularité</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($cours_populaires as $cours): 
                            $pourcentage = $total_inscriptions > 0 ? ($cours['nombre_inscriptions'] / $total_inscriptions) * 100 : 0;
                        ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($cours['titre']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $cours['nombre_inscriptions']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: <?php echo $pourcentage; ?>%"></div>
                                </div>
                                <span class="text-sm text-gray-600"><?php echo round($pourcentage, 1); ?>%</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Statistiques par catégorie -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Statistiques par catégorie</h2>
            <div class="grid gap-4">
                <?php foreach ($stats_categories as $stat): 
                    $pourcentage_cours = $total_cours > 0 ? ($stat['nombre_cours'] / $total_cours) * 100 : 0;
                    $pourcentage_inscriptions = $total_inscriptions > 0 ? ($stat['nombre_inscriptions'] / $total_inscriptions) * 100 : 0;
                ?>
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($stat['categorie']); ?></h3>
                    <div class="space-y-2">
                        <div>
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Cours (<?php echo $stat['nombre_cours']; ?>)</span>
                                <span><?php echo round($pourcentage_cours, 1); ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: <?php echo $pourcentage_cours; ?>%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Inscriptions (<?php echo $stat['nombre_inscriptions']; ?>)</span>
                                <span><?php echo round($pourcentage_inscriptions, 1); ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: <?php echo $pourcentage_inscriptions; ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Évolution des inscriptions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Évolution des inscriptions</h2>
            <div class="space-y-4">
                <?php 
                $max_inscriptions = 0;
                foreach ($inscriptions_par_mois as $inscription) {
                    $max_inscriptions = max($max_inscriptions, $inscription['nombre_inscriptions']);
                }
                
                foreach ($inscriptions_par_mois as $inscription): 
                    $pourcentage = $max_inscriptions > 0 ? ($inscription['nombre_inscriptions'] / $max_inscriptions) * 100 : 0;
                ?>
                <div>
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span><?php echo date('F Y', strtotime($inscription['mois'] . '-01')); ?></span>
                        <span><?php echo $inscription['nombre_inscriptions']; ?> inscriptions</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: <?php echo $pourcentage; ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>