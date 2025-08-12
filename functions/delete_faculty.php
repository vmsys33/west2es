<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}
if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'No ID provided']);
    exit;
}
require_once 'db_connection.php';
$id = intval($_GET['id']);
$stmt = $pdo->prepare("DELETE FROM user_data WHERE id_no = ?");
if ($stmt->execute([$id])) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete user']);
}