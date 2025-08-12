<?php
session_start();
require_once 'functions/db_connection.php';

echo "<h2>Notifications Debug Information</h2>";

try {
    // Check if notifications table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'notifications'");
    if ($stmt->rowCount() == 0) {
        echo "<p>❌ notifications table does not exist.</p>";
        exit;
    }
    
    echo "<p>✅ notifications table exists.</p>";
    
    // Get total count of notifications
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM notifications");
    $stmt->execute();
    $totalCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "<p>Total notifications in database: <strong>$totalCount</strong></p>";
    
    // Get count by seen status
    $stmt = $pdo->prepare("SELECT seen, COUNT(*) AS count FROM notifications GROUP BY seen");
    $stmt->execute();
    $seenCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Notifications by Status:</h3>";
    foreach ($seenCounts as $row) {
        $status = $row['seen'] == 1 ? 'Seen' : 'Unseen';
        echo "<p>$status: <strong>{$row['count']}</strong></p>";
    }
    
    // Show recent notifications
    echo "<h3>Recent Notifications (Last 10):</h3>";
    $stmt = $pdo->prepare("SELECT id, description, created_at, seen FROM notifications ORDER BY created_at DESC LIMIT 10");
    $stmt->execute();
    $recentNotifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($recentNotifications) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Description</th><th>Created At</th><th>Seen</th></tr>";
        foreach ($recentNotifications as $notification) {
            $seenStatus = $notification['seen'] == 1 ? 'Yes' : 'No';
            echo "<tr>";
            echo "<td>{$notification['id']}</td>";
            echo "<td>{$notification['description']}</td>";
            echo "<td>{$notification['created_at']}</td>";
            echo "<td>$seenStatus</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No notifications found in the database.</p>";
    }
    
    // Check session data
    echo "<h3>Session Information:</h3>";
    echo "<p>User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "</p>";
    echo "<p>Logged in: " . ($_SESSION['logged_in'] ?? 'NOT SET') . "</p>";
    echo "<p>User role: " . ($_SESSION['user_role'] ?? 'NOT SET') . "</p>";
    
} catch (PDOException $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}
?>
