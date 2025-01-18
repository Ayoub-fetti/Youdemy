<?php
require_once '../../config/database.php';
require_once '../../models/Admin.php';
session_start();

// Vérification de la session admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../user/login.php');
    exit();
}

$db = new Database();
$pdo = $db->connect();
$admin = new Admin($pdo);

// Récupération de tous les cours
$cours = $admin->getAllCours();

// Traitement de la suppression d'un cours
if(isset($_POST['supprimer_cours'])) {
    $cours_id = $_POST['cours_id'] ?? 0;
    if($cours_id && $admin->deleteCourse($cours_id)) {
        $message = "Cours supprimé avec succès!";
        $messageType = "success";
        // Rafraîchir la liste des cours
        $cours = $admin->getAllCours();
    } else {
        $message = "Erreur lors de la suppression du cours. Veuillez réessayer ou contacter l'administrateur.";
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Cours - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex">
        <!-- Sidebar -->
        <div class="flex">
   <!-- Sidebar -->
   <div class="w-64 bg-white h-screen shadow-md">
    <div class="flex items-center justify-center h-16 border-b">
     <div class="text-2xl font-bold text-purple-600">
      Bienvenue
     </div>
     <div class="ml-2 text-2xl font-semibold">
     <!-- <?php echo htmlspecialchars(ucfirst($_SESSION['user_nom'])); ?> -->
     </div>
    </div>
    <nav class="mt-10">
     <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-100" href="dashbord.php">
      <i class="fas fa-home">
      </i>
      <span class="ml-2">
       Tableau de bord
      </span>
     </a>
     <a class="flex items-center px-4 py-2 text-gray-700 bg-gray-100" href="gestion_cours.php">
      <i class="fas fa-file-alt">
      </i>
      <span class="ml-2">
       Cours
      </span>
     </a>
     <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-100" href="statistique.php">
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
      <i class="fas fa-sign-out-alt">
      </i>
      <span class="ml-2">
      Déconnexion
      </span>
     </a>
    </nav>
</div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
            
                <h2 class="text-2xl font-semibold mb-6">Rechercher des Cours</h2>
                <input class="w-56 px-4 py-2 mb-8 pl-10 text-gray-700 bg-white border rounded-full focus:outline-none" id="searchInput" placeholder="Rechercher ici..." type="text"/>
                <h2 class="text-2xl font-semibold mb-6">Gestion des Cours</h2>

                <?php if(isset($message)): ?>
                    <div class="mb-4 p-4 rounded <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <!-- Liste des cours -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                                <th class="px-4 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">tags</th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach($cours as $course): ?>
                                <tr class="transaction-row">
                                    <td class="px-6 py-4 whitespace-no-wrap"><?php echo htmlspecialchars($course['titre'] ?? ''); ?></td>
                                    <td class="px-4 py-4"><?php echo htmlspecialchars(substr($course['description'] ?? '', 0, 100)); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($course['nom_enseignant'] ?? ''); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($course['tags'] ?? ''); ?></td>

                                    <td class="px-6 py-4 whitespace-no-wrap">
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="cours_id" value="<?php echo $course['id']; ?>">
                                            <button type="submit" name="supprimer_cours" 
                                                    class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours ?');">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../public/assets/js/Dashbord.js"></script>

</body>
</html>