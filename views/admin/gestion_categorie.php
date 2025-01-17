<?php
require_once '../../config/database.php';
require_once '../../models/Admin.php';
session_start();
$db = new Database();
$pdo = $db->connect();
$admin = new Admin($pdo);
$database = new Database();
$pdo = $database->connect();
$categories = $admin->getAllCategories();

if (!$pdo) {
    die("Erreur de connexion à la base de données");
}

$message = '';
$messageType = '';

// Traitement de l'ajout
if(isset($_POST['ajouter']) && !empty($_POST['nom_categorie'])) {
    $nom = htmlspecialchars($_POST['nom_categorie']);
    if($admin->addCategory($nom)) {
        $message = "Catégorie ajoutée avec succès!";
        $messageType = "success";
        $categories = $admin->getAllCategories(); // Refresh the categories list
    } else {
        $message = "Erreur lors de l'ajout de la catégorie";
        $messageType = "error";
    }
}

// Traitement de la suppression
if(isset($_POST['supprimer'])) {
    $id = $_POST['categorie_id'];
    if($admin->deleteCategory($id)) {
        $message = "Catégorie supprimée avec succès!";
        $messageType = "success";
        $categories = $admin->getAllCategories(); // Refresh the categories list
    } else {
        $message = "Impossible de supprimer cette catégorie. Elle contient peut-être des cours.";
        $messageType = "error";
    }
}

// Traitement de la modification
if(isset($_POST['modifier_submit'])) {
    $id = $_POST['edit_categorie_id'];
    $nom = htmlspecialchars($_POST['edit_nom_categorie']);
    if($admin->modifierCategorie($id, $nom)) {
        $message = "Catégorie modifiée avec succès!";
        $messageType = "success";
        $categories = $admin->getAllCategories(); // Refresh the categories list
    } else {
        $message = "Erreur lors de la modification de la catégorie";
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Categories</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.min.js"></script>
 </head>
<body class="bg-gray-100 font-sans antialiased">
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
     <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-100" href="gestion_cours">
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
     <a class="flex items-center px-4 py-2 text-gray-700 bg-gray-100" href="gestion_categorie.php">
      <i class="fas fa-chart-bar">
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
            <?php if($message): ?>
                <div class="mb-4 p-4 rounded <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <h2 class="text-2xl font-bold mb-6">Rechercher des Catégories</h2>
            <input type="text" id="searchInput" placeholder="Rechercher une catégorie" class="flex-1 p-2 border rounded focus:outline-none focus:border-purple-500 mb-12">
            <h2 class="text-2xl font-bold mb-6">Ajouter des Catégories</h2>

            <!-- Formulaire d'ajout -->
            <form action="" method="POST" class="mb-8">
                <div class="flex gap-4">
                    <input type="text" name="nom_categorie" placeholder="Nom de la catégorie" required
                           class="flex-1 p-2 border rounded focus:outline-none focus:border-purple-500">
                    <button type="submit" name="ajouter" 
                            class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                        Ajouter
                    </button>
                </div>
            </form>

            <!-- Liste des catégories -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-6 text-left">ID</th>
                            <th class="py-3 px-6 text-left">Nom</th>
                            <th class="py-3 px-6 text-center">Suppression</th>
                            <th class="py-3 px-6 text-center">Modification</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories as $categorie): ?>
                            <tr class="border-b hover:bg-gray-50 transaction-row">
                                <td class="py-4 px-6"><?php echo htmlspecialchars($categorie['id']); ?></td>
                                <td class="py-4 px-6"><?php echo htmlspecialchars($categorie['nom']); ?></td>
                                <td class="py-4 px-6 text-center">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="categorie_id" value="<?php echo $categorie['id']; ?>">
                                        <button type="submit" name="supprimer" 
                                                class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <button type="button" 
                                            onclick="openEditModal('<?php echo $categorie['id']; ?>', '<?php echo htmlspecialchars($categorie['nom']); ?>')"
                                            class="bg-orange-500 text-white px-3 py-1 rounded hover:bg-orange-600">
                                        Modifier
                                    </button>
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
    <div class="bg-white p-8 rounded-lg shadow-xl">
        <h3 class="text-xl font-bold mb-4">Modifier la catégorie</h3>
        <form id="editForm" method="POST" class="space-y-4">
            <input type="hidden" id="edit_categorie_id" name="edit_categorie_id">
            <div>
                <label for="edit_nom_categorie" class="block text-sm font-medium text-gray-700">Nom de la catégorie</label>
                <input type="text" id="edit_nom_categorie" name="edit_nom_categorie" required
                       class="mt-1 block w-full border rounded-md shadow-sm p-2 focus:border-purple-500 focus:ring-purple-500">
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeEditModal()"
                        class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Annuler
                </button>
                <button type="submit" name="modifier_submit"
                        class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script src="../../public/assets/js/categories.js"></script>
</body>
</html>