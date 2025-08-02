<?php
/**
 * Debug Files Script
 * Check what files are actually in the database
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Include database connection
require_once __DIR__ . '/../functions/db_connection.php';

echo "🔍 Debug Files Check\n";
echo "===================\n\n";

$testTables = [
    'admin_files', 'aeld_files', 'cild_files', 'if_completed_files',
    'if_proposals_files', 'lulr_files', 'rp_completed_berf_files',
    'rp_completed_nonberf_files', 'rp_proposal_berf_files',
    'rp_proposal_nonberf_files', 't_lr_files', 't_pp_files', 't_rs_files'
];

foreach ($testTables as $table) {
    echo "📋 Checking table: {$table}\n";
    
    try {
        // Check if table exists
        $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
        if ($stmt->rowCount() == 0) {
            echo "   ❌ Table does not exist\n";
            continue;
        }
        
        // Get total count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM {$table}");
        $totalCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "   📊 Total files: {$totalCount}\n";
        
        if ($totalCount > 0) {
            // Get status distribution
            $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM {$table} GROUP BY status");
            $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "   📈 Status distribution:\n";
            foreach ($statuses as $status) {
                echo "      - {$status['status']}: {$status['count']}\n";
            }
            
            // Show sample files
            $stmt = $pdo->prepare("SELECT id, filename, file_path, status FROM {$table} LIMIT 5");
            $stmt->execute();
            $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "   📄 Sample files:\n";
            foreach ($files as $file) {
                echo "      - ID: {$file['id']}, Filename: {$file['filename']}, Status: {$file['status']}\n";
            }
            
            // Check for approved files specifically
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM {$table} WHERE status = 'approve'");
            $stmt->execute();
            $approvedCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            if ($approvedCount > 0) {
                echo "   ✅ Found {$approvedCount} approved files\n";
                
                // Show approved files
                $stmt = $pdo->prepare("SELECT id, filename, file_path FROM {$table} WHERE status = 'approve' LIMIT 3");
                $stmt->execute();
                $approvedFiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "   📄 Approved files:\n";
                foreach ($approvedFiles as $file) {
                    echo "      - ID: {$file['id']}, Filename: {$file['filename']}\n";
                }
            } else {
                echo "   ⚠️  No approved files found\n";
                
                // Check what statuses exist
                $stmt = $pdo->query("SELECT DISTINCT status FROM {$table} WHERE status IS NOT NULL");
                $existingStatuses = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                if (!empty($existingStatuses)) {
                    echo "   💡 Available statuses: " . implode(', ', $existingStatuses) . "\n";
                    echo "   💡 Try changing status to 'approve' for testing\n";
                }
            }
        } else {
            echo "   ⚠️  Table is empty\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "🎯 Recommendations:\n";
echo "1. If tables are empty, upload some files through the web interface\n";
echo "2. If files exist but none are 'approve' status, update their status in the database\n";
echo "3. You can manually update status with: UPDATE table_name SET status = 'approve' WHERE id = file_id\n";
?> 