<?php
require_once __DIR__ . '/../../models/Cours.php';
require_once __DIR__ . '/../../models/CoursSpecifique.php';
require_once __DIR__ . '/../../models/CoursPDF.php';
require_once __DIR__ . '/../../models/CoursVideo.php';
require_once __DIR__ . '/../../config/Database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: /mes-cours');
    exit();
}

$coursId = $_GET['id'];
$userId = $_SESSION['user_id'];
$database = new Database();
$pdo = $database->connect();

// verifier si student est inscrit a ce cours
$stmt = $pdo->prepare("SELECT c.*, i.date_inscription   
                       FROM cours c 
                       INNER JOIN inscriptions i ON c.id = i.cours_id 
                       WHERE c.id = ? AND i.etudiant_id = ?");
$stmt->execute([$coursId, $userId]);
$cours = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cours) {
    header('Location: mes_cours.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du cours - <?php echo htmlspecialchars($cours['titre']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.1.81/build/pdf.min.js"></script> -->
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($cours['titre']); ?></h1>
            <p class="text-gray-600 mb-6"><?php echo htmlspecialchars($cours['description']); ?></p>
            
            <div class="mt-8">
                <?php
                // le type de cours (PDF ou Video)
                $type = strpos($cours['contenu'], '.pdf') !== false ? 'pdf' : 'video';
                
                if ($type === 'pdf') {
                    // Afficher le PDF dans un iframe avec fallback
                    $pdfPath = $cours['contenu'];
                    
                    // Nettoyer le chemin pour obtenir juste le nom du fichier
                    $fileName = basename($pdfPath);
                    // Construire le nouveau chemin relatif pour l'URL
                    $pdfUrl = '/uploads/pdfs/' . $fileName;
                    // Construire le chemin physique complet
                    $physicalPath = __DIR__ . '/../../public/uploads/pdfs/' . $fileName;

                    if (!file_exists($physicalPath)) {
                        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <strong class="font-bold">Erreur :</strong>
                                <span class="block sm:inline">Le fichier PDF n\'est pas trouvé. Chemin : ' . htmlspecialchars($physicalPath) . '</span>
                              </div>';
                    } else {
                    ?>
                    <div class="w-full h-screen bg-gray-100 rounded-lg shadow relative">
                        <canvas id="pdfViewer" class="w-full h-full"></canvas>
                    <!-- ici khass ybaan contenu de pdf -->
                    </div>

                    <?php
                    }
                } else {
                    // Transformer l'URL YouTube en URL d'intégration
                    $videoUrl = $cours['contenu'];
                    if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
                        // Extraire l'ID de la vidéo YouTube
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $videoUrl, $matches);
                        if (isset($matches[1])) {
                            $videoId = $matches[1];
                            $embedUrl = "https://www.youtube.com/embed/" . $videoId;
                        } else {
                            $embedUrl = $videoUrl;
                        }
                    } else {
                        $embedUrl = $videoUrl;
                    }
                    ?>
                    <div class="aspect-w-16 aspect-h-9">
                        <iframe 
                            src="<?php echo htmlspecialchars($embedUrl); ?>"
                            class="w-full h-[600px] rounded-lg shadow"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                        </iframe>
                    </div>
                    <?php
                }
                ?>
            </div>

            <div class="mt-6 text-gray-600">
                <p>Date d'inscription: <?php echo date('d/m/Y', strtotime($cours['date_inscription'])); ?></p>
            </div>

            <div class="mt-8 flex justify-center">
                <a href="mes_cours.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                    Terminer le cours
                </a>
            </div>
        </div>
    </div>

</body>
</html>