<?php
    require_once __DIR__ . '/../../models/CoursPDF.php';
    require_once __DIR__ . '/../../models/CoursVideo.php';
    require_once __DIR__ . '/../../config/database.php';

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

                            $target_dir = __DIR__ . "/../../uploads/pdfs/";
                            if (!file_exists($target_dir)) {
                                if (!mkdir($target_dir, 0777, true)) {
                                    throw new Exception("Erreur système : Impossible de créer le dossier de destination.");
                                }
                            }

                            $target_file = $target_dir . basename($_FILES["fichier_pdf"]["name"]);
                            if (!move_uploaded_file($_FILES["fichier_pdf"]["tmp_name"], $target_file)) {
                                throw new Exception("Erreur lors du téléchargement du fichier.");
                            }

                            $cours = new CoursPDF($titre, $description, $categorie_id, $enseignant_id, $target_file);
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

                        $message = "Cours ajouté avec succès!";
                        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
                        exit;
                    } catch (Exception $e) {
                        $message = "Erreur : " . $e->getMessage();
                    }
                }
                
    // Recuperer la liste des cours de l'enseignant
    $query = "SELECT c.*, cat.nom as categorie_nom,
    CASE 
      WHEN c.contenu LIKE '%.pdf' THEN 'pdf'
      ELSE 'video'
    END as type
    FROM cours c 
    LEFT JOIN categories cat ON c.categorie_id = cat.id 
    WHERE c.enseignant_id = :enseignant_id 
    ORDER BY c.date_creation DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([':enseignant_id' => $enseignant_id]);
$cours_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recupérer la liste des categories pour le formulaire
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
    <title>Tableau de bord Enseignant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
    <div class="container mt-5">
        <h1>Tableau de bord Enseignant</h1>
        
        <?php if (!empty($message)): ?>
            <div class="alert <?php echo strpos($message, 'Erreur') === false ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulaire d'ajout de cours -->
        <div class="card mb-4">
            <div class="card-header">
                <h2>Ajouter un nouveau cours</h2>
            </div>
            <div class="card-body">
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre</label>
                        <input type="text" class="form-control" id="titre" name="titre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="categorie_id" class="form-label">Catégorie</label>

                        <select class="form-control" id="categorie_id" name="categorie_id" required>
                            <option value="">Sélectionner une catégorie</option>
                            <?php foreach ($categories as $categorie): ?>
                                <option value="<?php echo htmlspecialchars($categorie['id']); ?>">
                                    <?php echo htmlspecialchars($categorie['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_cours" class="form-label">Type de cours</label>
                        <select class="form-control" id="type_cours" name="type_cours" required>
                            <option value="">Sélectionner un type</option>
                            <option value="pdf">PDF</option>
                            <option value="video">Vidéo</option>
                        </select>
                    </div>
                    
                    <div id="pdf_upload" class="mb-3" style="display: none;">
                        <label for="fichier_pdf" class="form-label">Fichier PDF</label>
                        <input type="file" class="form-control" id="fichier_pdf" name="fichier_pdf" accept=".pdf">
                    </div>
                    
                    <div id="video_url" class="mb-3" style="display: none;">
                        <label for="url_video" class="form-label">URL de la vidéo</label>
                        <input type="url" class="form-control" id="url_video" name="url_video">
                    </div>
                    
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags (séparés par des virgules)</label>
                        <input type="text" class="form-control" id="tags" name="tags">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Ajouter le cours</button>
                </form>
            </div>
        </div>
        
        <!-- Liste des cours -->
        <div class="card">
            <div class="card-header">
                <h2>Mes cours</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Contenu</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cours_list as $cours): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cours['titre']); ?></td>
                                <td><?php echo htmlspecialchars($cours['description']); ?></td>
                                <td><?php echo htmlspecialchars($cours['type']); ?></td>
                                <td>
                                    <?php if ($cours['type'] === 'pdf'): ?>
                                        <a href="<?php echo htmlspecialchars($cours['contenu']); ?>" target="_blank" class="btn btn-sm btn-info">Voir le PDF</a>
                                    <?php else: ?>
                                        <a href="<?php echo htmlspecialchars($cours['contenu']); ?>" target="_blank" class="btn btn-sm btn-info">Voir la vidéo</a>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($cours['date_creation']); ?></td>
                                <td>
                                    <a href="edit_cours.php?id=<?php echo $cours['id']; ?>" class="btn btn-sm btn-primary">Modifier</a>
                                    <a href="delete_cours.php?id=<?php echo $cours['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours?')">Supprimer</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('type_cours').addEventListener('change', function() {
            const pdfUpload = document.getElementById('pdf_upload');
            const videoUrl = document.getElementById('video_url');
            
            if (this.value === 'pdf') {
                pdfUpload.style.display = 'block';
                videoUrl.style.display = 'none';
                document.getElementById('fichier_pdf').required = true;
                document.getElementById('url_video').required = false;
            } else if (this.value === 'video') {
                pdfUpload.style.display = 'none';
                videoUrl.style.display = 'block';
                document.getElementById('fichier_pdf').required = false;
                document.getElementById('url_video').required = true;
            } else {
                pdfUpload.style.display = 'none';
                videoUrl.style.display = 'none';
                document.getElementById('fichier_pdf').required = false;
                document.getElementById('url_video').required = false;
            }
        });
    </script>
</body>
</html>