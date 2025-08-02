<?php
require_once 'db_connection.php';

function logNotification($userId, $role, $activityType, $description) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, role, activity_type, description) 
                               VALUES (:user_id, :role, :activity_type, :description)");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':activity_type', $activityType);
        $stmt->bindParam(':description', $description);
        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Failed to log notification: " . $e->getMessage());
    }
}
?>