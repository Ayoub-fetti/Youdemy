<?php 
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';

session_start();

$db = new Database();
$pdo = $db->connect();
$user = new User($pdo);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = ($_POST['email']);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL); 
    $password = $_POST['password'];

   
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
            
            // Check if etudiant is inactive
            if ($user->getRole() === 'etudiant' && $user->getstatus() === 'inactif') {
                header('Location: ../Confirmation/inactif.php');
                exit();
            }
            
            // Check if enseignants is an inactive instructor
            if ($user->getRole() === 'enseignant' && $user->getstatus() === 'inactif') {
                header('Location: ../Confirmation/verification.php');
                exit();
            }
            
   
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
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Youdemy</title>
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
                <h1 class="text-3xl font-bold text-white mb-2">Bienvenue sur Youdemy</h1>
                <p class="text-gray-200">Connectez-vous pour accéder à vos cours</p>
            </div>

            <form action="login.php" method="post" class="space-y-6">
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

                <button 
                    type="submit" 
                    class="w-full bg-white hover:bg-gray-100 text-blue-400 font-medium py-3 px-4 rounded-lg transition duration-300 transform hover:scale-[1.02]"
                >
                    Se connecter
                </button>

                <div class="text-center">
                    <a href="register.php" class="text-gray-200 hover:text-white text-sm transition duration-300">
                        Vous n'avez pas de compte ? <span class="underline">Inscrivez-vous</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
