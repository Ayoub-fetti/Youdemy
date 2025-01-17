<?php
require_once __DIR__ . '/../../models/Enseignant.php';
require_once __DIR__ . '/../../config/database.php';

session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'enseignant') {
    header('Location: /login.php');
    exit;
}

$database = new Database();
$pdo = $database->connect();
$enseignant = new Enseignant($_SESSION['user_id'], $pdo);

// Message d'erreur
$error_message = isset($_GET['error']) ? $_GET['error'] : '';

// recuperer les informations du cours
$cours_id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$cours_id) {
    header('Location: enseignant_dash.php');
    exit;
}

// Récupérer les catégories
$query_categories = "SELECT * FROM categories ORDER BY nom";
$stmt_categories = $pdo->prepare($query_categories);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les informations du cours
$query = "SELECT c.*, GROUP_CONCAT(t.nom) as tags
          FROM cours c 
          LEFT JOIN cours_tags ct ON c.id = ct.cours_id 
          LEFT JOIN tags t ON ct.tag_id = t.id 
          WHERE c.id = ? AND c.enseignant_id = ?
          GROUP BY c.id";
$stmt = $pdo->prepare($query);
$stmt->execute([$cours_id, $_SESSION['user_id']]);
$cours = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cours) {
    header('Location: enseignant_dash.php');
    exit;
}

// Traiter la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enseignant->modifierCours($cours_id);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le cours</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Modifier le cours</h1>
        
        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="titre" class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                    <input type="text" class="w-full rounded-md bg-white border-2 border-black shadow-sm focus:border-black focus:ring-black" 
                           id="titre" name="titre" required value="<?php echo htmlspecialchars($cours['titre']); ?>">
                </div>
                
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea class="w-full rounded-md bg-white border-2 border-black shadow-sm focus:border-black focus:ring-black" 
                              id="description" name="description" rows="3" required><?php echo htmlspecialchars($cours['description']); ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="categorie_id" class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                    <select class="w-full rounded-md bg-white border-2 border-black shadow-sm focus:border-black focus:ring-black" 
                            id="categorie_id" name="categorie_id" required>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?php echo htmlspecialchars($categorie['id']); ?>" 
                                    <?php echo $categorie['id'] == $cours['categorie_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($categorie['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="type_cours" class="block text-sm font-medium text-gray-700 mb-1">Type de cours</label>
                    <select class="w-full rounded-md bg-white border-2 border-black shadow-sm focus:border-black focus:ring-black" 
                            id="type_cours" name="type_cours" required>
                        <option value="pdf" <?php echo strpos($cours['contenu'], '.pdf') !== false ? 'selected' : ''; ?>>PDF</option>
                        <option value="video" <?php echo strpos($cours['contenu'], '.pdf') === false ? 'selected' : ''; ?>>Vidéo</option>
                    </select>
                </div>
                
                <div id="pdf_upload" class="mb-4 <?php echo strpos($cours['contenu'], '.pdf') !== false ? '' : 'hidden'; ?>">
                    <label for="fichier_pdf" class="block text-sm font-medium text-gray-700 mb-1">Nouveau fichier PDF (optionnel)</label>
                    <input type="file" class="w-full rounded-md bg-white border-2 border-black shadow-sm focus:border-black focus:ring-black" 
                           id="fichier_pdf" name="fichier_pdf" accept=".pdf">
                    <?php if (strpos($cours['contenu'], '.pdf') !== false): ?>
                        <p class="mt-1 text-sm text-gray-500">PDF actuel : <a href="<?php echo htmlspecialchars($cours['contenu']); ?>" 
                           target="_blank" class="text-indigo-600 hover:text-indigo-900">Voir le PDF</a></p>
                    <?php endif; ?>
                </div>
                
                <div id="video_url" class="mb-4 <?php echo strpos($cours['contenu'], '.pdf') === false ? '' : 'hidden'; ?>">
                    <label for="url_video" class="block text-sm font-medium text-gray-700 mb-1">URL de la vidéo</label>
                    <input type="url" class="w-full rounded-md bg-white border-2 border-black shadow-sm focus:border-black focus:ring-black" 
                           id="url_video" name="url_video" value="<?php echo strpos($cours['contenu'], '.pdf') === false ? htmlspecialchars($cours['contenu']) : ''; ?>">
                </div>
                
                <div class="mb-4">
                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-1">Tags (séparés par des virgules)</label>
                    <input type="text" class="w-full rounded-md bg-white border-2 border-black shadow-sm focus:border-black focus:ring-black" 
                           id="tags" name="tags" value="<?php echo htmlspecialchars($cours['tags'] ?? ''); ?>">
                </div>
                
                <div class="flex justify-end space-x-4">
                    <a href="enseignant_dash.php" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Annuler</a>
                    <button type="submit" class="px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.getElementById('type_cours').addEventListener('change', function() {
            const pdfUpload = document.getElementById('pdf_upload');
            const videoUrl = document.getElementById('video_url');
            
            if (this.value === 'pdf') {
                pdfUpload.classList.remove('hidden');
                videoUrl.classList.add('hidden');
            } else {
                pdfUpload.classList.add('hidden');
                videoUrl.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
