<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-r from-blue-500 to-purple-500">

  <form action="register.php" method="post" class="relative w-[400px] h-auto bg-white/10 backdrop-blur-md border border-white/20 shadow-lg rounded-lg px-6 py-8 text-white">
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
