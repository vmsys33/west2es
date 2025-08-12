<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    try {
        // Start transaction for data consistency
        $pdo->beginTransaction();

        // Fetch the details of the pending file
        $stmt = $pdo->prepare("SELECT * FROM pending_files WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $pendingFile = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pendingFile) {
            // Validate table1 and table2 fields
            if (empty($pendingFile['table1']) || empty($pendingFile['table2'])) {
                throw new Exception('Missing table1 or table2 value in pending_files');
            }

            $versionNo = intval($pendingFile['version_no']); // Convert version_no to an integer

            if ($status === 'approve') {
                if ($versionNo === 1) {
                    // Insert into table1 only if version_no is 1
                    $stmtTable1 = $pdo->prepare("INSERT INTO {$pendingFile['table1']} (filename, user_id) VALUES (:filename, :user_id)");
                    $stmtTable1->bindParam(':filename', $pendingFile['name'], PDO::PARAM_STR);
                    $stmtTable1->bindParam(':user_id', $pendingFile['user_id'], PDO::PARAM_INT);
                    $stmtTable1->execute();
                    $fileId = $pdo->lastInsertId();

                    // Insert into table2 with full details
                    $stmtTable2 = $pdo->prepare("INSERT INTO {$pendingFile['table2']} 
                        (file_id, version_no, file_path, datetime, file_size, filename)
                        VALUES (:file_id, :version_no, :file_path, :datetime, :file_size, :filename)");
                    $stmtTable2->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                    $stmtTable2->bindParam(':version_no', $pendingFile['version_no'], PDO::PARAM_STR);
                    $stmtTable2->bindParam(':file_path', $pendingFile['file_path'], PDO::PARAM_STR);
                    $stmtTable2->bindParam(':datetime', $pendingFile['datetime'], PDO::PARAM_STR);
                    $stmtTable2->bindParam(':file_size', $pendingFile['file_size'], PDO::PARAM_STR);
                    $stmtTable2->bindParam(':filename', $pendingFile['filename'], PDO::PARAM_STR);
                    $stmtTable2->execute();

                    // Insert into master_files when version_no is 1
                    $stmtMaster = $pdo->prepare("INSERT INTO master_files 
                        (file_id, name, filename, user_id, version_no, file_path, datetime, file_size, table1, table2, download_path) 
                        VALUES (:file_id, :name, :filename, :user_id, :version_no, :file_path, :datetime, :file_size, :table1, :table2, :download_path)");
                    $stmtMaster->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                    $stmtMaster->bindParam(':name', $pendingFile['name'], PDO::PARAM_STR);
                    $stmtMaster->bindParam(':filename', $pendingFile['filename'], PDO::PARAM_STR);
                    $stmtMaster->bindParam(':user_id', $pendingFile['user_id'], PDO::PARAM_INT);
                    $stmtMaster->bindParam(':version_no', $pendingFile['version_no'], PDO::PARAM_STR);
                    $stmtMaster->bindParam(':file_path', $pendingFile['file_path'], PDO::PARAM_STR);
                    $stmtMaster->bindParam(':datetime', $pendingFile['datetime'], PDO::PARAM_STR);
                    $stmtMaster->bindParam(':file_size', $pendingFile['file_size'], PDO::PARAM_STR);
                    $stmtMaster->bindParam(':table1', $pendingFile['table1'], PDO::PARAM_STR);
                    $stmtMaster->bindParam(':table2', $pendingFile['table2'], PDO::PARAM_STR);
                    $stmtMaster->bindParam(':download_path', $pendingFile['download_path'], PDO::PARAM_STR);
                    $stmtMaster->execute();
                } else {
                    // Insert into table2 with full details for revisions
                    $stmtTable2 = $pdo->prepare("INSERT INTO {$pendingFile['table2']} 
                        (file_id, version_no, file_path, datetime, file_size, filename)
                        VALUES (:file_id, :version_no, :file_path, :datetime, :file_size, :filename)");
                    $stmtTable2->bindParam(':file_id', $pendingFile['file_id'], PDO::PARAM_INT);
                    $stmtTable2->bindParam(':version_no', $pendingFile['version_no'], PDO::PARAM_STR);
                    $stmtTable2->bindParam(':file_path', $pendingFile['file_path'], PDO::PARAM_STR);
                    $stmtTable2->bindParam(':datetime', $pendingFile['datetime'], PDO::PARAM_STR);
                    $stmtTable2->bindParam(':file_size', $pendingFile['file_size'], PDO::PARAM_STR);
                    $stmtTable2->bindParam(':filename', $pendingFile['filename'], PDO::PARAM_STR);
                    $stmtTable2->execute();

                    // Insert into master_files for other versions
                    $stmtMaster = $pdo->prepare("INSERT INTO master_files 
                        (name, filename, user_id, version_no, file_path, datetime, file_size, table1, table2, download_path, file_id) 
                        VALUES (:name, :filename, :user_id, :version_no, :file_path, :datetime, :file_size, :table1, :table2, :download_path, :file_id)");
                    $stmtMaster->bindParam(':name', $pendingFile['name'], PDO::PARAM_STR);
                    $stmtMaster->bindParam(':filename', $pendingFile['filename'], PDO::PARAM_STR);
                    $stmtMaster->bindParam(':user_id', $pendingFile['user_id'], PDO::PARAM_INT);
                    $stmtMaster->bindParam(':version_no', $pendingFile['version_no'], PDO::PARAM_STR);
                    $stmtMaster->bindParam(':file_path', $pendingFile['file_path'], PDO::PARAM_STR);
                    $stmtMaster->bindParam(':datetime', $pendingFile['datetime'], PDO::PARAM_STR);
                    $stmtMaster->bindParam(':file_size', $pendingFile['file_size'], PDO::PARAM_STR);
                    $stmtMaster->bindParam(':table1', $pendingFile['table1'], PDO::PARAM_STR);
                    $stmtMaster->bindParam(':table2', $pendingFile['table2'], PDO::PARAM_STR);
                    $stmtMaster->bindParam(':download_path', $pendingFile['download_path'], PDO::PARAM_STR);
                    $stmtMaster->bindParam(':file_id', $pendingFile['file_id'], PDO::PARAM_INT);
                    $stmtMaster->execute();
                }

                // Log the approval action (optional)
                try {
                    $approverId = $_SESSION['user_id'] ?? 0;
                    $approverName = ($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '');
                    $logStmt = $pdo->prepare("INSERT INTO file_approval_logs 
                        (pending_file_id, file_name, approved_by, approved_by_name, approval_date, target_table1, target_table2) 
                        VALUES (?, ?, ?, ?, NOW(), ?, ?)");
                    $logStmt->execute([
                        $id, 
                        $pendingFile['name'], 
                        $approverId, 
                        $approverName,
                        $pendingFile['table1'],
                        $pendingFile['table2']
                    ]);
                } catch (Exception $e) {
                    // Log error but don't fail the approval process
                    error_log("Failed to log approval: " . $e->getMessage());
                }
            }

            // ONLY DELETE FROM PENDING_FILES AFTER SUCCESSFUL INSERTION
            $deleteStmt = $pdo->prepare("DELETE FROM pending_files WHERE id = :id");
            $deleteStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteStmt->execute();

            // Commit the transaction
            $pdo->commit();

            echo json_encode(['success' => true, 'message' => 'File approved successfully']);
        } else {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'File not found']);
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("File approval error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
