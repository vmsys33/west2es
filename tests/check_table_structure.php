<?php
/**
 * Check Table Structure
 * See what columns actually exist in the tables
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Include database connection
require_once __DIR__ . '/../functions/db_connection.php';

echo "🔍 Table Structure Check\n";
echo "=======================\n\n";

$testTables = [
    'admin_files', 'aeld_files', 'cild_files', 'if_completed_files',
    'if_proposals_files', 'lulr_files', 'rp_completed_berf_files',
    'rp_completed_nonberf_files', 'rp_proposal_berf_files',
    'rp_proposal_nonberf_files', 't_lr_files', 't_pp_files', 't_rs_files'
];

foreach ($testTables as $table) {
    echo "📋 Table: {$table}\n";
    
    try {
        // Check if table exists
        $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
        if ($stmt->rowCount() == 0) {
            echo "   ❌ Table does not exist\n\n";
            continue;
        }
        
        // Get table structure
        $stmt = $pdo->query("DESCRIBE {$table}");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "   📊 Columns:\n";
        foreach ($columns as $column) {
            echo "      - {$column['Field']} ({$column['Type']}) - {$column['Null']} - {$column['Key']}\n";
        }
        
        // Check for required columns
        $requiredColumns = ['id', 'filename', 'status'];
        $optionalColumns = ['file_path', 'filepath', 'path', 'file_name'];
        
        $foundRequired = [];
        $foundOptional = [];
        
        foreach ($columns as $column) {
            $fieldName = $column['Field'];
            if (in_array($fieldName, $requiredColumns)) {
                $foundRequired[] = $fieldName;
            }
            if (in_array($fieldName, $optionalColumns)) {
                $foundOptional[] = $fieldName;
            }
        }
        
        echo "   ✅ Required columns found: " . implode(', ', $foundRequired) . "\n";
        if (!empty($foundOptional)) {
            echo "   📁 Path columns found: " . implode(', ', $foundOptional) . "\n";
        }
        
        // Check if table has data
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM {$table}");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "   📄 Total records: {$count}\n";
        
        if ($count > 0) {
            // Show sample data
            $stmt = $pdo->prepare("SELECT * FROM {$table} LIMIT 1");
            $stmt->execute();
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "   📄 Sample record:\n";
            foreach ($sample as $key => $value) {
                echo "      - {$key}: {$value}\n";
            }
        }
        
    } catch (Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "🎯 Summary:\n";
echo "- Check which path column name is used (file_path, filepath, path, etc.)\n";
echo "- Update test files to use the correct column names\n";
echo "- Focus on tables that have data (aeld_files has 3 approved files)\n";
?> 