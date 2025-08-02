<?php
require_once 'db_connection.php';
header('Content-Type: application/json');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    // $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    // $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    // $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    // $datetime = filter_input(INPUT_POST, 'datetime', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $datetime = filter_input(INPUT_POST, 'datetime', FILTER_SANITIZE_FULL_SPECIAL_CHARS);


    // Validate required fields
    if (empty($id) || empty($name) || empty($description) || empty($location) || empty($datetime)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    try {
        // Update the event in the database
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
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
