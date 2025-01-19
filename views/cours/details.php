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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        // Définir le worker PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>
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
                    $pdfPath = $cours['contenu'];
                    
                    // Si le chemin est absolu, le convertir en relatif
                    if (strpos($pdfPath, 'C:') === 0) {
                        // Extraire juste le nom du fichier
                        $fileName = basename($pdfPath);
                        $pdfUrl = '/uploads/pdfs/' . $fileName;
                    } else {
                        $pdfUrl = $pdfPath;
                    }

                    // Construire le chemin physique complet pour la vérification
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
                        // Construire l'URL complète pour le PDF
                        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                        $fullPdfUrl = $baseUrl . '/Youdemy/public' . $pdfUrl;
                    ?>
                    <div class="w-full h-screen bg-gray-100 rounded-lg shadow relative">
                        <canvas id="pdfViewer" class="w-full h-full"></canvas>
                        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-4 bg-white p-2 rounded shadow">
                            <button id="prev" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Précédent</button>
                            <span id="pageInfo" class="px-4 py-2">Page: <span id="pageNum">1</span> / <span id="pageCount">1</span></span>
                            <button id="next" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Suivant</button>
                        </div>
                    </div>

                    <script>
                        let pdfDoc = null;
                        let pageNum = 1;
                        let pageRendering = false;
                        let pageNumPending = null;
                        const scale = 1.5;
                        const canvas = document.getElementById('pdfViewer');
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

                            document.getElementById('pageNum').textContent = num;
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
                                document.getElementById('pageCount').textContent = pdfDoc.numPages;
                                renderPage(pageNum);
                            })
                            .catch(function(error) {
                                console.error('Error loading PDF:', error);
                                const canvas = document.getElementById('pdfViewer');
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

            <div class="mt-4 flex space-x-4">
                <a href="mes_cours.php" class="inline-block px-6 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">Retour</a>
                <a href="mes_cours.php" class="inline-block px-6 py-2 bg-blue-500 text-white rounded hover:bg-gray-600 transition-colors">Terminer Cours</a>

            </div>
        </div>
    </div>

</body>
</html>