<?php
    require_once __DIR__ . '/../../models/CoursPDF.php';
    require_once __DIR__ . '/../../models/CoursVideo.php';
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Enseignant.php';

    $database = new Database();
    $pdo = $database->connect();

    // Recuperer l'ID de l'enseignant
    session_start();
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'enseignant') {
        header('Location: /login.php');
        exit;
    }
    $enseignant_id = $_SESSION['user_id'];
    $message = '';

    // Check for edit success message
    if (isset($_GET['edit_success']) && $_GET['edit_success'] == '1') {
        $message = "Le cours a été modifié avec succès !";
    }

    // Recuperer la liste des cours de l'enseignant
    $query = "SELECT cours.*, categories.nom as categorie_nom,
            CASE 
            WHEN cours.contenu LIKE '%.pdf' THEN 'pdf'
            ELSE 'video'
            END as type
            FROM cours
            LEFT JOIN categories ON cours.categorie_id = categories.id 
            WHERE cours.enseignant_id = :enseignant_id 
            ORDER BY cours.date_creation DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute([':enseignant_id' => $enseignant_id]);
    $cours_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Cours</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl text-indigo-600 font-bold text-gray-800">Mes Cours</h1>
            <a href="enseignant_Dash.php" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Tableau de bord</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="p-4 mb-4 rounded-lg <?php echo strpos($message, 'Erreur') === false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Liste des cours -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="border-b border-gray-200 p-4">
                <h2 class="text-xl font-semibold text-gray-800">Liste de mes cours</h2>
            </div>
            <div class="p-6">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="w-1/6 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                            <th class="w-2/6 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="w-1/6 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="w-1/6 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contenu</th>
                            <th class="w-1/6 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($cours_list as $cours): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900 truncate"><?php echo htmlspecialchars($cours['titre']); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900 truncate"><?php echo htmlspecialchars($cours['description']); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($cours['type']); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <?php if ($cours['type'] === 'pdf'): ?>
                                    <a href="<?php echo htmlspecialchars($cours['contenu']); ?>" target="_blank" class="text-indigo-600 hover:text-indigo-900">Voir le PDF</a>
                                <?php else: ?>
                                    <a href="<?php echo htmlspecialchars($cours['contenu']); ?>" target="_blank" class="text-indigo-600 hover:text-indigo-900">Voir la vidéo</a>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?php echo htmlspecialchars($cours['date_creation']); ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <a href="edit_course.php?id=<?php echo $cours['id']; ?>" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                <form action="delete_course.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $cours['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900 ml-2" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
