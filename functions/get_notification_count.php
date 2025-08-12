<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

try {
    // Get unseen notifications count
    $stmt = $pdo->prepare("SELECT COUNT(*) AS unseen_count FROM notifications WHERE seen = 0");
    $stmt->execute();
    $notificationCount = $stmt->fetch(PDO::FETCH_ASSOC)['unseen_count'];
    
    // Get pending users count (for admin only)
    $pendingUsersCount = 0;
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        $stmt = $pdo->prepare("SELECT COUNT(*) AS pending_count FROM user_data WHERE status = 'pending'");
        $stmt->execute();
        $pendingUsersCount = $stmt->fetch(PDO::FETCH_ASSOC)['pending_count'];
    }
    
    // Get pending files count (for admin only)
    $pendingFilesCount = 0;
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        $stmt = $pdo->prepare("SELECT COUNT(*) AS pending_count FROM pending_files WHERE status = 'pending'");
        $stmt->execute();
        $pendingFilesCount = $stmt->fetch(PDO::FETCH_ASSOC)['pending_count'];
    }
    
    echo json_encode([
        'status' => 'success',
        'notification_count' => $notificationCount,
        'pending_users_count' => $pendingUsersCount,
        'pending_files_count' => $pendingFilesCount
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to get notification count: ' . $e->getMessage()]);
}
?>
