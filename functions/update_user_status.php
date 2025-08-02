<?php
require_once 'db_connection.php';
require_once 'notification_helper.php';
header('Content-Type: application/json'); // Ensure JSON response

// Ensure session is started
session_start();

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['id_no']) || !isset($data['status'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    exit;
}

$id_no = $data['id_no'];
$status = $data['status'];

try {
    // Update user status
    $stmt = $pdo->prepare("UPDATE user_data SET status = :status WHERE id_no = :id_no");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id_no', $id_no);
    $stmt->execute();

    // Fetch the user's details (first and last name)
    $nameStmt = $pdo->prepare("SELECT first_name, last_name FROM user_data WHERE id_no = :id_no");
    $nameStmt->bindParam(':id_no', $id_no);
    $nameStmt->execute();
    $user = $nameStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        exit;
    }

    $fullName = $user['first_name'] . ' ' . $user['last_name'];

    // Log the notification
    $userId = $_SESSION['user_id'];
    $role = $_SESSION['user_role'];

    try {
        logNotification($userId, $role, 'activate', "Activated user: $fullName (ID: $id_no)");
    } catch (Exception $e) {
        error_log("Notification logging failed: " . $e->getMessage());
    }

    echo json_encode(['status' => 'success', 'message' => 'User status updated successfully.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update user status: ' . $e->getMessage()]);
}
?>
