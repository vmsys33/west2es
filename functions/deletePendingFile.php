<?php
require_once 'db_connection.php'; // Ensure this connects to your database
require_once 'notification_helper.php'; // Include if you need to log notifications
header('Content-Type: application/json');

session_start();

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Validate the ID
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'File ID is required.']);
    exit;
}

$id = (int) $_POST['id'];

try {
    // Fetch the file details before deletion for logging
    $stmt = $pdo->prepare("SELECT * FROM pending_files WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$file) {
        echo json_encode(['status' => 'error', 'message' => 'File not found.']);
        exit;
    }

    $fileName = $file['name'] ?? 'Unknown File';

    // Delete the file from the table
    $stmt = $pdo->prepare("DELETE FROM pending_files WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Log the notification (if necessary)
    $userId = $_SESSION['user_id'] ?? null; // Ensure the user_id is stored in the session
    $role = $_SESSION['user_role'] ?? 'unknown'; // Ensure user_role is stored
    $userFullName = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] ?? 'Unknown User';

    try {
        logNotification($userId, $role, 'delete', "($userFullName) deleted pending file '$fileName' (ID: $id)");
    } catch (Exception $e) {
        error_log("Notification logging failed: " . $e->getMessage());
    }

    echo json_encode(['status' => 'success', 'message' => 'File deleted successfully!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete file: ' . $e->getMessage()]);
}
?>
