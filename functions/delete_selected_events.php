<?php
/**
 * Delete Selected Events
 * ADMINISTRATIVE FUNCTION - USE WITH EXTREME CAUTION
 * This script deletes selected events from the events table
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
    $eventIdsJson = $_POST['event_ids'] ?? '';
    
    if (empty($eventIdsJson)) {
        echo json_encode(['status' => 'error', 'message' => 'No event IDs provided']);
        exit;
    }
    
    $eventIds = json_decode($eventIdsJson, true);
    
    if (!is_array($eventIds) || empty($eventIds)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid event IDs format']);
        exit;
    }
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Validate that all event IDs exist
        $placeholders = str_repeat('?,', count($eventIds) - 1) . '?';
        $stmt = $pdo->prepare("SELECT id, name FROM events WHERE id IN ($placeholders)");
        $stmt->execute($eventIds);
        $existingEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($existingEvents) !== count($eventIds)) {
            throw new Exception('Some event IDs do not exist');
        }
        
        // Delete selected events
        $stmt = $pdo->prepare("DELETE FROM events WHERE id IN ($placeholders)");
        $stmt->execute($eventIds);
        $deletedCount = $stmt->rowCount();
        
        // Log the action
        $adminId = $_SESSION['user_id'] ?? 0;
        $adminName = ($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '');
        $eventNames = array_column($existingEvents, 'name');
        $eventNamesStr = implode(', ', $eventNames);
        
        try {
            logNotification($adminId, 'admin', 'delete_selected_events', 
                "($adminName) deleted $deletedCount selected events: $eventNamesStr");
        } catch (Exception $e) {
            error_log("Failed to log delete selected events action: " . $e->getMessage());
        }
        
        // Commit transaction
        $pdo->commit();
        
        echo json_encode([
            'status' => 'success',
            'message' => "$deletedCount event(s) have been deleted successfully",
            'deleted_count' => $deletedCount,
            'deleted_events' => $eventNames
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        error_log("Delete selected events error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred while deleting events: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
