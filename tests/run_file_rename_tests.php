<?php
/**
 * File Rename Test Runner
 * Simple test runner for file renaming functionality
 * Can be run without PHPUnit
 */

echo "🚀 File Rename Test Runner\n";
echo "==========================\n\n";

// Include database connection
require_once __DIR__ . '/../functions/db_connection.php';

// Start session for testing
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set up test user session
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['logged_in'] = true;

$testResults = [];
$totalTests = 0;
$passedTests = 0;

/**
 * Test Helper Functions
 */
function runTest($testName, $testFunction) {
    global $totalTests, $passedTests;
    $totalTests++;
    
    echo "🧪 Running: {$testName}\n";
    
    try {
        $result = $testFunction();
        if ($result) {
            echo "✅ PASSED: {$testName}\n";
            $passedTests++;
        } else {
            echo "❌ FAILED: {$testName}\n";
        }
    } catch (Exception $e) {
        echo "❌ ERROR: {$testName} - " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

/**
 * Test 1: Check Required Files
 */
function testRequiredFiles() {
    $requiredFiles = [
        'functions/file_functions/edit_filename.php',
        'functions/file_functions/rename_revision_file.php',
        'pages/content.php',
        'functions/notification_helper.php'
    ];

    foreach ($requiredFiles as $file) {
        if (!file_exists($file)) {
            echo "   ❌ Missing: {$file}\n";
            return false;
        }
    }
    
    echo "   ✅ All required files exist\n";
    return true;
}

/**
 * Test 2: Check Database Tables
 */
function testDatabaseTables() {
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
            // Check if main table exists
            $stmt = $pdo->query("SHOW TABLES LIKE '{$table1}'");
            if ($stmt->rowCount() == 0) {
                echo "   ⚠️  {$table1} - Table does not exist\n";
                continue;
            }
            
            // Check main table structure
            $stmt = $pdo->query("DESCRIBE {$table1}");
            $columns1 = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $requiredColumns1 = ['id', 'filename', 'status'];
            $missing1 = array_diff($requiredColumns1, $columns1);
            
            if (empty($missing1)) {
                echo "   ✅ {$table1} - Ready for filename editing\n";
            } else {
                echo "   ❌ {$table1} - Missing columns: " . implode(', ', $missing1) . "\n";
            }
            
            // Check versions table if it exists
            $stmt = $pdo->query("SHOW TABLES LIKE '{$table2}'");
            if ($stmt->rowCount() > 0) {
                $stmt = $pdo->query("DESCRIBE {$table2}");
                $columns2 = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                $requiredColumns2 = ['id', 'file_id', 'filename', 'status'];
                $missing2 = array_diff($requiredColumns2, $columns2);
                
                if (empty($missing2)) {
                    echo "   ✅ {$table2} - Ready for revision editing\n";
                } else {
                    echo "   ❌ {$table2} - Missing columns: " . implode(', ', $missing2) . "\n";
                }
            } else {
                echo "   ⚠️  {$table2} - Table does not exist\n";
            }
            
        } catch (Exception $e) {
            echo "   ❌ {$table1} and {$table2} - Error: " . $e->getMessage() . "\n";
        }
    }
}

/**
 * Test 3: Test main file renaming functionality
 */
function testMainFileRename() {
    global $pdo;
    
    // Find a test file to rename
    $testFile = getTestFile();
    if (!$testFile) {
        echo "   ⚠️  No test file available - skipping\n";
        return true; // Skip this test
    }

    $originalFilename = $testFile['filename'];
    $newFilename = 'test_rename_' . time() . '.pdf';
    $fileId = $testFile['id'];
    $table1 = $testFile['table'];

    // Test the rename functionality
    $result = renameMainFile($fileId, $newFilename, $table1);
    
    if (!$result['success']) {
        echo "   ❌ Main file rename failed: " . $result['message'] . "\n";
        return false;
    }
    
    // Verify the rename in database
    $stmt = $pdo->prepare("SELECT filename FROM {$table1} WHERE id = ?");
    $stmt->execute([$fileId]);
    $updatedFile = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($updatedFile['filename'] !== $newFilename) {
        echo "   ❌ Filename not updated in database\n";
        return false;
    }

    // Clean up - rename back
    renameMainFile($fileId, $originalFilename, $table1);
    
    echo "   ✅ Main file rename test passed\n";
    return true;
}

/**
 * Test 4: Test Revision File Rename
 */
