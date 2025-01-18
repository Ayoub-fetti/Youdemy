<?php 
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Admin.php';
session_start();

// verification de la session admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../user/login.php');
    exit();
}

$db = new Database();
$pdo = $db->connect();
$user = new User($pdo);
$admin = new Admin($pdo);
$errors = [];

$users = $admin->getAllUsers();
$totalEtudiants = $admin->totalEtudiant();
$totalEnseignants = $admin->totalEnseignant();
$totalCours = $admin->totalCours();


// logic pour la suppression d'un utilisateur 
if (isset($_POST['delete_user'])) {
    $admin = new Admin($pdo);
    $userId = $_POST['user_id']; // Récupérer l'ID de l'utilisateur à supprimer
    
    // Vérifier si l'utilisateur à supprimer n'est pas un admin
    $stmt = $pdo->prepare("SELECT role FROM utilisateurs WHERE id = ?");
    $stmt->execute([$userId]);
    $userRole = $stmt->fetchColumn();
    
    if ($userRole !== 'admin') {
        $admin->deleteUser($userId);
        header('Location: dashbord.php?delete_success=1');
    } else {
        header('Location: dashbord.php?delete_error=1');
    }
    exit();
}


?>



<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Administration</title>
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
     <?php echo htmlspecialchars(ucfirst($_SESSION['user_nom'])); ?>
     </div>
    </div>
    <nav class="mt-10">
     <a class="flex items-center px-4 py-2 text-gray-700 bg-gray-100" href="dashbord.php">
      <i class="fas fa-home">
      </i>
      <span class="ml-2">
       Tableau de bord
      </span>
     </a>
     <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-100" href="gestion_cours.php">
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
   <div class="flex-1 p-6">
    <div class="flex items-center justify-between">
     <div class="relative">
      <input class="w-full px-4 py-2 pl-10 text-gray-700 bg-white border rounded-full focus:outline-none" id="searchInput" placeholder="Rechercher ici..." type="text"/>
      </i>
     </div>
    </div>
    <div class="mt-6">
     <h2 class="text-2xl font-semibold">
      <i class="fas fa-users-cog text-violet-500"></i>

      Tableau de bord
     </h2>
     <div class="grid grid-cols-1 gap-6 mt-6 sm:grid-cols-2 lg:grid-cols-3">
      <div class="p-6 bg-violet-500 rounded-lg shadow">
       <div class="flex items-center">
        <i class="text-3xl text-white fas fa-chalkboard"></i>
        <div class="ml-4">
         <h3 class="text-lg text-white font-semibold">
          Total des cours
         </h3>
         <p class="text-2xl text-white font-bold">
          <?php echo $totalCours; ?>
         </p>
        </div>
       </div>
      </div>
      <div class="p-6 bg-violet-500 rounded-lg shadow">
       <div class="flex items-center">
        <i class="text-3xl text-white fas fa-user-graduate"></i>
        <div class="ml-4">
         <h3 class="text-lg text-white font-semibold">
          Total des étudiants
         </h3>
         <p class="text-2xl text-white font-bold">
          <?php echo $totalEtudiants; ?>
         </p>
        </div>
       </div>
      </div>
      <div class="p-6 bg-violet-500 rounded-lg shadow">
       <div class="flex items-center">
        <i class="text-3xl text-white fas fa-chalkboard-teacher"></i>
        <div class="ml-4">
         <h3 class="text-lg text-white font-semibold">
          Total des enseignants
         </h3>
         <p class="text-2xl text-white font-bold">
          <?php echo $totalEnseignants; ?>
         </p>
        </div>
       </div>
      </div>
     </div>
    </div>
    <div class="mt-8">
     <h2 class="text-2xl font-semibold">
      <i class="fas fa-calendar-alt text-violet-500">
      </i>
      Utilisateurs
     </h2>
     <div class="mt-4 overflow-x-auto">
      <table class="min-w-full bg-white rounded-lg shadow">
       <thead>
        <tr>
         <th class="px-4 py-2 text-left">
          Nom
         </th>
         <th class="px-4 py-2 text-left">
          Email
         </th>
         <th class="px-4 py-2 text-left">
              Role
            </th>
            <th class="px-4 py-2 text-left">
                 Status
            </th>
            <th class="px-4 py-2 text-left">
            Rejoint
            </th>
            <th class="px-4 py-2 text-left">
            </th>
        </tr>
       </thead>
       <tbody>
        <?php foreach ($users as $user) { ?>
        <tr class="transaction-row">
         <td class="px-4 py-2 border-t">
          <?php echo htmlspecialchars($user['nom']); ?>
         </td>
         <td class="px-4 py-2 border-t">
          <?php echo htmlspecialchars($user['email']); ?>
         </td>
        <td class="px-4 py-2 border-t">
            <span class="bg-gray-200 p-1 rounded-lg inline-block w-24 text-center"><?php echo htmlspecialchars($user['role']);?></span>
            </td>
            <td class="px-4 py-2 border-t" id="status-<?php echo $user['id']; ?>" onclick="toggleUserStatus(<?php echo $user['id']; ?>)">
             <?php if ($user['role'] !== 'admin'): ?>
             <span class="<?php echo $user['status'] === 'actif' ? 'bg-green-300' : 'bg-red-300'; ?> p-1 rounded-lg inline-block w-24 text-center">
                <?php echo htmlspecialchars($user['status']); ?>
            </span>
            <?php endif; ?>
        </td>
        <td class="px-4 py-2 border-t">
                    <?php echo htmlspecialchars($user['date_creation']); ?>
        </td>
        <td class="mr-40">
            <?php if ($user['role'] !== 'admin'): ?>
                <form method="POST" action="dashbord.php">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                    <button type="submit" name="delete_user" class="bg-transparent border-0 cursor-pointer">
                        <i class="fas fa-user-slash text-red-500"></i>
                    </button>
                </form>
            <?php endif; ?>
        </td>
        </tr>
        <?php } ?>
       </tbody>
      </table>
     </div>
    </div>
   </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- bibliotheque de Js permet de creer des alertes personnalisees -->
  <script src="../../public/assets/js/Dashbord.js"></script>
 </body>
</html>
