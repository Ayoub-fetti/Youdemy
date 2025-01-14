<?php 
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Admin.php';
session_start();

// Vérification de la session admin
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


?>



<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Administration</title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
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
     <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-100" href="#">
      <i class="fas fa-file-alt">
      </i>
      <span class="ml-2">
       Content
      </span>
     </a>
     <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-100" href="statistique.php">
      <i class="fas fa-chart-bar">
      </i>
      <span class="ml-2">
       Analytics
      </span>
     </a>
     <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-100" href="#">
      <i class="fas fa-thumbs-up">
      </i>
      <span class="ml-2">
       Likes
      </span>
     </a>
     <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-100" href="#">
      <i class="fas fa-comments">
      </i>
      <span class="ml-2">
       Comments
      </span>
     </a>
     <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-100" href="#">
      <i class="fas fa-share">
      </i>
      <span class="ml-2">
       Share
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
      <input class="w-full px-4 py-2 pl-10 text-gray-700 bg-white border rounded-full focus:outline-none" placeholder="Search here..." type="text"/>
      <!-- <i class="absolute left-0 ml-3 text-gray-500 fas fa-search"> -->
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
        </tr>
       </thead>
       <tbody>
        <?php foreach ($users as $user) { ?>
        <tr>
         <td class="px-4 py-2 border-t">
          <?php echo htmlspecialchars($user['nom']); ?>
         </td>
         <td class="px-4 py-2 border-t">
          <?php echo htmlspecialchars($user['email']); ?>
         </td>
         <td class="px-4 py-2 border-t">
             <?php echo htmlspecialchars($user['status']); ?>
             </td>
             <td class="px-4 py-2 border-t">
                 <?php echo htmlspecialchars($user['role']); ?>
                </td>
                <td class="px-4 py-2 border-t">
                    <?php echo htmlspecialchars($user['date_creation']); ?>
         </td>
        </tr>
        <?php } ?>
       </tbody>
      </table>
     </div>
    </div>
   </div>
  </div>
 </body>
</html>
