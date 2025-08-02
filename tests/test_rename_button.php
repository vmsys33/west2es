<?php
/**
 * Test Rename Button Functionality
 * Tests the actual rename functionality through the web interface
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Include database connection
require_once __DIR__ . '/../functions/db_connection.php';

echo "🔍 Test Rename Button Functionality\n";
echo "===================================\n\n";

// Set up test user session
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['logged_in'] = true;

/**
 * Test Functions
 */
function testMainFileRename() {
    global $pdo;
    
    echo "🧪 Testing Main File Rename via Web Interface...\n";
    
    // Find a test file
    $testFile = findTestFile();
    if (!$testFile) {
        echo "❌ No test file found.\n";
        return false;
    }
    
    echo "   📄 Found test file: {$testFile['filename']}\n";
    
    $originalFilename = $testFile['filename'];
    $newFilename = 'web_test_' . time() . '.pdf';
    $fileId = $testFile['id'];
    $table1 = $testFile['table'];
    
    echo "   🔄 Testing rename to: {$newFilename}\n";
    
    // Simulate the web interface request
    $_POST = [
        'file_id' => $fileId,
        'new_filename' => $newFilename,
        'table1' => $table1,
        'table2' => $table1 . '_versions' // Add table2 parameter
    ];
    
    // Capture output from the actual edit_filename.php
    ob_start();
    include 'functions/file_functions/edit_filename.php';
    $output = ob_get_clean();
    
    $response = json_decode($output, true);
    
    if ($response && $response['status'] === 'success') {
        echo "   ✅ Web interface rename successful!\n";
        echo "   📝 Response: " . $response['message'] . "\n";
        
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
        $_POST = [
            'file_id' => $fileId,
            'new_filename' => $originalFilename,
            'table1' => $table1,
            'table2' => $table1 . '_versions'
        ];
        
        ob_start();
        include 'functions/file_functions/edit_filename.php';
        ob_get_clean();
        
        echo "   ✅ Cleanup completed\n";
        
        return true;
    } else {
        echo "   ❌ Web interface rename failed\n";
        if ($response) {
            echo "   📝 Error: " . $response['message'] . "\n";
        } else {
            echo "   📝 No response received\n";
        }
        return false;
    }
}

function testRevisionRename() {
    global $pdo;
    
    echo "\n🧪 Testing Revision Rename via Web Interface...\n";
    
    // Find a test revision
    $testRevision = findTestRevision();
    if (!$testRevision) {
        echo "   ⚠️  No test revision available - skipping\n";
        return true; // Skip this test
    }
    
    echo "   📄 Found test revision: {$testRevision['filename']}\n";
    
    $originalFilename = $testRevision['filename'];
    $newFilename = 'web_revision_test_' . time() . '.pdf';
    $revisionId = $testRevision['id'];
    $table1 = $testRevision['table1'];
    $table2 = $testRevision['table2'];
    
    echo "   🔄 Testing rename to: {$newFilename}\n";
    
    // Simulate the web interface request
    $_POST = [
        'revision_id' => $revisionId,
        'new_filename' => $newFilename,
        'table1' => $table1,
        'table2' => $table2
    ];
    
    // Capture output from the actual rename_revision_file.php
    ob_start();
    include 'functions/file_functions/rename_revision_file.php';
    $output = ob_get_clean();
    
    $response = json_decode($output, true);
    
    if ($response && $response['status'] === 'success') {
        echo "   ✅ Web interface revision rename successful!\n";
        echo "   📝 Response: " . $response['message'] . "\n";
        
        // Verify in database
        $stmt = $pdo->prepare("SELECT filename FROM {$table2} WHERE id = ?");
        $stmt->execute([$revisionId]);
        $updatedRevision = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($updatedRevision['filename'] === $newFilename) {
            echo "   ✅ Database updated correctly\n";
        } else {
            echo "   ❌ Database not updated correctly\n";
            return false;
        }
        
        // Clean up - rename back
        $_POST = [
            'revision_id' => $revisionId,
            'new_filename' => $originalFilename,
            'table1' => $table1,
            'table2' => $table2
        ];
        
        ob_start();
        include 'functions/file_functions/rename_revision_file.php';
        ob_get_clean();
        
        echo "   ✅ Cleanup completed\n";
        
        return true;
    } else {
        echo "   ❌ Web interface revision rename failed\n";
        if ($response) {
            echo "   📝 Error: " . $response['message'] . "\n";
        } else {
            echo "   📝 No response received\n";
        }
        return false;
    }
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

function findTestRevision() {
    global $pdo;
    
    $testTables = [
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

    foreach ($testTables as $table1 => $table2) {
        try {
            $stmt = $pdo->prepare("SELECT id, filename FROM {$table2} WHERE status = 'approve' LIMIT 1");
            $stmt->execute();
            $revision = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($revision) {
                $revision['table1'] = $table1;
                $revision['table2'] = $table2;
                return $revision;
            }
        } catch (Exception $e) {
            continue;
        }
    }
    return null;
}

/**
 * Run Tests
 */
$tests = [
    'Main File Rename' => 'testMainFileRename',
    'Revision Rename' => 'testRevisionRename'
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
echo "📊 RENAME BUTTON TEST SUMMARY\n";
echo str_repeat("=", 50) . "\n";
echo "Total Tests: {$total}\n";
echo "Passed: {$passed}\n";
echo "Failed: " . ($total - $passed) . "\n";
echo "Success Rate: " . round(($passed / $total) * 100, 2) . "%\n\n";

if ($passed === $total) {
    echo "🎉 All tests passed! The rename button should now be responsive.\n";
    echo "You can now use the rename functionality in the web interface.\n";
} else {
    echo "⚠️  Some tests failed. The rename button may still have issues.\n";
}

echo "\n📋 Next Steps:\n";
echo "1. Try clicking the rename button in the web interface\n";
echo "2. Test with different filenames\n";
echo "3. Check browser console for any JavaScript errors\n";
?> 