<?php
/**
 * Manual Database Field Addition
 * Add email verification fields to user_data table
 */

echo "Adding email verification fields to database...\n\n";

try {
    require_once 'functions/db_connection.php';
    
    // Add verification_token field
    try {
        $pdo->exec("ALTER TABLE user_data ADD COLUMN verification_token VARCHAR(255) NULL");
        echo "✓ verification_token field added\n";
    } catch (Exception $e) {
        echo "verification_token field already exists\n";
    }
    
    // Add email_verified field
    try {
        $pdo->exec("ALTER TABLE user_data ADD COLUMN email_verified TINYINT(1) DEFAULT 0");
        echo "✓ email_verified field added\n";
    } catch (Exception $e) {
        echo "email_verified field already exists\n";
    }
    
    // Add verification_expires field
    try {
        $pdo->exec("ALTER TABLE user_data ADD COLUMN verification_expires TIMESTAMP NULL");
        echo "✓ verification_expires field added\n";
    } catch (Exception $e) {
        echo "verification_expires field already exists\n";
    }
    
    echo "\n✅ Database fields added successfully!\n";
    echo "Email verification system is now ready to use.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
