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
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-r from-blue-500 to-purple-500 py-8 px-4">
    <div class="max-w-md mx-auto w-full">
        <div class="mb-6">
            <a href="../cours/index.php" class="inline-flex items-center text-white hover:text-gray-200 transition duration-300">
                <i class="fas fa-angle-double-left mr-2"></i>
                <span>Retour à l'accueil</span>
            </a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100/90 backdrop-blur-md text-red-700 p-4 rounded-lg mb-6">
                <ul class="list-none space-y-2">
                    <?php foreach ($errors as $error): ?>
                        <li class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="bg-white/10 backdrop-blur-md border border-white/20 shadow-lg rounded-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Créez votre compte</h1>
                <p class="text-gray-200">Rejoignez Youdemy dès aujourd'hui</p>
            </div>

            <form action="register.php" method="post" class="space-y-6">
                <div>
                    <label for="username" class="block text-white text-sm font-medium mb-2">Nom complet</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-white"></i>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            placeholder="Votre nom complet" 
                            class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                            required
                        >
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-white text-sm font-medium mb-2">Adresse email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-white"></i>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="exemple@email.com" 
                            class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                            required
                        >
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-white text-sm font-medium mb-2">Mot de passe</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-white"></i>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••" 
                            class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                            required
                        >
                    </div>
                </div>

                <div>
                    <label for="confirm-password" class="block text-white text-sm font-medium mb-2">Confirmer le mot de passe</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-white"></i>
                        <input 
                            type="password" 
                            id="confirm-password" 
                            name="passwordC" 
                            placeholder="••••••••" 
                            class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300"
                            required
                        >
                    </div>
                </div>

                <div>
                    <label for="role" class="block text-white text-sm font-medium mb-2">Je souhaite m'inscrire en tant que</label>
                    <div class="relative">
                        <i class="fas fa-user-tag absolute left-3 top-1/2 transform -translate-y-1/2 text-white"></i>
                        <select 
                            id="role" 
                            name="role" 
                            class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/10 border border-white/20  focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-300 appearance-none"
                            required
                        >
                            <option value="etudiant">Étudiant</option>
                            <option value="enseignant">Enseignant</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-white pointer-events-none"></i>
                    </div>
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-white hover:bg-gray-100 text-blue-400 font-medium py-3 px-4 rounded-lg transition duration-300 transform hover:scale-[1.02]"
                >
                    Créer mon compte
                </button>

                <div class="text-center">
                    <a href="login.php" class="text-gray-200 hover:text-white text-sm transition duration-300">
                        Déjà inscrit ? <span class="underline">Connectez-vous</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
