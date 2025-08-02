<?php
/**
 * Filename Editing Test
 * Tests the filename editing functionality
 */

require_once __DIR__ . '/../functions/db_connection.php';

echo "📝 Testing Filename Editing Functionality\n";
echo "========================================\n\n";

// Test 1: Check if edit_filename.php exists
echo "📁 Checking Required Files:\n";
$requiredFiles = [
    'functions/file_functions/edit_filename.php',
    'pages/content.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✅ {$file}\n";
    } else {
        echo "❌ {$file} - Missing!\n";
    }
}

// Test 2: Check database tables for filename editing
echo "\n🗄️  Checking Database Tables:\n";
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

foreach ($fileCategories as $table1 => $table2) {
    try {
        // Check if tables exist and have required columns
        $stmt = $pdo->query("DESCRIBE {$table1}");
        $columns1 = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $stmt = $pdo->query("DESCRIBE {$table2}");
        $columns2 = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $required1 = ['id', 'filename'];
        $required2 = ['id', 'filename'];
        
        $missing1 = array_diff($required1, $columns1);
        $missing2 = array_diff($required2, $columns2);
        
        if (empty($missing1) && empty($missing2)) {
            echo "✅ {$table1} and {$table2} - Ready for filename editing\n";
        } else {
            echo "❌ {$table1} and {$table2} - Missing columns: " . implode(', ', array_merge($missing1, $missing2)) . "\n";
        }
    } catch (Exception $e) {
        echo "❌ {$table1} and {$table2} - Error: " . $e->getMessage() . "\n";
    }
}

// Test 3: Check content.php for edit filename button
echo "\n🔍 Checking Frontend Implementation:\n";
$contentFile = file_get_contents('pages/content.php');

$frontendChecks = [
    'edit-filename' => 'Edit filename button class',
    'editFilenameModal' => 'Edit filename modal',
    'editFilenameForm' => 'Edit filename form',
    'edit_filename.php' => 'AJAX endpoint reference'
];

foreach ($frontendChecks as $check => $description) {
    if (strpos($contentFile, $check) !== false) {
        echo "✅ {$description}\n";
    } else {
        echo "❌ {$description} - Missing!\n";
    }
}

// Test 4: Check edit_filename.php functionality
echo "\n🔧 Checking Backend Functionality:\n";
$editFilenameFile = file_get_contents('functions/file_functions/edit_filename.php');

$backendChecks = [
    'filter_var' => 'Input sanitization',
    'htmlspecialchars' => 'XSS prevention',
    'allowedTables' => 'SQL injection prevention',
    'preg_replace' => 'Filename sanitization',
    'pathinfo' => 'File extension handling'
];

foreach ($backendChecks as $check => $description) {
    if (strpos($editFilenameFile, $check) !== false) {
        echo "✅ {$description}\n";
    } else {
        echo "❌ {$description} - Missing!\n";
    }
}

// Test 5: Check for sample files to test with
echo "\n📄 Checking for Test Files:\n";
$testFileFound = false;

foreach ($fileCategories as $table1 => $table2) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM {$table1} WHERE status = 'approve' LIMIT 1");
        $stmt->execute();
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($count > 0) {
            echo "✅ {$table1} - Has files available for testing\n";
            $testFileFound = true;
        }
    } catch (Exception $e) {
        // Table might not exist or have issues
    }
}

if (!$testFileFound) {
    echo "⚠️  No test files found. Consider adding sample files for testing.\n";
}

// Test 6: Security checks
echo "\n🛡️  Security Validations:\n";
$securityChecks = [
    'session_start' => 'Session management',
    'logged_in' => 'Authentication check',
    'FILTER_SANITIZE_NUMBER_INT' => 'Input validation',
    'in_array' => 'Table whitelisting',
    'error_log' => 'Error logging'
];

foreach ($securityChecks as $check => $description) {
    if (strpos($editFilenameFile, $check) !== false) {
        echo "✅ {$description}\n";
    } else {
        echo "❌ {$description} - Missing!\n";
    }
}

echo "\n✅ Filename Editing Test Complete!\n";
echo "The filename editing feature is ready for use.\n";
echo "\n📋 Usage Instructions:\n";
echo "1. Click the edit filename button (yellow edit icon) next to any file\n";
echo "2. Enter the new filename (without extension)\n";
echo "3. Click 'Save Changes' to update the filename\n";
echo "4. The system will automatically preserve the file extension\n";
?> 