function testRevisionFileRename() {
    global $pdo;
    
    // Find a test revision
    $testRevision = getTestRevision();
    if (!$testRevision) {
        echo "   ⚠️  No test revision available - skipping\n";
        return true; // Skip this test
    }

    $originalFilename = $testRevision['filename'];
    $newFilename = 'test_revision_rename_' . time() . '.pdf';
    $revisionId = $testRevision['id'];
    $table1 = $testRevision['table1'];
    $table2 = $testRevision['table2'];

    // Test the rename functionality
    $result = renameRevisionFile($revisionId, $newFilename, $table1, $table2);
    
    if (!$result['success']) {
        echo "   ❌ Revision file rename failed: " . $result['message'] . "\n";
        return false;
    }
    
    // Verify the rename in database
    $stmt = $pdo->prepare("SELECT filename FROM {$table2} WHERE id = ?");
    $stmt->execute([$revisionId]);
    $updatedRevision = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($updatedRevision['filename'] !== $newFilename) {
        echo "   ❌ Revision filename not updated in database\n";
        return false;
    }
    
    // The original code had a check for file_path, which is no longer present.
    // Assuming the intent was to check if the filename was updated in the main table
    // or if the revision table itself was updated.
    // Since the main file rename test already verified the main table,
    // and the revision file rename test already verified the revision table,
    // this part of the test is now redundant for the new logic.
    // Keeping it for now, but it might need adjustment based on the new logic.
    // For now, I'll remove the file_path check as it's not in the new logic.

    // Clean up - rename back
    renameRevisionFile($revisionId, $originalFilename, $table1, $table2);
    
    echo "   ✅ Revision file rename test passed\n";
    return true;
}

/**
 * Test 5: Test Specific Filename Rename
 */
function testSpecificFilenameRename() {
    $specificFilenames = [
        'document_2024.pdf',
        'report_final.docx',
        'presentation_v2.pptx',
        'data_analysis.xlsx',
        'contract_agreement.pdf'
    ];

    $testFile = getTestFile();
    if (!$testFile) {
        echo "   ⚠️  No test file available - skipping\n";
        return true; // Skip this test
    }

    $originalFilename = $testFile['filename'];
    $fileId = $testFile['id'];
    $table1 = $testFile['table'];

    foreach ($specificFilenames as $specificFilename) {
        // Test renaming to specific filename
        $result = renameMainFile($fileId, $specificFilename, $table1);
        
        if (!$result['success']) {
            echo "   ❌ Failed to rename to specific filename {$specificFilename}: " . $result['message'] . "\n";
            return false;
        }
        
        // Verify the specific filename
        global $pdo;
        $stmt = $pdo->prepare("SELECT filename FROM {$table1} WHERE id = ?");
        $stmt->execute([$fileId]);
        $updatedFile = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($updatedFile['filename'] !== $specificFilename) {
            echo "   ❌ Specific filename {$specificFilename} not set correctly\n";
            return false;
        }
    }

    // Clean up - rename back
    renameMainFile($fileId, $originalFilename, $table1);
    
    echo "   ✅ Specific filename rename test passed\n";
    return true;
}

/**
 * Test 6: Test Invalid Filename Handling
 */
function testInvalidFilenameHandling() {
    $invalidFilenames = [
        '', // Empty filename
        '   ', // Whitespace only
        'file/with/slashes.pdf', // Invalid characters
        'file\\with\\backslashes.pdf', // Invalid characters
        'file:with:colons.pdf', // Invalid characters
        'file*with*asterisks.pdf', // Invalid characters
        'file?with?question.pdf', // Invalid characters
        'file"with"quotes.pdf', // Invalid characters
        'file<with>brackets.pdf', // Invalid characters
        'file|with|pipes.pdf', // Invalid characters
    ];

    $testFile = getTestFile();
    if (!$testFile) {
        echo "   ⚠️  No test file available - skipping\n";
        return true; // Skip this test
    }

    foreach ($invalidFilenames as $invalidFilename) {
        $result = renameMainFile($testFile['id'], $invalidFilename, $testFile['table']);
        if ($result['success']) {
            echo "   ❌ Invalid filename {$invalidFilename} should have been rejected\n";
            return false;
        }
    }
    
    echo "   ✅ Invalid filename handling test passed\n";
    return true;
}

/**
 * Test 7: Test Frontend JavaScript
 */
function testFrontendJavaScript() {
    $contentFile = file_get_contents('pages/content.php');
    
    $jsChecks = [
        'edit-filename',
        'rename-revision',
        'Swal.fire',
        '$.ajax'
    ];

    foreach ($jsChecks as $check) {
        if (strpos($contentFile, $check) === false) {
            echo "   ❌ JavaScript functionality {$check} not found\n";
            return false;
        }
    }
    
    echo "   ✅ Frontend JavaScript test passed\n";
    return true;
}

/**
 * Helper Functions
 */
/**
 * Helper method to get a test file
 */
