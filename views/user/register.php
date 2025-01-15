<?php 
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
session_start();

$db = new Database();
$pdo = $db->connect();
$user = new User($pdo);
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nom = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $passwordC = $_POST['passwordC'];
  $role = $_POST['role'];

  // Validation
  if (empty($nom)) {
      $errors['name'] = "Le nom d'utilisateur est requis";
  }
  if (empty($email)) {
      $errors['email'] = "L'email est requis";
  }
  if (empty($password)) {
      $errors['password'] = "Le mot de passe est requis";
  }
  if ($password !== $passwordC) {
      $errors['passwordC'] = "Les mots de passe ne correspondent pas";
  }
  if (empty($role)) {
    $errors['role'] = "Le rôle est requis";
  }

  if (empty($errors)) {
      // Utilisation de la méthode register de la classe User
      $result = $user->register($nom, $email, $password, $role);
      
      if ($result['success']) {
          // Enregistrement réussi
          $_SESSION['success_message'] = $result['message'];
          header("Location: login.php");
          exit();
      } else {
          // Erreur lors de l'enregistrement
          $errors['register'] = $result['message'];
      }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="flex flex-col items-center min-h-screen bg-gradient-to-r from-blue-500 to-purple-500">

<div class="w-full max-w-md mt-6 px-4">
  <?php if (!empty($errors)): ?>
    <div class=" text-black p-4 rounded-md mb-4">
      <ul class="list-disc ml-5">
        <?php foreach ($errors as $error): ?>
          <li><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

</div>

  <form action="register.php" method="post" class="relative w-[400px] bg-white/10 backdrop-blur-md border border-white/20 shadow-lg rounded-lg px-6 py-8 text-white">
    <a href="../cours/index.php" class="absolute top-4 left-4 text-white">
      <i class="fas fa-angle-double-left"></i>
    </a>
    <h3 class="text-center text-xl font-medium">Inscription</h3>

    <label for="username" class="block mt-4 text-sm font-medium">Nom</label>
    <input type="text" id="username" placeholder="Votre nom" name="username" class="w-full mt-1 p-2 rounded bg-white/10 text-sm placeholder-gray-300">

    <label for="email" class="block mt-4 text-sm font-medium">Email</label>
    <input type="text" id="email" placeholder="Votre email" name="email" class="w-full mt-1 p-2 rounded bg-white/10 text-sm placeholder-gray-300">

    <label for="password" class="block mt-4 text-sm font-medium">Mot de passe</label>
    <input type="password" id="password" placeholder="Votre mot de passe" name="password" class="w-full mt-1 p-2 rounded bg-white/10 text-sm placeholder-gray-300">

    <label for="confirm-password" class="block mt-4 text-sm font-medium">Confirmer mot de passe</label>
    <input type="password" id="confirm-password" placeholder="Confirmer votre mot de passe" name="passwordC" class="w-full mt-1 p-2 rounded bg-white/10 text-sm placeholder-gray-300">

    <label for="role" class="block mt-4 text-sm font-medium">Rôle</label>
    <select id="role" name="role" class="w-full mt-1 p-2 rounded bg-white/10 text-sm text-gray-900">
      <option value="etudiant">Étudiant</option>
      <option value="enseignant">Enseignant</option>
    </select>

    <a class="text-xs underline mt-4 block text-center" href="login.php">Vous avez déjà un compte ?</a>
    <button class="w-full mt-6 p-2 bg-white text-gray-900 font-semibold rounded hover:bg-gray-200 transition">S'inscrire</button>
  </form>

</body>
</html>
