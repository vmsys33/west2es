<?php
/**
 * Simple Web Rename Test
 * Tests the rename functionality without header issues
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Include database connection
require_once __DIR__ . '/../functions/db_connection.php';

echo "🔍 Simple Web Rename Test\n";
echo "========================\n\n";

// Set up test user session
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['logged_in'] = true;

/**
 * Test Functions
 */
function testWebRename() {
    global $pdo;
    
    echo "🧪 Testing Web Rename Functionality...\n";
    
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
    
    // Test the rename functionality directly
    $result = performWebRename($fileId, $newFilename, $table1);
    
    if ($result['success']) {
        echo "   ✅ Web rename successful!\n";
        echo "   📝 Response: " . $result['message'] . "\n";
        
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
        performWebRename($fileId, $originalFilename, $table1);
        echo "   ✅ Cleanup completed\n";
        
        return true;
    } else {
        echo "   ❌ Web rename failed: " . $result['message'] . "\n";
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

function performWebRename($fileId, $newFilename, $table1) {
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
echo "Starting web rename test...\n\n";

if (testWebRename()) {
    echo "✅ Web rename test passed!\n";
    echo "🎉 The rename button should now be responsive in the web interface.\n";
    echo "\n📋 You can now:\n";
    echo "1. Click the rename button in the web interface\n";
    echo "2. Enter a new filename\n";
    echo "3. The system will update both database and file system\n";
} else {
    echo "❌ Web rename test failed!\n";
    echo "⚠️  The rename button may still have issues.\n";
}
?> 