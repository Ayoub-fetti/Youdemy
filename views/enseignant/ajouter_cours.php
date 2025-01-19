<?php
    require_once __DIR__ . '/../../models/CoursPDF.php';
    require_once __DIR__ . '/../../models/CoursVideo.php';
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Enseignant.php';

    $database = new Database();
    $pdo = $database->connect();

    // Recuperer l'ID de l'enseignant
    session_start();
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'enseignant') {
        header('Location: /login.php');
        exit;
    }
    $enseignant_id = $_SESSION['user_id'];
    $message = '';

    // Check for success message from URL
    if (isset($_GET['success']) && $_GET['success'] == '1') {
        $message = "Le cours a été ajouté avec succès !";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $titre = $_POST['titre'];
            $description = $_POST['description'];
            $categorie_id = $_POST['categorie_id'];
            $type_cours = $_POST['type_cours'];
            $enseignant_id = $_SESSION['user_id'];
            
            if ($type_cours === 'pdf') {
                if (!isset($_FILES["fichier_pdf"]) || $_FILES["fichier_pdf"]["error"] !== UPLOAD_ERR_OK) {
                    throw new Exception("Erreur : Veuillez sélectionner un fichier PDF valide.");
                }

                $target_dir = __DIR__ . "/../../public/uploads/pdfs/";
                if (!file_exists($target_dir)) {
                    if (!mkdir($target_dir, 0777, true)) {
                        throw new Exception("Erreur système : Impossible de créer le dossier de destination.");
                    }
                }

                $target_file = $target_dir . basename($_FILES["fichier_pdf"]["name"]);
                if (!move_uploaded_file($_FILES["fichier_pdf"]["tmp_name"], $target_file)) {
                    throw new Exception("Erreur lors du téléchargement du fichier.");
                }

                // Store relative path in database
                $relative_path = "/uploads/pdfs/" . basename($_FILES["fichier_pdf"]["name"]);
                $cours = new CoursPDF($titre, $description, $categorie_id, $enseignant_id, $relative_path);
            } else {
                if (empty($_POST['url_video'])) {
                    throw new Exception("L'URL de la vidéo est requise.");
                }
                $url_video = $_POST['url_video'];
                $cours = new CoursVideo($titre, $description, $categorie_id, $enseignant_id, $url_video);
            }
            
            $query = "INSERT INTO cours (titre, description, contenu, categorie_id, enseignant_id) 
                    VALUES (:titre, :description, :contenu, :categorie_id, :enseignant_id)";
            
            $stmt = $pdo->prepare($query);
            if (!$stmt->execute([
                ':titre' => $cours->getTitre(),
                ':description' => $cours->getDescription(),
                ':contenu' => $cours->getContenu(),
                ':categorie_id' => $cours->getCategorieId(),
                ':enseignant_id' => $cours->getEnseignantId()
            ])) {
                throw new Exception("Erreur lors de l'enregistrement du cours.");
            }

            // recuperer id du cours maintenant cree
            $cours_id = $pdo->lastInsertId();

            // Handle tags
            if (isset($_POST['tags_list']) && !empty($_POST['tags_list'])) {
                $tags = explode(',', $_POST['tags_list']);
                $enseignant = new Enseignant();
                $enseignant->handleTags($cours_id, $tags);
            }

            $message = "Cours ajouté avec succès!";
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit;
        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
        }
    }

    // la liste des categories pour le formulaire
    $query_categories = "SELECT * FROM categories ORDER BY nom";
    $stmt_categories = $pdo->prepare($query_categories);
    $stmt_categories->execute();
    $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Cours</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
    <style>
        .tag-container {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            min-height: 40px;
        }

        .tag {
            background-color: #4F46E5;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            margin: 2px;
        }

        .tag span {
            margin-right: 5px;
        }

        .tag button {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl text-indigo-600 font-bold text-gray-800">Ajouter un Cours</h1>
            <a href="enseignant_Dash.php" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Tableau de bord</a>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="p-4 mb-4 rounded-lg <?php echo strpos($message, 'Erreur') === false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulaire d'ajout de cours -->
        <div class="bg-white rounded-lg shadow-md mb-8">
            <div class="border-b border-gray-200 p-4">
                <h2 class="text-xl font-semibold text-gray-800">Ajouter un nouveau cours</h2>
            </div>
            <div class="p-6">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="titre" class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                        <input type="text" class="w-full rounded-md bg-white border-2 border-black shadow-sm focus:border-black focus:ring-black" id="titre" name="titre" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea class="w-full rounded-md bg-white border-2 border-black shadow-sm focus:border-black focus:ring-black" id="description" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="categorie_id" class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                        <select class="w-full rounded-md bg-white border-2 border-black shadow-sm focus:border-black focus:ring-black" id="categorie_id" name="categorie_id" required>
                            <option value="">Sélectionner une catégorie</option>
                            <?php foreach ($categories as $categorie): ?>
                                <option value="<?php echo htmlspecialchars($categorie['id']); ?>">
                                    <?php echo htmlspecialchars($categorie['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="type_cours" class="block text-sm font-medium text-gray-700 mb-1">Type de cours</label>
                        <select class="w-full rounded-md bg-white border-2 border-black shadow-sm focus:border-black focus:ring-black" id="type_cours" name="type_cours" required>
                            <option value="">Sélectionner un type</option>
                            <option value="pdf">PDF</option>
                            <option value="video">Vidéo</option>
                        </select>
                    </div>
                    
                    <div id="pdf_upload" class="mb-4 hidden">
                        <label for="fichier_pdf" class="block text-sm font-medium text-gray-700 mb-1">Fichier PDF</label>
                        <input type="file" class="w-full rounded-md bg-white border-2 border-black shadow-sm focus:border-black focus:ring-black" id="fichier_pdf" name="fichier_pdf" accept=".pdf">
                    </div>
                    
                    <div id="video_url" class="mb-4 hidden">
                        <label for="url_video" class="block text-sm font-medium text-gray-700 mb-1">URL de la vidéo</label>
                        <input type="url" class="w-full rounded-md bg-white border-2 border-black shadow-sm focus:border-black focus:ring-black" id="url_video" name="url_video">
                    </div>
                    
                    <div class="mb-4">
                        <label for="tags" class="block text-sm font-medium text-gray-700 mb-1">Tags (Appuyez sur virgule après chaque tag)</label>
                        <div class="tag-container" id="tag-container"></div>
                        <input type="text" class="w-full rounded-md bg-white border-2 border-black shadow-sm focus:border-black focus:ring-black mt-2" name="tags" id="tags" placeholder="Ajouter des tags">
                        <input type="hidden" name="tags_list" id="tags_list">
                    </div>
                    
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Ajouter le cours
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="../../public/assets/js/Enseignant.js"></script>
</body>
</html>
