<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Cours.php';

// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers pour CORS et JSON
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

try {
    // Récupérer et logger les paramètres
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    error_log("=== Début de la requête pagination.php ===");
    error_log("Paramètres reçus - Page: $page, Search: $search");
    
    $coursObj = new Cours();
    $resultats = $coursObj->getCoursWithPagination($page, 6, $search);
    
    // Vérifier la structure des données
    if (!is_array($resultats)) {
        throw new Exception('Les résultats ne sont pas un tableau');
    }
    
    if (!isset($resultats['cours']) || !is_array($resultats['cours'])) {
        throw new Exception('Le format des cours est invalide');
    }
    
    // Assurez-vous que toutes les clés requises sont présentes
    $required_keys = ['cours', 'total', 'pages', 'current_page'];
    foreach ($required_keys as $key) {
        if (!isset($resultats[$key])) {
            throw new Exception("La clé '$key' est manquante dans les résultats");
        }
    }
    
    error_log("Structure des résultats: " . print_r(array_keys($resultats), true));
    error_log("Nombre de cours: " . count($resultats['cours']));
    error_log("Total: " . $resultats['total']);
    error_log("Pages: " . $resultats['pages']);
    error_log("Page courante: " . $resultats['current_page']);
    
    // Encoder en JSON avec gestion des caractères spéciaux
    $json = json_encode($resultats, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
    if ($json === false) {
        throw new Exception("Erreur d'encodage JSON: " . json_last_error_msg());
    }
    
    echo $json;
    
} catch (Exception $e) {
    error_log("!!! ERREUR dans pagination.php: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Erreur lors du chargement des cours: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
