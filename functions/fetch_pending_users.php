<?php
require_once 'db_connection.php';
header('Content-Type: application/json'); // Ensure response is JSON

try {
    $stmt = $pdo->prepare("SELECT id_no, deped_id_no, last_name, first_name, middle_name, email FROM user_data WHERE status = 'pending'");
    $stmt->execute();
    $pendingUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $pendingUsers]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch pending users: ' . $e->getMessage()]);
}
?>
