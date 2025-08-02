<?php
require_once 'db_connection.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("SELECT * FROM events ORDER BY datetime ASC");
    $stmt->execute();

    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $events]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch events: ' . $e->getMessage()]);
}
?>
