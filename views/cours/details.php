<?php
require_once __DIR__ . '/../../models/Cours.php';
require_once __DIR__ . '/../../models/Etudiant.php';
require_once __DIR__ . '/../../models/User.php';
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
$etudiant = new Etudiant( $pdo);
$cours = $etudiant->getCoursInscrit();



// traitement pour terminer le ccours 
 if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['terminer_cours'])) {
    
    $etudiant->terminerCours($coursId);
    header('Location: mes_cours.php');
    exit();
}


// verifier si student est inscrit a ce cours
$stmt = $pdo->prepare("SELECT cours.*, inscriptions.date_inscription   
                       FROM cours 
                       INNER JOIN inscriptions  ON cours.id = inscriptions.cours_id 
                       WHERE cours.id = ? AND inscriptions.etudiant_id = ?");
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        // Définir le worker PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 lg:p-8">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <h1 class="text-2xl sm:text-3xl font-bold text-center sm:text-left"><?php echo htmlspecialchars($cours['titre']); ?></h1>
                <a href="mes_cours.php" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition duration-300 text-sm w-full sm:w-auto text-center">
                    <i class="fas fa-arrow-left mr-2"></i>Retour aux cours
                </a>
            </div>
            
            <p class="text-gray-600 mb-6 text-sm sm:text-base"><?php echo htmlspecialchars($cours['description']); ?></p>
            
            <div class="mt-8">
                <?php
                $type = strpos($cours['contenu'], '.pdf') !== false ? 'pdf' : 'video';
                
                if ($type === 'pdf') {
                    $pdfPath = $cours['contenu'];
                    if (strpos($pdfPath, 'C:') === 0) {
                        $pdfPath = str_replace('C:/laragon/www/Youdemy/', '', $pdfPath);
                    }
                    $pdfUrl = '/uploads/pdfs/' . basename($pdfPath);
                    $physicalPath = __DIR__ . '/../../public' . $pdfUrl;

                    if (!file_exists($physicalPath)) {
                        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <strong class="font-bold">Erreur :</strong>
                                <span class="block sm:inline">Le fichier PDF n\'est pas trouvé.</span>
                                <div class="mt-2">
                                    <p>Détails de débogage :</p>
                                    <ul class="list-disc pl-5">
                                        <li>Chemin relatif : ' . htmlspecialchars($pdfUrl) . '</li>
                                        <li>Chemin physique : ' . htmlspecialchars($physicalPath) . '</li>
                                    </ul>
                                </div>
                              </div>';
                    } else {
                        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                        $fullPdfUrl = $baseUrl . '/Youdemy/public' . $pdfUrl;
                    ?>
                    <div class="w-full max-w-4xl mx-auto">
                        <div class="bg-gray-100 p-4 rounded-lg mb-4">
                            <div class="flex flex-wrap justify-center sm:justify-between items-center gap-4 mb-4">
                                <div class="flex space-x-2">
                                    <button id="prev" class="bg-purple-500 hover:bg-purple-700 text-white px-4 py-2 rounded-md transition duration-300">Précédent</button>
                                    <button id="next" class="bg-purple-500 hover:bg-purple-700 text-white px-4 py-2 rounded-md transition duration-300">Suivant</button>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span>Page: <span id="page_num"></span> / <span id="page_count"></span></span>
                                </div>
                            </div>
                            <canvas id="pdf_renderer" class="w-full h-auto border border-gray-300 rounded-lg"></canvas>
                        </div>
                    </div>

                    <script>
                        let pdfDoc = null;
                        let pageNum = 1;
                        let pageRendering = false;
                        let pageNumPending = null;
                        const scale = 1.5;
                        const canvas = document.getElementById('pdf_renderer');
                        const ctx = canvas.getContext('2d');

                        function renderPage(num) {
                            pageRendering = true;
                            pdfDoc.getPage(num).then(function(page) {
                                const viewport = page.getViewport({scale: scale});
                                canvas.height = viewport.height;
                                canvas.width = viewport.width;

                                const renderContext = {
                                    canvasContext: ctx,
                                    viewport: viewport
                                };
                                const renderTask = page.render(renderContext);

                                renderTask.promise.then(function() {
                                    pageRendering = false;
                                    if (pageNumPending !== null) {
                                        renderPage(pageNumPending);
                                        pageNumPending = null;
                                    }
                                });
                            });

                            document.getElementById('page_num').textContent = num;
                        }

                        function queueRenderPage(num) {
                            if (pageRendering) {
                                pageNumPending = num;
                            } else {
                                renderPage(num);
                            }
                        }

                        function onPrevPage() {
                            if (pageNum <= 1) {
                                return;
                            }
                            pageNum--;
                            queueRenderPage(pageNum);
                        }

                        function onNextPage() {
                            if (pageNum >= pdfDoc.numPages) {
                                return;
                            }
                            pageNum++;
                            queueRenderPage(pageNum);
                        }

                        document.getElementById('prev').addEventListener('click', onPrevPage);
                        document.getElementById('next').addEventListener('click', onNextPage);

                        // Charger le PDF avec gestion d'erreur
                        console.log('Loading PDF from:', '<?php echo $fullPdfUrl; ?>');
                        pdfjsLib.getDocument('<?php echo $fullPdfUrl; ?>').promise
                            .then(function(pdfDoc_) {
                                console.log('PDF loaded successfully');
                                pdfDoc = pdfDoc_;
                                document.getElementById('page_count').textContent = pdfDoc.numPages;
                                renderPage(pageNum);
                            })
                            .catch(function(error) {
                                console.error('Error loading PDF:', error);
                                const canvas = document.getElementById('pdf_renderer');
                                canvas.insertAdjacentHTML('beforebegin', 
                                    '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">' +
                                        '<strong class="font-bold">Erreur de chargement du PDF:</strong><br>' +
                                        '<span class="block sm:inline">' + error.message + '</span>' +
                                    '</div>'
                                );
                            });
                    </script>
                    <?php
                    }
                } else {
                    $videoUrl = $cours['contenu'];
                    if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
                        // Extract YouTube video ID
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $videoUrl, $matches);
                        if (isset($matches[1])) {
                            $videoId = $matches[1];
                            ?>
                            <div class="w-full max-w-5xl mx-auto">
                                <div class="relative w-full" style="padding-bottom: 56.25%;">
                                    <iframe 
                                        class="absolute top-0 left-0 w-full h-full rounded-lg shadow-lg"
                                        src="https://www.youtube.com/embed/<?php echo $videoId; ?>"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            </div>
                            <?php
                        }
                    }
                }
                ?>
            </div>
        
                <div class="mt-6 space-y-4">
                    <p class="text-gray-600 text-sm">
                        <i class="far fa-calendar-alt mr-2"></i>
                        Date d'inscription: <?php echo date('d/m/Y', strtotime($cours['date_inscription'])); ?>
                    </p>
                    
                    <form method="POST" class="mt-4">
                        <button type="submit" name="terminer_cours" class="w-full sm:w-auto bg-green-500 hover:bg-green-700 text-white px-6 py-2 rounded-md transition duration-300">
                            <i class="fas fa-check-circle mr-2"></i>Marquer comme terminé
                        </button>    
                    </form>
            </div>
        </div>
    </div>

</body>
</html>