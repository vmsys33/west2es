<?php

use PHPUnit\Framework\TestCase;

/**
 * File Rename Test Suite
 * Tests the file renaming functionality for both main files and revision files
 */
class FileRenameTest extends TestCase
{
    private $pdo;
    private $testTables = [
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

    protected function setUp(): void
    {
        // Include database connection
        require_once __DIR__ . '/../functions/db_connection.php';
        global $pdo;
        $this->pdo = $pdo;
        
        // Start session for testing
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Set up test user session
        $_SESSION['user_id'] = 1;
        $_SESSION['role'] = 'admin';
        $_SESSION['logged_in'] = true;
    }

    protected function tearDown(): void
    {
        // Clean up any test data
        $this->cleanupTestData();
    }

    /**
     * Test 1: Check if required files exist
     */
    public function testRequiredFilesExist()
    {
        $requiredFiles = [
            'functions/file_functions/edit_filename.php',
            'functions/file_functions/rename_revision_file.php',
            'pages/content.php',
            'functions/notification_helper.php'
        ];

        foreach ($requiredFiles as $file) {
            $this->assertFileExists($file, "Required file {$file} does not exist");
        }
    }

    /**
     * Test 2: Check database tables structure
     */
    public function testDatabaseTablesStructure()
    {
        foreach ($this->testTables as $table1 => $table2) {
            // Check main table
            $stmt = $this->pdo->query("DESCRIBE {$table1}");
            $columns1 = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $requiredColumns1 = ['id', 'filename', 'file_path', 'status'];
            foreach ($requiredColumns1 as $column) {
                $this->assertContains($column, $columns1, "Table {$table1} missing column {$column}");
            }

            // Check versions table
            $stmt = $this->pdo->query("DESCRIBE {$table2}");
            $columns2 = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $requiredColumns2 = ['id', 'file_id', 'filename', 'file_path', 'status'];
            foreach ($requiredColumns2 as $column) {
                $this->assertContains($column, $columns2, "Table {$table2} missing column {$column}");
            }
        }
    }

    /**
     * Test 3: Test main file renaming functionality
     */
    public function testMainFileRename()
    {
        // Find a test file to rename
        $testFile = $this->getTestFile();
        if (!$testFile) {
            $this->markTestSkipped('No test file available for renaming');
        }

        $originalFilename = $testFile['filename'];
        $newFilename = 'test_rename_' . time() . '.pdf';
        $fileId = $testFile['id'];
        $table1 = $testFile['table'];

        // Test the rename functionality
        $result = $this->renameMainFile($fileId, $newFilename, $table1);
        
        $this->assertTrue($result['success'], 'Main file rename failed: ' . $result['message']);
        
        // Verify the rename in database
        $stmt = $this->pdo->prepare("SELECT filename, file_path FROM {$table1} WHERE id = ?");
        $stmt->execute([$fileId]);
        $updatedFile = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertEquals($newFilename, $updatedFile['filename'], 'Filename not updated in database');
        $this->assertStringContainsString($newFilename, $updatedFile['file_path'], 'File path not updated correctly');

        // Clean up - rename back
        $this->renameMainFile($fileId, $originalFilename, $table1);
    }

    /**
     * Test 4: Test revision file renaming functionality
     */
    public function testRevisionFileRename()
    {
        // Find a test revision to rename
        $testRevision = $this->getTestRevision();
        if (!$testRevision) {
            $this->markTestSkipped('No test revision available for renaming');
        }

        $originalFilename = $testRevision['filename'];
        $newFilename = 'test_revision_rename_' . time() . '.pdf';
        $revisionId = $testRevision['id'];
        $table1 = $testRevision['table1'];
        $table2 = $testRevision['table2'];

        // Test the rename functionality
        $result = $this->renameRevisionFile($revisionId, $newFilename, $table1, $table2);
        
        $this->assertTrue($result['success'], 'Revision file rename failed: ' . $result['message']);
        
        // Verify the rename in database
        $stmt = $this->pdo->prepare("SELECT filename, file_path FROM {$table2} WHERE id = ?");
        $stmt->execute([$revisionId]);
        $updatedRevision = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertEquals($newFilename, $updatedRevision['filename'], 'Revision filename not updated in database');
        $this->assertStringContainsString($newFilename, $updatedRevision['file_path'], 'Revision file path not updated correctly');

        // Clean up - rename back
        $this->renameRevisionFile($revisionId, $originalFilename, $table1, $table2);
    }

    /**
     * Test 5: Test specific filename renaming
     */
    public function testSpecificFilenameRename()
    {
        $specificFilenames = [
            'document_2024.pdf',
            'report_final.docx',
            'presentation_v2.pptx',
            'data_analysis.xlsx',
            'contract_agreement.pdf'
        ];

        foreach ($specificFilenames as $specificFilename) {
            // Find a test file
            $testFile = $this->getTestFile();
            if (!$testFile) {
                $this->markTestSkipped('No test file available for specific filename testing');
            }

            $originalFilename = $testFile['filename'];
            $fileId = $testFile['id'];
            $table1 = $testFile['table'];

            // Test renaming to specific filename
            $result = $this->renameMainFile($fileId, $specificFilename, $table1);
            
            $this->assertTrue($result['success'], "Failed to rename to specific filename {$specificFilename}: " . $result['message']);
            
            // Verify the specific filename
            $stmt = $this->pdo->prepare("SELECT filename FROM {$table1} WHERE id = ?");
            $stmt->execute([$fileId]);
            $updatedFile = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->assertEquals($specificFilename, $updatedFile['filename'], "Specific filename {$specificFilename} not set correctly");

            // Clean up - rename back
            $this->renameMainFile($fileId, $originalFilename, $table1);
        }
    }

    /**
     * Test 6: Test invalid filename handling
     */
    public function testInvalidFilenameHandling()
    {
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

        $testFile = $this->getTestFile();
        if (!$testFile) {
            $this->markTestSkipped('No test file available for invalid filename testing');
        }

        foreach ($invalidFilenames as $invalidFilename) {
            $result = $this->renameMainFile($testFile['id'], $invalidFilename, $testFile['table']);
            $this->assertFalse($result['success'], "Invalid filename {$invalidFilename} should have been rejected");
        }
    }

    /**
     * Test 7: Test duplicate filename handling
     */
    public function testDuplicateFilenameHandling()
    {
        // Get two different files
        $files = $this->getTestFiles(2);
        if (count($files) < 2) {
            $this->markTestSkipped('Need at least 2 test files for duplicate filename testing');
        }

        $file1 = $files[0];
        $file2 = $files[1];
        $duplicateFilename = 'duplicate_test_file.pdf';

        // Rename first file to the duplicate name
        $result1 = $this->renameMainFile($file1['id'], $duplicateFilename, $file1['table']);
        $this->assertTrue($result1['success'], 'First rename should succeed');

        // Try to rename second file to the same name
        $result2 = $this->renameMainFile($file2['id'], $duplicateFilename, $file2['table']);
        $this->assertFalse($result2['success'], 'Duplicate filename should be rejected');

        // Clean up
        $this->renameMainFile($file1['id'], $file1['filename'], $file1['table']);
    }

    /**
     * Test 8: Test file extension preservation
     */
    public function testFileExtensionPreservation()
    {
        $testFile = $this->getTestFile();
        if (!$testFile) {
            $this->markTestSkipped('No test file available for extension testing');
        }

        $originalFilename = $testFile['filename'];
        $originalExtension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $newFilenameWithoutExt = 'test_extension_' . time();
        $fileId = $testFile['id'];
        $table1 = $testFile['table'];

        // Rename without extension
        $result = $this->renameMainFile($fileId, $newFilenameWithoutExt, $table1);
        $this->assertTrue($result['success'], 'Rename without extension failed');

        // Check that extension was preserved
        $stmt = $this->pdo->prepare("SELECT filename FROM {$table1} WHERE id = ?");
        $stmt->execute([$fileId]);
        $updatedFile = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $expectedFilename = $newFilenameWithoutExt . '.' . $originalExtension;
        $this->assertEquals($expectedFilename, $updatedFile['filename'], 'File extension not preserved');

        // Clean up
        $this->renameMainFile($fileId, $originalFilename, $table1);
    }

    /**
     * Test 9: Test AJAX endpoint availability
     */
    public function testAjaxEndpointsAvailable()
    {
        $endpoints = [
            'functions/file_functions/edit_filename.php',
            'functions/file_functions/rename_revision_file.php'
        ];

        foreach ($endpoints as $endpoint) {
            $this->assertFileExists($endpoint, "AJAX endpoint {$endpoint} does not exist");
            
            // Check if endpoint returns JSON
            $content = file_get_contents($endpoint);
            $this->assertStringContainsString('Content-Type: application/json', $content, "Endpoint {$endpoint} should return JSON");
        }
    }

    /**
     * Test 10: Test frontend JavaScript functionality
     */
    public function testFrontendJavaScript()
    {
        $contentFile = file_get_contents('pages/content.php');
        
        // Check for required JavaScript functions
        $jsChecks = [
            'edit-filename',
            'rename-revision',
            'Swal.fire',
            '$.ajax',
            'console.log'
        ];

        foreach ($jsChecks as $check) {
            $this->assertStringContainsString($check, $contentFile, "JavaScript functionality {$check} not found");
        }
    }

    /**
     * Helper method to get a test file
     */
    private function getTestFile()
    {
        foreach ($this->testTables as $table1 => $table2) {
            try {
                $stmt = $this->pdo->prepare("SELECT id, filename, file_path FROM {$table1} WHERE status = 'approve' LIMIT 1");
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
    private function getTestFiles($count = 1)
    {
        $files = [];
        foreach ($this->testTables as $table1 => $table2) {
            try {
                $stmt = $this->pdo->prepare("SELECT id, filename, file_path FROM {$table1} WHERE status = 'approve' LIMIT ?");
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
    private function getTestRevision()
    {
        foreach ($this->testTables as $table1 => $table2) {
            try {
                $stmt = $this->pdo->prepare("SELECT id, filename, file_path FROM {$table2} WHERE status = 'approve' LIMIT 1");
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
    private function renameMainFile($fileId, $newFilename, $table1)
    {
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
     * Helper method to rename a revision file
     */
    private function renameRevisionFile($revisionId, $newFilename, $table1, $table2)
    {
        // Simulate the AJAX request
        $_POST = [
            'revision_id' => $revisionId,
            'new_filename' => $newFilename,
            'table1' => $table1,
            'table2' => $table2
        ];

        // Capture output
        ob_start();
        include 'functions/file_functions/rename_revision_file.php';
        $output = ob_get_clean();

        $response = json_decode($output, true);
        return [
            'success' => isset($response['status']) && $response['status'] === 'success',
            'message' => isset($response['message']) ? $response['message'] : 'Unknown error'
        ];
    }

    /**
     * Helper method to cleanup test data
     */
    private function cleanupTestData()
    {
        // Clean up any test files created during testing
        // This is a placeholder - implement as needed
    }
} 