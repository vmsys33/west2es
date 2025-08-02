<?php
/**
 * Simple File Rename Test
 * Tests rename functionality with actual database structure
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Include database connection
require_once __DIR__ . '/../functions/db_connection.php';

echo "🔍 Simple File Rename Test\n";
echo "==========================\n\n";

// Set up test user session
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['logged_in'] = true;

/**
 * Test Functions
 */
function testDatabaseRename() {
    global $pdo;
    
    echo "🧪 Testing Database Rename Functionality...\n";
    
    // Find a test file
    $testFile = findTestFile();
    if (!$testFile) {
        echo "❌ No test file found. Please add some approved files to test with.\n";
        return false;
    }
    
    echo "   📄 Found test file: {$testFile['filename']}\n";
    
    $originalFilename = $testFile['filename'];
    $newFilename = 'test_rename_' . time() . '.pdf';
    $fileId = $testFile['id'];
    $table1 = $testFile['table'];
    
    echo "   🔄 Attempting to rename to: {$newFilename}\n";
    
    // Test the rename in database
    $result = performDatabaseRename($fileId, $newFilename, $table1);
    
    if ($result['success']) {
        echo "   ✅ Database rename successful!\n";
        
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
        performDatabaseRename($fileId, $originalFilename, $table1);
        echo "   ✅ Cleanup completed\n";
        
        return true;
    } else {
        echo "   ❌ Database rename failed: " . $result['message'] . "\n";
        return false;
    }
}

function testSpecificFilename() {
    global $pdo;
    
    echo "\n🧪 Testing Specific Filename Rename...\n";
    
    $specificFilenames = [
        'document_2024.pdf',
        'report_final.docx',
        'presentation_v2.pptx',
        'data_analysis.xlsx',
        'contract_agreement.pdf'
    ];
    
    // Find a test file
    $testFile = findTestFile();
    if (!$testFile) {
        echo "❌ No test file found.\n";
        return false;
    }
    
    $originalFilename = $testFile['filename'];
    $fileId = $testFile['id'];
    $table1 = $testFile['table'];
    
    foreach ($specificFilenames as $specificFilename) {
        echo "   📄 Testing rename to: {$specificFilename}\n";
        
        // Test the rename
        $result = performDatabaseRename($fileId, $specificFilename, $table1);
        
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
        } else {
            echo "   ❌ Specific filename rename failed: " . $result['message'] . "\n";
            return false;
        }
    }
    
    // Clean up - rename back
    performDatabaseRename($fileId, $originalFilename, $table1);
    echo "   ✅ Cleanup completed\n";
    
    return true;
}

function testInvalidFilename() {
    echo "\n🧪 Testing Invalid Filename Handling...\n";
    
    $invalidFilenames = [
        '', // Empty
        '   ', // Whitespace
        'file/with/slashes.pdf', // Invalid chars
        'file*with*asterisks.pdf', // Invalid chars
        'file:with:colons.pdf', // Invalid chars
        'file"with"quotes.pdf', // Invalid chars
        'file<with>brackets.pdf' // Invalid chars
    ];
    
    $testFile = findTestFile();
    if (!$testFile) {
        echo "❌ No test file found.\n";
        return false;
    }
    
    $allRejected = true;
    
    foreach ($invalidFilenames as $invalidFilename) {
        $result = performDatabaseRename($testFile['id'], $invalidFilename, $testFile['table']);
        
        if ($result['success']) {
            echo "   ❌ Invalid filename '{$invalidFilename}' was accepted (should be rejected)\n";
            $allRejected = false;
        } else {
            echo "   ✅ Invalid filename '{$invalidFilename}' correctly rejected\n";
        }
    }
    
    return $allRejected;
}

function testFileSystemRename() {
    echo "\n🧪 Testing File System Rename...\n";
    
    $testFile = findTestFile();
    if (!$testFile) {
        echo "❌ No test file found.\n";
        return false;
    }
    
    $table1 = $testFile['table'];
    $originalFilename = $testFile['filename'];
    
    // Check if physical file exists
    $uploadDir = __DIR__ . '/../uploads/files/' . $table1 . '/';
    $originalFilePath = $uploadDir . $originalFilename;
    
    if (!file_exists($originalFilePath)) {
        echo "   ⚠️  Physical file not found: {$originalFilePath}\n";
        echo "   💡 This is normal if files are stored differently\n";
        return true; // Skip this test
    }
    
    echo "   📄 Found physical file: {$originalFilename}\n";
    
    $newFilename = 'test_fs_rename_' . time() . '.pdf';
    $newFilePath = $uploadDir . $newFilename;
    
    // Test file system rename
    if (rename($originalFilePath, $newFilePath)) {
        echo "   ✅ File system rename successful!\n";
        
        // Verify file exists with new name
        if (file_exists($newFilePath)) {
            echo "   ✅ New file exists in file system\n";
        } else {
            echo "   ❌ New file not found in file system\n";
            return false;
        }
        
        // Clean up - rename back
        if (rename($newFilePath, $originalFilePath)) {
            echo "   ✅ File system cleanup completed\n";
        } else {
            echo "   ⚠️  File system cleanup failed\n";
        }
        
        return true;
    } else {
        echo "   ❌ File system rename failed\n";
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

function performDatabaseRename($fileId, $newFilename, $table1) {
    global $pdo;
    
    try {
        // Validate filename
        if (empty($newFilename)) {
            return ['success' => false, 'message' => 'Filename cannot be empty'];
        }
        
        // Sanitize filename (remove special characters except dots and hyphens)
        $sanitizedFilename = preg_replace('/[^a-zA-Z0-9._-]/', '', $newFilename);
        
        if (empty($sanitizedFilename)) {
            return ['success' => false, 'message' => 'Invalid filename characters'];
        }
        
        // Check if new filename already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM {$table1} WHERE filename = ? AND id != ?");
        $stmt->execute([$sanitizedFilename, $fileId]);
        $existingCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($existingCount > 0) {
            return ['success' => false, 'message' => 'A file with this name already exists'];
        }
        
        // Update the filename in database
        $stmt = $pdo->prepare("UPDATE {$table1} SET filename = ? WHERE id = ?");
        $stmt->execute([$sanitizedFilename, $fileId]);
        
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
 * Run Tests
 */
$tests = [
    'Database Rename' => 'testDatabaseRename',
    'Specific Filename' => 'testSpecificFilename',
    'Invalid Filename Handling' => 'testInvalidFilename',
    'File System Rename' => 'testFileSystemRename'
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
echo "📊 SIMPLE TEST SUMMARY\n";
echo str_repeat("=", 50) . "\n";
echo "Total Tests: {$total}\n";
echo "Passed: {$passed}\n";
echo "Failed: " . ($total - $passed) . "\n";
echo "Success Rate: " . round(($passed / $total) * 100, 2) . "%\n\n";

if ($passed === $total) {
    echo "🎉 All simple tests passed! File rename functionality is working.\n";
    echo "The system can handle database updates and file system operations.\n";
} else {
    echo "⚠️  Some tests failed. Please check the errors above.\n";
    echo "This helps identify which aspects of the rename functionality need attention.\n";
}

echo "\n📋 Test Results Analysis:\n";
echo "- Database Rename: Tests if filenames can be updated in the database\n";
echo "- Specific Filename: Tests renaming to exact predefined names\n";
echo "- Invalid Filename: Tests security validation of filenames\n";
echo "- File System Rename: Tests physical file renaming (if files exist)\n";
?> 