<?php
require_once 'functions/db_connection.php';

echo "<h2>Adding Photo Column to user_data Table</h2>";

try {
    // Check if photo column already exists
    $stmt = $pdo->query("SHOW COLUMNS FROM user_data LIKE 'photo'");
    if ($stmt->rowCount() == 0) {
        echo "<p>❌ Photo column does not exist. Adding it...</p>";
        
        // Add the photo column
        $alterTable = "ALTER TABLE user_data ADD COLUMN photo VARCHAR(255) DEFAULT NULL AFTER email";
        
        $pdo->exec($alterTable);
        echo "<p>✅ Photo column added successfully.</p>";
    } else {
        echo "<p>✅ Photo column already exists.</p>";
    }
    
    // Display the updated table structure
    echo "<h3>Updated user_data Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE user_data");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
