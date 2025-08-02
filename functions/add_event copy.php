<?php
require_once 'db_connection.php';
require_once 'notification_helper.php'; // Include the notification helper
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $datetime = filter_input(INPUT_POST, 'datetime', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validate required fields
    if (empty($name) || empty($description) || empty($location) || empty($datetime)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    try {
        // Insert the event into the database
        $stmt = $pdo->prepare("INSERT INTO events (name, description, location, datetime) VALUES (:name, :description, :location, :datetime)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':datetime', $datetime);
        $stmt->execute();

        // Log the notification
        $userId=$_SESSION['user_id'];
        $role=$_SESSION['user_role'];
        logNotification($userId, $role, 'create', "Created an event: $name");

        // Return success response
        echo json_encode(['status' => 'success', 'message' => 'Event added successfully!']);
    } catch (PDOException $e) {
        // Handle database errors
        echo json_encode(['status' => 'error', 'message' => 'Failed to add event: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
