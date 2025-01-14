<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../models/Admin.php';
header('Content-Type: application/json');

if (!isset($_GET['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User ID not provided.']);
    exit();
}

$userId = $_GET['user_id'];

$db = new Database();
$pdo = $db->connect();
$admin = new Admin($pdo);

$newStatus = $admin->toggleStatus($userId);

if ($newStatus) {
    echo json_encode(['success' => true, 'newStatus' => $newStatus]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to toggle status.']);
}

?>