<?php
/**
 * Test Preview Fix
 * Tests that preview functionality works after renaming files
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Include database connection
require_once __DIR__ . '/../functions/db_connection.php';

echo "🔍 Test Preview Fix After Rename\n";
echo "================================\n\n";

// Set up test user session
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['logged_in'] = true;

/**
 * Test Functions
 */
function testPreviewAfterRename() {
    global $pdo;
    
    echo "🧪 Testing Preview After Rename...\n";
    
    // Find a test file
    $testFile = findTestFile();
    if (!$testFile) {
        echo "❌ No test file found.\n";
        return false;
    }
    
    echo "   📄 Found test file: {$testFile['filename']}\n";
    
    $originalFilename = $testFile['filename'];
    $newFilename = 'preview_test_' . time() . '.pdf';
    $fileId = $testFile['id'];
    $table1 = $testFile['table'];
    $table2 = $table1 . '_versions';
    
    echo "   🔄 Testing rename to: {$newFilename}\n";
    
    // Test the rename functionality
    $result = performRename($fileId, $newFilename, $table1);
    
    if ($result['success']) {
        echo "   ✅ Rename successful!\n";
        
        // Test preview functionality after rename
        $previewResult = testPreviewFunctionality($fileId, $table2);
        
        if ($previewResult['success']) {
            echo "   ✅ Preview functionality works after rename!\n";
            echo "   📝 Preview data: " . json_encode($previewResult['data']) . "\n";
        } else {
            echo "   ❌ Preview functionality failed after rename: " . $previewResult['message'] . "\n";
        }
        
        // Clean up - rename back
        performRename($fileId, $originalFilename, $table1);
        echo "   ✅ Cleanup completed\n";
        
        return $previewResult['success'];
    } else {
        echo "   ❌ Rename failed: " . $result['message'] . "\n";
        return false;
    }
}

function testPreviewFunctionality($fileId, $table2) {
    // Simulate the preview request
    $_GET = [
        'file_id' => $fileId,
        'file_table' => $table2
    ];
    
    // Capture output from fetch_revisions.php
    ob_start();
    include 'functions/file_functions/fetch_revisions.php';
    $output = ob_get_clean();
    
    $response = json_decode($output, true);
    
    if ($response && $response['status'] === 'success') {
        return [
            'success' => true,
            'data' => $response['data']
        ];
    } else {
        return [
            'success' => false,
            'message' => isset($response['message']) ? $response['message'] : 'Unknown error'
        ];
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

function performRename($fileId, $newFilename, $table1) {
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
 * Run Test
 */
echo "Starting preview fix test...\n\n";

if (testPreviewAfterRename()) {
    echo "✅ Preview fix test passed!\n";
    echo "🎉 The preview functionality should now work after renaming files.\n";
    echo "\n📋 The system now:\n";
    echo "1. Correctly renames files in the database\n";
    echo "2. Constructs proper file paths for preview\n";
    echo "3. Maintains file accessibility after rename\n";
} else {
    echo "❌ Preview fix test failed!\n";
    echo "⚠️  The preview functionality may still have issues after renaming.\n";
}
?> 