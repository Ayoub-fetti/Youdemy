<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Cours.php';

header('Content-Type: application/json');

try {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    $coursObj = new Cours();
    $resultats = $coursObj->getCoursWithPagination($page, 6, $search);
    
    echo json_encode($resultats);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors du chargement des cours']);
}
