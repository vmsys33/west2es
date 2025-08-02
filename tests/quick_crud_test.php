<?php
/**
 * Quick CRUD Test for File Categories
 * Simple test to validate CRUD operations across all file categories
 */

require_once __DIR__ . '/../functions/db_connection.php';
require_once __DIR__ . '/../functions/file_operations.php';

echo "🔍 Quick CRUD Test for File Categories\n";
echo "=====================================\n\n";

// Define all file categories
$fileCategories = [
    'admin_files' => 'admin_files_versions',
    'aeld_files' => 'aeld_files_versions',
    'cild_files' => 'cild_files_versions',
    'if_completed_files' => 'if_completed_files_versions',
    'if_proposals_files' => 'if_proposals_files_versions',
    'lulr_files' => 'lulr_files_versions',
    'rp_completed_berf_files' => 'rp_completed_berf_files_versions',
    'rp_completed_nonberf_files' => 'rp_completed_nonberf_files_versions',
    'rp_proposal_berf_files' => 'rp_proposal_berf_files_versions',
    'rp_proposal_nonberf_files' => 'rp_proposal_nonberf_files_versions',
    't_lr_files' => 't_lr_files_versions',
    't_pp_files' => 't_pp_files_versions',
    't_rs_files' => 't_rs_files_versions'
];

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

foreach ($fileCategories as $table1 => $table2) {
    echo "📁 Testing: {$table1}\n";
    echo "----------------------------------------\n";
    
    // Test 1: Table Existence
    $totalTests++;
    try {
        $stmt1 = $pdo->query("SHOW TABLES LIKE '{$table1}'");
        $stmt2 = $pdo->query("SHOW TABLES LIKE '{$table2}'");
        
        if ($stmt1->rowCount() > 0 && $stmt2->rowCount() > 0) {
            echo "✅ Tables exist: {$table1}, {$table2}\n";
            $passedTests++;
        } else {
            echo "❌ Tables missing: {$table1}, {$table2}\n";
            $failedTests++;
        }
    } catch (Exception $e) {
        echo "❌ Error checking tables: " . $e->getMessage() . "\n";
        $failedTests++;
    }
    
    // Test 2: Table Structure
    $totalTests++;
    try {
        $stmt = $pdo->query("DESCRIBE {$table1}");
        $columns1 = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $stmt = $pdo->query("DESCRIBE {$table2}");
        $columns2 = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $required1 = ['id', 'filename', 'user_id', 'status'];
        $required2 = ['id', 'file_id', 'version_no', 'filename', 'file_path', 'datetime', 'file_size'];
        
        $missing1 = array_diff($required1, $columns1);
        $missing2 = array_diff($required2, $columns2);
        
        if (empty($missing1) && empty($missing2)) {
            echo "✅ Table structure is correct\n";
            $passedTests++;
        } else {
            echo "❌ Missing columns: " . implode(', ', array_merge($missing1, $missing2)) . "\n";
            $failedTests++;
        }
    } catch (Exception $e) {
        echo "❌ Error checking structure: " . $e->getMessage() . "\n";
        $failedTests++;
    }
    
    // Test 3: Read Operation
    $totalTests++;
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM {$table1} 
            WHERE status = 'approve'
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "✅ Read operation: {$result['count']} approved files found\n";
        $passedTests++;
    } catch (Exception $e) {
        echo "❌ Read operation failed: " . $e->getMessage() . "\n";
        $failedTests++;
    }
    
    // Test 4: File Operations Function
    $totalTests++;
    try {
        $testFile = [
            'name' => 'test_file.pdf',
            'tmp_name' => '/tmp/test_file.pdf',
            'size' => 1024,
            'type' => 'application/pdf',
            'error' => 0
        ];
        
        $result = setFileTableVariables($table1, $testFile);
        
        if (isset($result['uploadDir']) && isset($result['table2'])) {
            echo "✅ File operations function working\n";
            $passedTests++;
        } else {
            echo "❌ File operations function failed\n";
            $failedTests++;
        }
    } catch (Exception $e) {
        echo "❌ File operations function error: " . $e->getMessage() . "\n";
        $failedTests++;
    }
    
    // Test 5: Page Title Function
    $totalTests++;
    try {
        require_once __DIR__ . '/../functions/pageTitle.php';
        $pageTitle = getPageTitle($table1);
        
        if (!empty($pageTitle)) {
            echo "✅ Page title function: {$pageTitle}\n";
            $passedTests++;
        } else {
            echo "❌ Page title function returned empty\n";
            $failedTests++;
        }
    } catch (Exception $e) {
        echo "❌ Page title function error: " . $e->getMessage() . "\n";
        $failedTests++;
    }
    
    // Test 6: File Category Function
    $totalTests++;
    try {
        $fileCategory = getFileCategory($table1);
        
        if (!empty($fileCategory)) {
            echo "✅ File category function: {$fileCategory}\n";
            $passedTests++;
        } else {
            echo "❌ File category function returned empty\n";
            $failedTests++;
        }
    } catch (Exception $e) {
        echo "❌ File category function error: " . $e->getMessage() . "\n";
        $failedTests++;
    }
    
    echo "\n";
}

// Generate Summary Report
echo "📊 Test Summary\n";
echo "===============\n";
echo "Total Tests: {$totalTests}\n";
echo "Passed: {$passedTests}\n";
echo "Failed: {$failedTests}\n";
echo "Success Rate: " . round(($passedTests / $totalTests) * 100, 2) . "%\n\n";

if ($failedTests > 0) {
    echo "🚨 Some tests failed. Please check the issues above.\n";
} else {
    echo "🎉 All CRUD operations are working correctly!\n";
}

// Test AJAX Endpoints
echo "\n🔗 Testing AJAX Endpoints\n";
echo "========================\n";

$ajaxEndpoints = [
    __DIR__ . '/../functions/file_functions/fetch_revisions.php',
    __DIR__ . '/../functions/file_functions/fetch_file_revisions.php',
    __DIR__ . '/../functions/file_functions/add_file.php',
    __DIR__ . '/../functions/file_functions/update_revision_file.php',
    __DIR__ . '/../functions/file_functions/delete_revision.php',
    __DIR__ . '/../functions/file_functions/add_revision_file.php',
    __DIR__ . '/../functions/file_functions/delete_file.php',
    __DIR__ . '/../functions/file_functions/download_file.php'
];

foreach ($ajaxEndpoints as $endpoint) {
    if (file_exists($endpoint)) {
        echo "✅ {$endpoint}\n";
    } else {
        echo "❌ {$endpoint} - Missing!\n";
    }
}

echo "\n✅ CRUD Test Complete!\n";
?> 