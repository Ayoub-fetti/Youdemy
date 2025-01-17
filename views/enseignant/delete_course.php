<?php
require_once 'C:/laragon/www/Youdemy/config/database.php';

$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $delete_query = "DELETE FROM cours WHERE id = :id";
    $delete_stmt = $db->prepare($delete_query);
    $delete_stmt->execute([':id' => $id]);
    header('Location: enseignant_Dash.php');
    exit;
} else {
    die('Invalid request.');
}
?>
