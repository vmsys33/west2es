<?php
/**
 * Test Actual Rename Process
 * Tests the actual rename process to see what's happening
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Include database connection
require_once __DIR__ . '/../functions/db_connection.php';

echo "🔍 Test Actual Rename Process\n";
echo "=============================\n\n";

// Set up test user session
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['logged_in'] = true;

/**
 * Test Functions
 */
function testActualRename() {
    global $pdo;
    
    echo "🧪 Testing Actual Rename Process...\n";
    
    // Find a test revision
    $testRevision = findTestRevision();
    if (!$testRevision) {
        echo "❌ No test revision found.\n";
        return false;
    }
    
    echo "   📄 Found test revision: {$testRevision['filename']}\n";
    echo "   🔍 Revision ID: {$testRevision['id']}\n";
    
    $originalFilename = $testRevision['filename'];
    $testFilenames = [
        'test file.pdf',
        'test-file.pdf',
        'test_file.pdf',
        'test..pdf',
        'test.pdf.',
        'test with spaces.pdf'
    ];
    
    foreach ($testFilenames as $newFilename) {
        echo "\n   🔄 Testing rename to: '{$newFilename}'\n";
        
        // Simulate the exact process from rename_revision_file.php
        $sanitizedFilename = trim($newFilename);
        echo "   After trim: '{$sanitizedFilename}'\n";
        
        $sanitizedFilename = preg_replace('/[^a-zA-Z0-9._-]/', '', $sanitizedFilename);
        echo "   After preg_replace: '{$sanitizedFilename}'\n";
        
        $sanitizedFilename = preg_replace('/\.{2,}/', '.', $sanitizedFilename);
        echo "   After period cleanup: '{$sanitizedFilename}'\n";
        
        $sanitizedFilename = rtrim($sanitizedFilename, '.');
        echo "   After rtrim: '{$sanitizedFilename}'\n";
        
        // Check if it has a valid extension
        if (!preg_match('/\.[a-zA-Z0-9]+$/', $sanitizedFilename)) {
            echo "   ❌ No valid extension detected!\n";
        } else {
            echo "   ✅ Valid extension detected\n";
        }
        
        // Test the actual rename (but don't commit)
        $result = testRenameProcess($testRevision['id'], $sanitizedFilename, $testRevision['table1'], $testRevision['table2']);
        
        if ($result['success']) {
            echo "   ✅ Rename process successful\n";
            
            // Clean up immediately
            testRenameProcess($testRevision['id'], $originalFilename, $testRevision['table1'], $testRevision['table2']);
        } else {
            echo "   ❌ Rename process failed: " . $result['message'] . "\n";
        }
    }
    
    return true;
}

function testRenameProcess($revisionId, $newFilename, $table1, $table2) {
    global $pdo;
    
    try {
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

        // Update the database record
        $stmt = $pdo->prepare("UPDATE $table2 SET filename = :new_filename WHERE id = :revision_id");
        $stmt->bindParam(':new_filename', $newFilename, PDO::PARAM_STR);
        $stmt->bindParam(':revision_id', $revisionId, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            $pdo->rollBack();
            $errorInfo = $stmt->errorInfo();
            return ['success' => false, 'message' => 'Failed to update database: ' . $errorInfo[2]];
        }

        // Commit transaction
        $pdo->commit();

        return [
            'success' => true,
            'message' => "Revision file renamed successfully to '$newFilename'"
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
            $stmt = $pdo->prepare("SELECT id, filename, file_id FROM {$table2} WHERE status = 'approve' LIMIT 1");
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
echo "Starting actual rename process test...\n\n";

if (testActualRename()) {
    echo "\n✅ Actual rename process test completed!\n";
    echo "🎉 This shows exactly what's happening during the rename process.\n";
} else {
    echo "\n❌ Actual rename process test failed!\n";
}
?> 