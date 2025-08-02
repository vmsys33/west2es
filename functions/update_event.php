<?php
require_once 'db_connection.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['name'], $data['description'], $data['location'], $data['datetime'])) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

$id = $data['id'];
$name = $data['name'];
$description = $data['description'];
$location = $data['location'];
$datetime = $data['datetime'];

try {
    $stmt = $pdo->prepare("UPDATE events SET name = :name, description = :description, location = :location, datetime = :datetime WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':datetime', $datetime);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Event updated successfully!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update event: ' . $e->getMessage()]);
}
?>
