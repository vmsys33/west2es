<?php
/**
 * CRUD Operations Test Unit for File Categories
 * Tests all file categories to ensure consistency across different tables
 */

require_once '../functions/db_connection.php';
require_once '../functions/file_operations.php';

class FileCategoryCRUDTest {
    private $pdo;
    private $testResults = [];
    private $testFile = [
        'name' => 'test_file.pdf',
        'tmp_name' => '/tmp/test_file.pdf',
        'size' => 1024,
        'type' => 'application/pdf',
        'error' => 0
    ];

    // All file categories and their corresponding tables
    private $fileCategories = [
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

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Run all CRUD tests for all file categories
     */
    public function runAllTests() {
        echo "🚀 Starting CRUD Operations Test for All File Categories\n";
        echo "=====================================================\n\n";

        foreach ($this->fileCategories as $table1 => $table2) {
            echo "📁 Testing Category: {$table1}\n";
            echo "----------------------------------------\n";
            
            $this->testFileCategory($table1, $table2);
            echo "\n";
        }

        $this->generateReport();
    }

    /**
     * Test CRUD operations for a specific file category
     */
    private function testFileCategory($table1, $table2) {
        $categoryResults = [
            'category' => $table1,
            'tests' => []
        ];

        // Test 1: Table Structure Validation
        $categoryResults['tests']['table_structure'] = $this->testTableStructure($table1, $table2);

        // Test 2: Create Operation
        $categoryResults['tests']['create'] = $this->testCreateOperation($table1, $table2);

        // Test 3: Read Operation
        $categoryResults['tests']['read'] = $this->testReadOperation($table1, $table2);

        // Test 4: Update Operation
        $categoryResults['tests']['update'] = $this->testUpdateOperation($table1, $table2);

        // Test 5: Delete Operation
        $categoryResults['tests']['delete'] = $this->testDeleteOperation($table1, $table2);

        // Test 6: File Operations Function
        $categoryResults['tests']['file_operations'] = $this->testFileOperationsFunction($table1);

        $this->testResults[$table1] = $categoryResults;
    }

    /**
     * Test 1: Validate table structure
     */
    private function testTableStructure($table1, $table2) {
        try {
            // Check if tables exist
            $stmt1 = $this->pdo->query("SHOW TABLES LIKE '{$table1}'");
            $stmt2 = $this->pdo->query("SHOW TABLES LIKE '{$table2}'");
            
            $table1Exists = $stmt1->rowCount() > 0;
            $table2Exists = $stmt2->rowCount() > 0;

            if (!$table1Exists || !$table2Exists) {
                return [
                    'status' => 'FAILED',
                    'message' => "Tables missing: " . 
                                (!$table1Exists ? $table1 : '') . 
                                (!$table2Exists ? $table2 : '')
                ];
            }

            // Check required columns in main table
            $stmt = $this->pdo->query("DESCRIBE {$table1}");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $requiredColumns = ['id', 'filename', 'user_id', 'status'];
            
            $missingColumns = array_diff($requiredColumns, $columns);
            if (!empty($missingColumns)) {
                return [
                    'status' => 'FAILED',
                    'message' => "Missing columns in {$table1}: " . implode(', ', $missingColumns)
                ];
            }

            // Check required columns in versions table
            $stmt = $this->pdo->query("DESCRIBE {$table2}");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $requiredColumns = ['id', 'file_id', 'version_no', 'filename', 'file_path', 'datetime', 'file_size'];
            
            $missingColumns = array_diff($requiredColumns, $columns);
            if (!empty($missingColumns)) {
                return [
                    'status' => 'FAILED',
                    'message' => "Missing columns in {$table2}: " . implode(', ', $missingColumns)
                ];
            }

            echo "✅ Table structure validation passed\n";
            return ['status' => 'PASSED', 'message' => 'Table structure is valid'];

        } catch (Exception $e) {
            return ['status' => 'FAILED', 'message' => 'Table structure error: ' . $e->getMessage()];
        }
    }

    /**
     * Test 2: Create operation
     */
    private function testCreateOperation($table1, $table2) {
        try {
            // Insert test record into main table
            $stmt = $this->pdo->prepare("
                INSERT INTO {$table1} (filename, user_id, status) 
                VALUES (:filename, :user_id, :status)
            ");
            
            $filename = 'test_file_' . time() . '.pdf';
            $userId = 1; // Assuming user ID 1 exists
            $status = 'approve';
            
            $stmt->bindParam(':filename', $filename);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':status', $status);
            $stmt->execute();
            
            $fileId = $this->pdo->lastInsertId();

            // Insert test record into versions table
            $stmt = $this->pdo->prepare("
                INSERT INTO {$table2} (file_id, version_no, filename, file_path, datetime, file_size) 
                VALUES (:file_id, :version_no, :filename, :file_path, NOW(), :file_size)
            ");
            
            $versionNo = 1;
            $filePath = '/test/path/' . $filename;
            $fileSize = '1KB';
            
            $stmt->bindParam(':file_id', $fileId);
            $stmt->bindParam(':version_no', $versionNo);
            $stmt->bindParam(':filename', $filename);
            $stmt->bindParam(':file_path', $filePath);
            $stmt->bindParam(':file_size', $fileSize);
            $stmt->execute();

            echo "✅ Create operation passed\n";
            return [
                'status' => 'PASSED', 
                'message' => 'Create operation successful',
                'file_id' => $fileId
            ];

        } catch (Exception $e) {
            return ['status' => 'FAILED', 'message' => 'Create operation error: ' . $e->getMessage()];
        }
    }

    /**
     * Test 3: Read operation
     */
    private function testReadOperation($table1, $table2) {
        try {
            // Test reading from main table
            $stmt = $this->pdo->prepare("
                SELECT af.id, af.filename, af.user_id, ud.first_name, 
                       afv.version_no, ud.last_name, MAX(afv.version_no) AS latest_version, 
                       afv.datetime
                FROM {$table1} af
                LEFT JOIN {$table2} afv ON af.id = afv.file_id
                LEFT JOIN user_data ud ON af.user_id = ud.id_no
                WHERE af.status = 'approve'
                GROUP BY af.id, af.filename, af.user_id, ud.first_name, ud.last_name
            ");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "✅ Read operation passed\n";
            return [
                'status' => 'PASSED', 
                'message' => 'Read operation successful',
                'count' => count($results)
            ];

        } catch (Exception $e) {
            return ['status' => 'FAILED', 'message' => 'Read operation error: ' . $e->getMessage()];
        }
    }

    /**
     * Test 4: Update operation
     */
    private function testUpdateOperation($table1, $table2) {
        try {
            // Get a test record to update
            $stmt = $this->pdo->prepare("SELECT id FROM {$table1} LIMIT 1");
            $stmt->execute();
            $file = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$file) {
                return ['status' => 'SKIPPED', 'message' => 'No records to update'];
            }

            $fileId = $file['id'];
            $newFilename = 'updated_file_' . time() . '.pdf';

            // Update main table
            $stmt = $this->pdo->prepare("UPDATE {$table1} SET filename = :filename WHERE id = :id");
            $stmt->bindParam(':filename', $newFilename);
            $stmt->bindParam(':id', $fileId);
            $stmt->execute();

            // Update versions table
            $stmt = $this->pdo->prepare("
                UPDATE {$table2} 
                SET filename = :filename, datetime = NOW() 
                WHERE file_id = :file_id AND version_no = 1
            ");
            $stmt->bindParam(':filename', $newFilename);
            $stmt->bindParam(':file_id', $fileId);
            $stmt->execute();

            echo "✅ Update operation passed\n";
            return ['status' => 'PASSED', 'message' => 'Update operation successful'];

        } catch (Exception $e) {
            return ['status' => 'FAILED', 'message' => 'Update operation error: ' . $e->getMessage()];
        }
    }

    /**
     * Test 5: Delete operation
     */
    private function testDeleteOperation($table1, $table2) {
        try {
            // Get a test record to delete
            $stmt = $this->pdo->prepare("SELECT id FROM {$table1} WHERE filename LIKE 'test_file_%' LIMIT 1");
            $stmt->execute();
            $file = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$file) {
                return ['status' => 'SKIPPED', 'message' => 'No test records to delete'];
            }

            $fileId = $file['id'];

            // Delete from versions table first (foreign key constraint)
            $stmt = $this->pdo->prepare("DELETE FROM {$table2} WHERE file_id = :file_id");
            $stmt->bindParam(':file_id', $fileId);
            $stmt->execute();

            // Delete from main table
            $stmt = $this->pdo->prepare("DELETE FROM {$table1} WHERE id = :id");
            $stmt->bindParam(':id', $fileId);
            $stmt->execute();

            echo "✅ Delete operation passed\n";
            return ['status' => 'PASSED', 'message' => 'Delete operation successful'];

        } catch (Exception $e) {
            return ['status' => 'FAILED', 'message' => 'Delete operation error: ' . $e->getMessage()];
        }
    }

    /**
     * Test 6: File operations function
     */
    private function testFileOperationsFunction($table1) {
        try {
            $result = setFileTableVariables($table1, $this->testFile);
            
            $requiredKeys = ['uploadDir', 'uploadDir2', 'file_path', 'download_path', 'table2'];
            $missingKeys = array_diff($requiredKeys, array_keys($result));
            
            if (!empty($missingKeys)) {
                return [
                    'status' => 'FAILED', 
                    'message' => 'Missing keys in setFileTableVariables: ' . implode(', ', $missingKeys)
                ];
            }

            echo "✅ File operations function passed\n";
            return ['status' => 'PASSED', 'message' => 'File operations function working correctly'];

        } catch (Exception $e) {
            return ['status' => 'FAILED', 'message' => 'File operations function error: ' . $e->getMessage()];
        }
    }

    /**
     * Generate comprehensive test report
     */
    private function generateReport() {
        echo "\n📊 CRUD Operations Test Report\n";
        echo "=============================\n\n";

        $totalTests = 0;
        $passedTests = 0;
        $failedTests = 0;
        $skippedTests = 0;

        foreach ($this->testResults as $category => $results) {
            echo "📁 {$category}:\n";
            
            foreach ($results['tests'] as $testName => $testResult) {
                $status = $testResult['status'];
                $message = $testResult['message'];
                
                $totalTests++;
                switch ($status) {
                    case 'PASSED':
                        $passedTests++;
                        echo "  ✅ {$testName}: {$message}\n";
                        break;
                    case 'FAILED':
                        $failedTests++;
                        echo "  ❌ {$testName}: {$message}\n";
                        break;
                    case 'SKIPPED':
                        $skippedTests++;
                        echo "  ⚠️  {$testName}: {$message}\n";
                        break;
                }
            }
            echo "\n";
        }

        echo "📈 Summary:\n";
        echo "  Total Tests: {$totalTests}\n";
        echo "  Passed: {$passedTests}\n";
        echo "  Failed: {$failedTests}\n";
        echo "  Skipped: {$skippedTests}\n";
        echo "  Success Rate: " . round(($passedTests / $totalTests) * 100, 2) . "%\n\n";

        if ($failedTests > 0) {
            echo "🚨 Issues Found:\n";
            foreach ($this->testResults as $category => $results) {
                foreach ($results['tests'] as $testName => $testResult) {
                    if ($testResult['status'] === 'FAILED') {
                        echo "  - {$category} > {$testName}: {$testResult['message']}\n";
                    }
                }
            }
        } else {
            echo "🎉 All CRUD operations are working correctly across all file categories!\n";
        }
    }
}

// Run the tests
try {
    $test = new FileCategoryCRUDTest($pdo);
    $test->runAllTests();
} catch (Exception $e) {
    echo "❌ Test execution failed: " . $e->getMessage() . "\n";
}
?> 