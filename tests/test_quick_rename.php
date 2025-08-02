<?php
/**
 * Quick File Rename Test
 * Simple test to verify basic rename functionality
 */

// Start session first, before any output
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Include database connection
require_once __DIR__ . '/../functions/db_connection.php';

// Set up test user session
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['logged_in'] = true;

echo "🔍 Quick File Rename Test\n";
echo "========================\n\n";

/**
 * Quick Test Functions
 */
function testBasicRename() {
    global $pdo;
    
    echo "🧪 Testing Basic Rename Functionality...\n";
    
    // Find a test file
    $testFile = findTestFile();
    if (!$testFile) {
        echo "❌ No test file found. Please add some approved files to test with.\n";
        return false;
    }
    
    echo "   📄 Found test file: {$testFile['filename']}\n";
    
    $originalFilename = $testFile['filename'];
    $newFilename = 'quick_test_' . time() . '.pdf';
    $fileId = $testFile['id'];
    $table1 = $testFile['table'];
    
    echo "   🔄 Attempting to rename to: {$newFilename}\n";
    
    // Test the rename
    $result = performRename($fileId, $newFilename, $table1);
    
    if ($result['success']) {
        echo "   ✅ Rename successful!\n";
        
        // Verify in database
        $stmt = $pdo->prepare("SELECT filename FROM {$table1} WHERE id = ?");
        $stmt->execute([$fileId]);
        $updatedFile = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($updatedFile['filename'] === $newFilename) {
            echo "   ✅ Database updated correctly\n";
        } else {
            echo "   ❌ Database not updated correctly\n";
            return false;
        }
        
        // Clean up - rename back
        performRename($fileId, $originalFilename, $table1);
        echo "   ✅ Cleanup completed\n";
        
        return true;
    } else {
        echo "   ❌ Rename failed: " . $result['message'] . "\n";
        return false;
    }
}

function testSpecificFilename() {
    global $pdo;
    
    echo "\n🧪 Testing Specific Filename Rename...\n";
    
    $specificFilename = 'test_document_2024.pdf';
    
    // Find a test file
    $testFile = findTestFile();
    if (!$testFile) {
        echo "❌ No test file found.\n";
        return false;
    }
    
    $originalFilename = $testFile['filename'];
    $fileId = $testFile['id'];
    $table1 = $testFile['table'];
    
    echo "   📄 Testing rename to: {$specificFilename}\n";
    
    // Test the rename
    $result = performRename($fileId, $specificFilename, $table1);
    
    if ($result['success']) {
        echo "   ✅ Specific filename rename successful!\n";
        
        // Verify in database
        $stmt = $pdo->prepare("SELECT filename FROM {$table1} WHERE id = ?");
        $stmt->execute([$fileId]);
        $updatedFile = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($updatedFile['filename'] === $specificFilename) {
            echo "   ✅ Specific filename set correctly\n";
        } else {
            echo "   ❌ Specific filename not set correctly\n";
            return false;
        }
        
        // Clean up - rename back
        performRename($fileId, $originalFilename, $table1);
        echo "   ✅ Cleanup completed\n";
        
        return true;
    } else {
        echo "   ❌ Specific filename rename failed: " . $result['message'] . "\n";
        return false;
    }
}

function testInvalidFilename() {
    echo "\n🧪 Testing Invalid Filename Handling...\n";
    
    $invalidFilenames = [
        '', // Empty
        '   ', // Whitespace
        'file/with/slashes.pdf', // Invalid chars
        'file*with*asterisks.pdf' // Invalid chars
    ];
    
    $testFile = findTestFile();
    if (!$testFile) {
        echo "❌ No test file found.\n";
        return false;
    }
    
    $allRejected = true;
    
    foreach ($invalidFilenames as $invalidFilename) {
        $result = performRename($testFile['id'], $invalidFilename, $testFile['table']);
        
        if ($result['success']) {
            echo "   ❌ Invalid filename '{$invalidFilename}' was accepted (should be rejected)\n";
            $allRejected = false;
        } else {
            echo "   ✅ Invalid filename '{$invalidFilename}' correctly rejected\n";
        }
    }
    
    return $allRejected;
}

function findTestFile() {
    global $pdo;
    
    $testTables = [
        'admin_files', 'aeld_files', 'cild_files', 'if_completed_files',
        'if_proposals_files', 'lulr_files', 'rp_completed_berf_files',
        'rp_completed_nonberf_files', 'rp_proposal_berf_files',
        'rp_proposal_nonberf_files', 't_lr_files', 't_pp_files', 't_rs_files'
    ];

    foreach ($testTables as $table1) {
        try {
            $stmt = $pdo->prepare("SELECT id, filename FROM {$table1} WHERE status = 'approve' LIMIT 1");
            $stmt->execute();
            $file = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($file) {
                $file['table'] = $table1;
                return $file;
            }
        } catch (Exception $e) {
            continue;
        }
    }
    return null;
}

function performRename($fileId, $newFilename, $table1) {
    // Simulate the AJAX request
    $_POST = [
        'file_id' => $fileId,
        'new_filename' => $newFilename,
        'table1' => $table1
    ];

    // Capture output
    ob_start();
    include 'functions/file_functions/edit_filename.php';
    $output = ob_get_clean();

    $response = json_decode($output, true);
    return [
        'success' => isset($response['status']) && $response['status'] === 'success',
        'message' => isset($response['message']) ? $response['message'] : 'Unknown error'
    ];
}

/**
 * Run Quick Tests
 */
$tests = [
    'Basic Rename' => 'testBasicRename',
    'Specific Filename' => 'testSpecificFilename',
    'Invalid Filename Handling' => 'testInvalidFilename'
];

$passed = 0;
$total = count($tests);

foreach ($tests as $testName => $testFunction) {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Running: {$testName}\n";
    echo str_repeat("=", 50) . "\n";
    
    if ($testFunction()) {
        echo "✅ {$testName} - PASSED\n";
        $passed++;
    } else {
        echo "❌ {$testName} - FAILED\n";
    }
}

/**
 * Summary
 */
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 QUICK TEST SUMMARY\n";
echo str_repeat("=", 50) . "\n";
echo "Total Tests: {$total}\n";
echo "Passed: {$passed}\n";
echo "Failed: " . ($total - $passed) . "\n";
echo "Success Rate: " . round(($passed / $total) * 100, 2) . "%\n\n";

if ($passed === $total) {
    echo "🎉 All quick tests passed! File rename functionality is working.\n";
    echo "You can now run the full test suite with: php tests/run_file_rename_tests.php\n";
} else {
    echo "⚠️  Some tests failed. Please check the errors above.\n";
    echo "Make sure all required files exist and database is properly configured.\n";
}

echo "\n📋 Next Steps:\n";
echo "1. Run full test suite: php tests/run_file_rename_tests.php\n";
echo "2. Run PHPUnit tests: php vendor/bin/phpunit tests/FileRenameTest.php\n";
echo "3. Check documentation: tests/FILE_RENAME_TEST_DOCUMENTATION.md\n";
?> 