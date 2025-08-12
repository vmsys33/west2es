<?php
session_start();
require_once 'functions/db_connection.php';

echo "<h2>Create Sample Notifications</h2>";

try {
    // Check if notifications table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'notifications'");
    if ($stmt->rowCount() == 0) {
        echo "<p>❌ notifications table does not exist. Creating it...</p>";
        
        // Create the notifications table
        $createTable = "CREATE TABLE notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            description TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            seen TINYINT(1) DEFAULT 0
        )";
        
        $pdo->exec($createTable);
        echo "<p>✅ notifications table created successfully.</p>";
    } else {
        echo "<p>✅ notifications table already exists.</p>";
    }
    
    // Check current notification count
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM notifications");
    $stmt->execute();
    $currentCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo "<p>Current notifications in database: <strong>$currentCount</strong></p>";
    
    if ($currentCount == 0) {
        echo "<p>Adding sample notifications...</p>";
        
        // Sample notifications
        $sampleNotifications = [
            "New user registration: John Doe has registered for an account.",
            "File uploaded: Annual Report 2024 has been uploaded to Administrative Files.",
            "Event created: School Meeting scheduled for next week.",
            "Profile updated: Teacher profile has been updated.",
            "System maintenance: Scheduled maintenance completed successfully.",
            "New proposal submitted: Research proposal for Science Fair.",
            "File approved: Budget proposal has been approved by admin.",
            "User status changed: Faculty member status updated to active.",
            "Backup completed: System backup completed successfully.",
            "Login alert: Multiple login attempts detected from new location."
        ];
        
        $stmt = $pdo->prepare("INSERT INTO notifications (description, seen) VALUES (?, ?)");
        
        foreach ($sampleNotifications as $index => $description) {
            // Make some notifications seen, some unseen
            $seen = ($index % 3 == 0) ? 1 : 0; // Every 3rd notification will be seen
            
            $stmt->execute([$description, $seen]);
        }
        
        echo "<p>✅ Added " . count($sampleNotifications) . " sample notifications.</p>";
        
        // Show the new count
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM notifications");
        $stmt->execute();
        $newCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        echo "<p>Total notifications now: <strong>$newCount</strong></p>";
        
    } else {
        echo "<p>Notifications already exist. No sample data added.</p>";
    }
    
    // Show recent notifications
    echo "<h3>Recent Notifications:</h3>";
    $stmt = $pdo->prepare("SELECT id, description, created_at, seen FROM notifications ORDER BY created_at DESC LIMIT 5");
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
    }
    
    echo "<p><a href='pages/notification.php'>Go to Notifications Page</a></p>";
    
} catch (PDOException $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}
?>
