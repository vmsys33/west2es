<?php
require_once 'functions/db_connection.php';

echo "<h2>General Settings Initialization</h2>";

try {
    // Check if general_setting table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'general_setting'");
    if ($stmt->rowCount() == 0) {
        echo "<p>❌ general_setting table does not exist. Creating it...</p>";
        
        // Create the table
        $createTable = "CREATE TABLE general_setting (
            id INT AUTO_INCREMENT PRIMARY KEY,
            website_name VARCHAR(255) DEFAULT 'Cadiz West 2 Elementary School',
            email_address VARCHAR(255) DEFAULT 'admin@west2es.edu.ph',
            school_logo VARCHAR(255) DEFAULT '',
            admin_name VARCHAR(255) DEFAULT 'Administrator'
        )";
        
        $pdo->exec($createTable);
        echo "<p>✅ general_setting table created successfully.</p>";
    } else {
        echo "<p>✅ general_setting table exists.</p>";
    }
    
    // Check if there's any data in the table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM general_setting");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        echo "<p>❌ No data found in general_setting table. Inserting default values...</p>";
        
        // Insert default values
        $insertDefault = "INSERT INTO general_setting (website_name, email_address, school_logo, admin_name) 
                         VALUES ('Cadiz West 2 Elementary School', 'admin@west2es.edu.ph', '', 'Administrator')";
        
        $pdo->exec($insertDefault);
        echo "<p>✅ Default values inserted successfully.</p>";
    } else {
        echo "<p>✅ Found {$count} record(s) in general_setting table.</p>";
    }
    
    // Display current settings
    $stmt = $pdo->query("SELECT * FROM general_setting WHERE id = 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($settings) {
        echo "<h3>Current Settings:</h3>";
        echo "<pre>";
        print_r($settings);
        echo "</pre>";
    }
    
} catch (PDOException $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
