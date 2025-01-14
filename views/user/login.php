<?php 
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';

session_start();

$db = new Database();
$pdo = $db->connect();
$user = new User($pdo);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // validation 
    if (empty($email)) {
        $errors[] = 'Veuillez entrer votre adresse e-mail';
    }
    if (empty($password)) {
        $errors[] = 'Veuillez entrer votre mot de passe';
    }

    if (empty($errors)) {
        if ($user->login($email, $password)) {
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_nom'] = $user->getName();
            $_SESSION['user_role'] = $user->getRole();
            
            // Check if user is inactive
            if ($user->getRole() === 'etudiant' && $user->getstatus() === 'inactif') {
                header('Location: ../Confirmation/inactif.php');
                exit();
            }
            
            // Check if user is an inactive instructor
            if ($user->getRole() === 'enseignant' && $user->getstatus() === 'inactif') {
                header('Location: ../Confirmation/verification.php');
                exit();
            }
            
            // redirection vers le role 
            switch($user->getRole()) {
                case 'admin':
                    header('Location: ../admin/dashbord.php');
                    break;
                case 'etudiant':
                    header('Location: ../cours/catalogue.php');
                    break;
                case 'enseignant':
                    header('Location: ../enseignant/enseignant_Dash.php');
                    break;
                
                default:
                    header("Location: ../cours/catalogue.php");
            }
            exit();
        } else {
            $errors[] = 'Adresse e-mail ou mot de passe incorrect';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connecter</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-r from-blue-500 to-purple-500">
  <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach($errors as $error): ?>
                    <p><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
                <?php $errors = []; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php 
                echo htmlspecialchars($_SESSION['success_message']); 
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>


  <form action="login.php" method="post" class="relative w-[400px] h-[500px] bg-white/10 backdrop-blur-md border border-white/20 shadow-lg rounded-lg px-10 py-12 text-white">
  <a href="../cours/index.php"> <i><i class="fas fa-angle-double-left"></i></i></a>
    <h3 class="text-center text-2xl font-medium">Bienvenue</h3>

    <label for="username" class="block mt-6 text-lg font-medium">Email</label>
    <input type="text" id="username" name="email" placeholder="Votre email" class="w-full mt-2 p-3 rounded bg-white/10 text-sm placeholder-gray-300">

    <label for="password" class="block mt-6 text-lg font-medium">Mot de passe</label>
    <input type="password" id="password" name="password" placeholder="Votre mot de passe" class="w-full mt-2 p-3 rounded bg-white/10 text-sm placeholder-gray-300">

    <a class="text-xs underline mt-6 block text-center" href="register.php">Vous n'avez pas un compte ?</a>
    <button class="w-full mt-8 p-3 bg-white text-gray-900 font-semibold rounded hover:bg-gray-200 transition">Se connecter</button>

    </div>
  </form>
</body>
</html>