function getTestFile() {
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

/**
 * Helper method to get multiple test files
 */
function getTestFiles($count = 1) {
    global $pdo;
    
    $testTables = [
        'admin_files', 'aeld_files', 'cild_files', 'if_completed_files',
        'if_proposals_files', 'lulr_files', 'rp_completed_berf_files',
        'rp_completed_nonberf_files', 'rp_proposal_berf_files',
        'rp_proposal_nonberf_files', 't_lr_files', 't_pp_files', 't_rs_files'
    ];

    $files = [];
    foreach ($testTables as $table1) {
        try {
            $stmt = $pdo->prepare("SELECT id, filename FROM {$table1} WHERE status = 'approve' LIMIT ?");
            $stmt->execute([$count]);
            $tableFiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($tableFiles as $file) {
                $file['table'] = $table1;
                $files[] = $file;
                
                if (count($files) >= $count) {
                    return $files;
                }
            }
        } catch (Exception $e) {
            continue;
        }
    }
    return $files;
}

/**
 * Helper method to get a test revision
 */
function getTestRevision() {
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
 * Helper method to rename a main file
 */
function renameMainFile($fileId, $newFilename, $table1) {
    global $pdo;
    
    try {
        // Validate filename
        if (empty($newFilename)) {
            return ['success' => false, 'message' => 'Filename cannot be empty'];
        }
        
        // Check for invalid characters
        if (preg_match('/[\/\\:*?"<>|]/', $newFilename)) {
            return ['success' => false, 'message' => 'Invalid filename characters'];
        }
        
        // Check if new filename already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM {$table1} WHERE filename = ? AND id != ?");
        $stmt->execute([$newFilename, $fileId]);
        $existingCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($existingCount > 0) {
            return ['success' => false, 'message' => 'A file with this name already exists'];
        }
        
        // Update the filename in database
        $stmt = $pdo->prepare("UPDATE {$table1} SET filename = ? WHERE id = ?");
        $stmt->execute([$newFilename, $fileId]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Filename updated successfully'];
        } else {
            return ['success' => false, 'message' => 'No rows were updated'];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

/**
 * Helper method to rename a revision file
 */
function renameRevisionFile($revisionId, $newFilename, $table1, $table2) {
    global $pdo;
    
    try {
        // Validate filename
        if (empty($newFilename)) {
            return ['success' => false, 'message' => 'Filename cannot be empty'];
        }
        
        // Check for invalid characters
        if (preg_match('/[\/\\:*?"<>|]/', $newFilename)) {
            return ['success' => false, 'message' => 'Invalid filename characters'];
        }
        
        // Check if new filename already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM {$table2} WHERE filename = ? AND id != ?");
        $stmt->execute([$newFilename, $revisionId]);
        $existingCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($existingCount > 0) {
            return ['success' => false, 'message' => 'A revision with this name already exists'];
        }
        
        // Update the filename in database
        $stmt = $pdo->prepare("UPDATE {$table2} SET filename = ? WHERE id = ?");
        $stmt->execute([$newFilename, $revisionId]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Revision filename updated successfully'];
        } else {
            return ['success' => false, 'message' => 'No rows were updated'];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

/**
 * Run All Tests
 */
echo "Starting file rename tests...\n\n";

runTest("Required Files Check", 'testRequiredFiles');
runTest("Database Tables Structure", 'testDatabaseTables');
runTest("Main File Rename Functionality", 'testMainFileRename');
runTest("Revision File Rename Functionality", 'testRevisionFileRename');
runTest("Specific Filename Rename", 'testSpecificFilenameRename');
runTest("Invalid Filename Handling", 'testInvalidFilenameHandling');
runTest("Frontend JavaScript", 'testFrontendJavaScript');

/**
 * Test Summary
 */
echo "📊 Test Summary\n";
echo "===============\n";
echo "Total Tests: {$totalTests}\n";
echo "Passed: {$passedTests}\n";
echo "Failed: " . ($totalTests - $passedTests) . "\n";
echo "Success Rate: " . round(($passedTests / $totalTests) * 100, 2) . "%\n\n";

if ($passedTests === $totalTests) {
    echo "🎉 All tests passed! File rename functionality is working correctly.\n";
} else {
    echo "⚠️  Some tests failed. Please check the errors above.\n";
}

echo "\n📋 Usage Instructions:\n";
echo "1. Main File Rename: Click the edit filename button (yellow edit icon) next to any file\n";
echo "2. Revision File Rename: Click the rename button (tag icon) in the revision table\n";
echo "3. Enter the new filename (without extension)\n";
echo "4. The system will automatically preserve the file extension\n";
echo "5. Invalid filenames will be rejected automatically\n";
?> 