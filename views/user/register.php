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
  <form class="relative w-[400px] h-[650px] bg-white/10 backdrop-blur-md border border-white/20 shadow-lg rounded-lg px-10 py-12 text-white">
    <h3 class="text-center text-2xl font-medium">Inscription</h3>

    <label for="username" class="block mt-6 text-lg font-medium">Email</label>
    <input type="text" id="username" placeholder="Votre email" class="w-full mt-2 p-3 rounded bg-white/10 text-sm placeholder-gray-300">

    <label for="password" class="block mt-6 text-lg font-medium">Mot de passe</label>
    <input type="password" id="password" placeholder="Votre mot de passe" class="w-full mt-2 p-3 rounded bg-white/10 text-sm placeholder-gray-300">

    <label for="confirm-password" class="block mt-6 text-lg font-medium">Confirmer votre mot de passe</label>
    <input type="password" id="confirm-password" placeholder="Confirmer votre mot de passe" class="w-full mt-2 p-3 rounded bg-white/10 text-sm placeholder-gray-300">

    <label for="role" class="block mt-6 text-lg font-medium">Choisir votre rôle</label>
    <select id="role" class="w-full mt-2 p-3 rounded bg-white/10 text-sm text-gray-900">
      <option value="etudiant">Étudiant</option>
      <option value="enseignant">Enseignant</option>
    </select>

   
    <a class="text-xs underline mt-6 block text-center" href="register.php">Vous n'avez pas un compte ?</a>
    <button class="w-full mt-8 p-3 bg-white text-gray-900 font-semibold rounded hover:bg-gray-200 transition">S'inscrire</button>
  </form>

</body>
</html>