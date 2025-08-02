<?php
/**
 * Database Check Script
 * Check what files are available for testing
 */

echo "🔍 Database Check\n";
echo "================\n\n";

// Include database connection
require_once __DIR__ . '/../functions/db_connection.php';

try {
    // Check available tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📋 Available Tables:\n";
    foreach ($tables as $table) {
        echo "   - {$table}\n";
    }
    
    echo "\n📄 Checking for test files...\n";
    
    $testTables = [
        'admin_files', 'aeld_files', 'cild_files', 'if_completed_files',
        'if_proposals_files', 'lulr_files', 'rp_completed_berf_files',
        'rp_completed_nonberf_files', 'rp_proposal_berf_files',
        'rp_proposal_nonberf_files', 't_lr_files', 't_pp_files', 't_rs_files'
    ];
    
    $foundFiles = false;
    
    foreach ($testTables as $table) {
        if (in_array($table, $tables)) {
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM {$table} WHERE status = 'approve'");
                $stmt->execute();
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                
                if ($count > 0) {
                    echo "   ✅ {$table}: {$count} approved files\n";
                    $foundFiles = true;
                    
                    // Show sample files
                    $stmt = $pdo->prepare("SELECT id, filename, file_path FROM {$table} WHERE status = 'approve' LIMIT 3");
                    $stmt->execute();
                    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($files as $file) {
                        echo "      - ID: {$file['id']}, Filename: {$file['filename']}\n";
                    }
                } else {
                    echo "   ⚠️  {$table}: No approved files\n";
                }
            } catch (Exception $e) {
                echo "   ❌ {$table}: Error - " . $e->getMessage() . "\n";
            }
        } else {
            echo "   ❌ {$table}: Table does not exist\n";
        }
    }
    
    if (!$foundFiles) {
        echo "\n⚠️  No approved files found for testing.\n";
        echo "To add test files, you can:\n";
        echo "1. Upload some files through the web interface\n";
        echo "2. Set their status to 'approve' in the database\n";
        echo "3. Or create dummy files for testing\n";
    } else {
        echo "\n✅ Found test files! You can now run the tests.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database connection error: " . $e->getMessage() . "\n";
}
?> 