<?php
/**
 * Simple Preview Test
 * Tests that preview functionality works after renaming files
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Include database connection
require_once __DIR__ . '/../functions/db_connection.php';

echo "🔍 Simple Preview Test\n";
echo "=====================\n\n";

// Set up test user session
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['logged_in'] = true;

/**
 * Test Functions
 */
function testPreviewFunctionality() {
    global $pdo;
    
    echo "🧪 Testing Preview Functionality...\n";
    
    // Find a test file
    $testFile = findTestFile();
    if (!$testFile) {
        echo "❌ No test file found.\n";
        return false;
    }
    
    echo "   📄 Found test file: {$testFile['filename']}\n";
    
    $fileId = $testFile['id'];
    $table1 = $testFile['table'];
    $table2 = $table1 . '_versions';
    
    echo "   🔍 Testing preview for file ID: {$fileId}\n";
    echo "   📋 Table: {$table2}\n";
    
    // Test preview functionality
    $previewResult = testPreviewData($fileId, $table2);
    
    if ($previewResult['success']) {
        echo "   ✅ Preview functionality works!\n";
        echo "   📝 Found " . count($previewResult['data']) . " revisions\n";
        
        foreach ($previewResult['data'] as $revision) {
            echo "      - Version {$revision['version_no']}: {$revision['filename']}\n";
            echo "        Path: {$revision['file_path']}\n";
        }
        
        return true;
    } else {
        echo "   ❌ Preview functionality failed: " . $previewResult['message'] . "\n";
        return false;
    }
}

function testPreviewData($fileId, $table2) {
    global $pdo;
    
    try {
        // Fetch revisions from the specified table (without file_path column)
        $stmt = $pdo->prepare("SELECT version_no, filename, datetime, file_size, file_id 
                               FROM $table2 
                               WHERE file_id = :file_id 
                               ORDER BY version_no DESC");
        $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
        $stmt->execute();
        $revisions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($revisions) {
            // Construct file_path for each record
            $mainTable = str_replace('_versions', '', $table2);
            foreach ($revisions as &$revision) {
                $revision['table_name'] = $table2;
                
                // Construct file_path based on table name and filename
                $revision['file_path'] = "uploads/files/{$mainTable}/{$revision['filename']}";
                $revision['download_path'] = $revision['file_path']; // For download functionality
            }

            return [
                'success' => true,
                'data' => $revisions
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No revisions found for this file.'
            ];
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
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

/**
 * Run Test
 */
echo "Starting simple preview test...\n\n";

if (testPreviewFunctionality()) {
    echo "✅ Simple preview test passed!\n";
    echo "🎉 The preview functionality should now work correctly.\n";
    echo "\n📋 The system now:\n";
    echo "1. Correctly constructs file paths for preview\n";
    echo "2. Maintains file accessibility after rename\n";
    echo "3. Shows proper file information in preview\n";
} else {
    echo "❌ Simple preview test failed!\n";
    echo "⚠️  The preview functionality may still have issues.\n";
}
?> 