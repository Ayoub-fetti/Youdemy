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

// Récupération de tous les tags disponibles
$all_tags = $admin->getAllTags();

// Traitement de la suppression d'un tag
if(isset($_POST['supprimer_tag'])) {
    $tag_id = $_POST['tag_id'] ?? 0;
    if($tag_id && $admin->deleteTag($tag_id)) {
        $message = "Tag supprimé avec succès!";
        $messageType = "success";
        // Rafraîchir la liste des tags
        $all_tags = $admin->getAllTags();
        // Rafraîchir la liste des cours pour mettre à jour l'affichage des tags
        $cours = $admin->getAllCours();
    } else {
        $message = "Erreur lors de la suppression du tag.";
        $messageType = "error";
    }
}

// Traitement de l'ajout d'un tag
if(isset($_POST['ajouter_tag'])) {
    $tag_nom = trim($_POST['tag_nom'] ?? '');
    if(!empty($tag_nom)) {
        if($admin->addTag($tag_nom)) {
            $message = "Tag ajouté avec succès!";
            $messageType = "success";
            // Rafraîchir la liste des tags
            $all_tags = $admin->getAllTags();
        } else {
            $message = "Erreur lors de l'ajout du tag.";
            $messageType = "error";
        }
    } else {
        $message = "Le nom du tag ne peut pas être vide.";
        $messageType = "error";
    }
}

// Traitement de la modification des tags
if(isset($_POST['modifier_tags'])) {
    $cours_id = $_POST['cours_id'] ?? 0;
    $tags = $_POST['tags'] ?? [];
    
    if($cours_id && $admin->updateCourseTags($cours_id, $tags)) {
        $message = "Tags modifiés avec succès!";
        $messageType = "success";
        // Rafraîchir la liste des cours
        $cours = $admin->getAllCours();
    } else {
        $message = "Erreur lors de la modification des tags. Veuillez réessayer.";
        $messageType = "error";
    }
}

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
                <div class="form-group">
                    <input class="w-56 px-4 py-2 mb-8 pl-10 text-gray-700 bg-white border rounded-full focus:outline-none" id="searchInput" placeholder="Rechercher ici..." type="text" style="display:inline-block; width:auto;">
                    <form method="POST" class="inline" style="display:inline-block; width:auto;">
                        <input name="tag_nom" class="w-56 px-4 py-2 pl-4 text-gray-700 bg-white border rounded-full focus:outline-none" placeholder="Nom du tag..." type="text"/>
                        <button type="submit" name="ajouter_tag" class="px-4 py-2 bg-purple-600 text-white rounded-full hover:bg-purple-700 focus:outline-none">
                            Ajouter
                        </button>
                    </form>
                </div>
            
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
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($course['titre']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($course['description']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($course['nom_enseignant']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($course['tags'] ?? ''); ?></td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <button type="button" onclick="openEditModal(<?php echo $course['id']; ?>, '<?php echo htmlspecialchars($course['tags'] ?? '', ENT_QUOTES); ?>')" 
                                                    class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                                <i class="fas fa-tags"></i>
                                            </button>
                                            <form method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce cours?');">
                                                <input type="hidden" name="cours_id" value="<?php echo $course['id']; ?>">
                                                <button type="submit" name="supprimer_cours" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de modification -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-xl w-96">
            <h3 class="text-xl font-bold mb-4">Modifier les tags</h3>
            <form id="editForm" method="POST" class="space-y-4">
                <input type="hidden" name="cours_id" id="editCourseId">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Sélectionner les tags:</label>
                    <div class="max-h-48 overflow-y-auto p-2 border rounded">
                        <?php foreach ($all_tags as $tag): ?>
                        <div class="flex items-center justify-between mb-2 p-2 hover:bg-gray-50">
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" name="tags[]" value="<?php echo $tag['id']; ?>" 
                                       id="tag_<?php echo $tag['id']; ?>"
                                       class="tag-checkbox h-4 w-4 text-blue-600 rounded border-gray-300">
                                <label for="tag_<?php echo $tag['id']; ?>" class="text-sm text-gray-700">
                                    <?php echo htmlspecialchars($tag['nom']); ?>
                                </label>
                            </div>
                            <button type="button" onclick="deleteTag(<?php echo $tag['id']; ?>)" 
                                    class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" onclick="closeEditModal()" 
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                        Annuler
                    </button>
                    <button type="submit" name="modifier_tags" 
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Formulaire caché pour la suppression des tags -->
    <form id="deleteTagForm" method="POST" style="display: none;">
        <input type="hidden" name="tag_id" id="deleteTagId">
        <input type="hidden" name="supprimer_tag" value="1">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../public/assets/js/gestion_cours.js"></script>
    <script src="../../public/assets/js/Dashbord.js"></script>
</body>
</html>