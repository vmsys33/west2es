<?php
require_once 'db_connection.php';
header('Content-Type: application/json');

// Validate the request
if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Event ID is required.']);
    exit;
}

$id = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($event) {
        echo json_encode(['status' => 'success', 'data' => $event]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Event not found.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch event details: ' . $e->getMessage()]);
}
?>
