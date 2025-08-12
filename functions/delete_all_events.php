<?php
/**
 * Delete All Events
 * ADMINISTRATIVE FUNCTION - USE WITH EXTREME CAUTION
 * This script deletes all events from the events table
 */

require_once 'db_connection.php';
require_once 'notification_helper.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Admin privileges required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmation = $_POST['confirmation'] ?? '';
    
    // Require explicit confirmation
    if ($confirmation !== 'DELETE_ALL_EVENTS_CONFIRM') {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Confirmation code required. Please type "DELETE_ALL_EVENTS_CONFIRM" to proceed.'
        ]);
        exit;
    }
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Get count of events before deletion
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM events");
        $eventCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Delete all events
        $stmt = $pdo->prepare("DELETE FROM events");
        $stmt->execute();
        $deletedCount = $stmt->rowCount();
        
        // Log the action
        $adminId = $_SESSION['user_id'] ?? 0;
        $adminName = ($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '');
        
        try {
            logNotification($adminId, 'admin', 'delete_all_events', 
                "($adminName) deleted ALL events from the database. Total events deleted: $deletedCount");
        } catch (Exception $e) {
            error_log("Failed to log delete all events action: " . $e->getMessage());
        }
        
        // Commit transaction
        $pdo->commit();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'All events have been deleted successfully',
            'deleted_count' => $deletedCount,
            'total_events_before' => $eventCount
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        error_log("Delete all events error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred while deleting events: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
