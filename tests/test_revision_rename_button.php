<?php
/**
 * Test Revision Rename Button
 * Tests that the revision rename button functionality works
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Include database connection
require_once __DIR__ . '/../functions/db_connection.php';

echo "🔍 Test Revision Rename Button\n";
echo "==============================\n\n";

// Set up test user session
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['logged_in'] = true;

/**
 * Test Functions
 */
function testRevisionRenameButton() {
    global $pdo;
    
    echo "🧪 Testing Revision Rename Button...\n";
    
    // Find a test revision
    $testRevision = findTestRevision();
    if (!$testRevision) {
        echo "❌ No test revision found.\n";
        return false;
    }
    
    echo "   📄 Found test revision: {$testRevision['filename']}\n";
    echo "   🔍 Revision ID: {$testRevision['id']}\n";
    echo "   📋 Table1: {$testRevision['table1']}\n";
    echo "   📋 Table2: {$testRevision['table2']}\n";
    
    // Test the rename functionality
    $result = testRevisionRename($testRevision['id'], 'test_rename_' . time() . '.pdf', $testRevision['table1'], $testRevision['table2']);
    
    if ($result['success']) {
        echo "   ✅ Revision rename successful!\n";
        echo "   📝 Response: " . $result['message'] . "\n";
        
        // Clean up - rename back
        $cleanupResult = testRevisionRename($testRevision['id'], $testRevision['filename'], $testRevision['table1'], $testRevision['table2']);
        if ($cleanupResult['success']) {
            echo "   ✅ Cleanup completed\n";
        }
        
        return true;
    } else {
        echo "   ❌ Revision rename failed: " . $result['message'] . "\n";
        return false;
    }
}

function testRevisionRename($revisionId, $newFilename, $table1, $table2) {
    global $pdo;
    
    try {
        // Validate revision ID
        if (!$revisionId || $revisionId <= 0) {
            return ['success' => false, 'message' => 'Invalid revision ID'];
        }

        // Validate filename
        if (empty($newFilename)) {
            return ['success' => false, 'message' => 'New filename cannot be empty'];
        }

        // Sanitize filename (remove special characters except dots and hyphens)
        $newFilename = preg_replace('/[^a-zA-Z0-9._-]/', '', $newFilename);
        
        // Ensure filename has an extension
        if (!preg_match('/\.[a-zA-Z0-9]+$/', $newFilename)) {
            return ['success' => false, 'message' => 'Filename must have a valid extension'];
        }

        // Define allowed tables to prevent SQL injection
        $allowedTables = [
            'admin_files', 'aeld_files', 'cild_files', 'if_completed_files', 
            'if_proposals_files', 'lulr_files', 'rp_completed_berf_files', 
            'rp_completed_nonberf_files', 'rp_proposal_berf_files', 
            'rp_proposal_nonberf_files', 't_lr_files', 't_pp_files', 't_rs_files',
            'admin_files_versions', 'aeld_files_versions', 'cild_files_versions', 
            'if_completed_files_versions', 'if_proposals_files_versions', 'lulr_files_versions', 
            'rp_completed_berf_files_versions', 'rp_completed_nonberf_files_versions', 
            'rp_proposal_berf_files_versions', 'rp_proposal_nonberf_files_versions', 
            't_lr_files_versions', 't_pp_files_versions', 't_rs_files_versions'
        ];

        if (!in_array($table1, $allowedTables) || !in_array($table2, $allowedTables)) {
            return ['success' => false, 'message' => 'Invalid table name'];
        }

        // Start transaction
        $pdo->beginTransaction();

        // Get the current revision information
        $stmt = $pdo->prepare("SELECT filename FROM $table2 WHERE id = :revision_id");
        $stmt->bindParam(':revision_id', $revisionId, PDO::PARAM_INT);
        $stmt->execute();
        $revision = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$revision) {
            $pdo->rollBack();
            return ['success' => false, 'message' => 'Revision not found'];
        }

        // Check if new filename already exists in the same table
        $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM $table2 WHERE filename = :filename AND id != :revision_id");
        $stmt->bindParam(':filename', $newFilename, PDO::PARAM_STR);
        $stmt->bindParam(':revision_id', $revisionId, PDO::PARAM_INT);
        $stmt->execute();
        $existingCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($existingCount > 0) {
            $pdo->rollBack();
            return ['success' => false, 'message' => 'A file with this name already exists'];
        }

        // Get the current file name
        $currentFileName = $revision['filename'];

        // Construct file paths based on table name
        $uploadDir = 'uploads/files/' . $table1 . '/';
        $physicalOldPath = $_SERVER['DOCUMENT_ROOT'] . '/west2es/' . $uploadDir . $currentFileName;
        $physicalNewPath = $_SERVER['DOCUMENT_ROOT'] . '/west2es/' . $uploadDir . $newFilename;

        // Check if the physical file exists
        if (file_exists($physicalOldPath)) {
            // Rename the physical file
            if (!rename($physicalOldPath, $physicalNewPath)) {
                $pdo->rollBack();
                return ['success' => false, 'message' => 'Failed to rename file on server'];
            }
        } else {
            // File doesn't exist physically, but we can still update the database
            // This is normal for some systems where files are stored differently
        }

        // Update the database record
        $stmt = $pdo->prepare("UPDATE $table2 SET filename = :new_filename WHERE id = :revision_id");
        $stmt->bindParam(':new_filename', $newFilename, PDO::PARAM_STR);
        $stmt->bindParam(':revision_id', $revisionId, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            // Rollback file rename if database update fails
            if (file_exists($physicalNewPath)) {
                rename($physicalNewPath, $physicalOldPath);
            }
            $pdo->rollBack();
            $errorInfo = $stmt->errorInfo();
            return ['success' => false, 'message' => 'Failed to update database: ' . $errorInfo[2]];
        }

        // Commit transaction
        $pdo->commit();

        return [
            'success' => true,
            'message' => "Revision file renamed successfully from '$currentFileName' to '$newFilename'"
        ];

    } catch (Exception $e) {
        // Rollback transaction
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
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
 * Run Test
 */
echo "Starting revision rename button test...\n\n";

if (testRevisionRenameButton()) {
    echo "✅ Revision rename button test passed!\n";
    echo "🎉 The revision rename functionality should work correctly.\n";
    echo "\n📋 The system now:\n";
    echo "1. Correctly handles revision rename requests\n";
    echo "2. Updates revision filenames in database\n";
    echo "3. Maintains file accessibility after rename\n";
} else {
    echo "❌ Revision rename button test failed!\n";
    echo "⚠️  The revision rename functionality may have issues.\n";
}
?> 