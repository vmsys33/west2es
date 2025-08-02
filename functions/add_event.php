<?php
require_once 'db_connection.php';
require_once 'notification_helper.php'; // Include the notification helper
header('Content-Type: application/json');

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ensure session is started
session_start();

if (!isset($_SESSION['user_id'], $_SESSION['user_role'])) {
    echo json_encode(['status' => 'error', 'message' => 'User session data is missing.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs

    $name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS); 
    $description = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS); 
    $location = filter_var($_POST['location'], FILTER_SANITIZE_FULL_SPECIAL_CHARS); 
    $datetime = filter_var($_POST['datetime'], FILTER_SANITIZE_FULL_SPECIAL_CHARS); 



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
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];


        // Get the user's username, first name, and last name from the session
            $userFullName = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
            

            // Log the notification for creating an event with username, first name, and last name
            try {
                logNotification($userId, $role, 'create', "User ($userFullName) created an event: $name");
            } catch (Exception $e) {
                error_log("Notification logging failed: " . $e->getMessage());
            }


        
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
