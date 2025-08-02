<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    try {
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

            // Update the status of the pending file
            $updateStmt = $pdo->prepare("UPDATE pending_files SET status = :status WHERE id = :id");
            $updateStmt->bindParam(':status', $status, PDO::PARAM_STR);
            $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $updateStmt->execute();

            $versionNo = intval($pendingFile['version_no']); // Convert version_no to an integer

            if ($status === 'approve') {
                if ($versionNo === 1) {
                    // Insert into table1 only if version_no is 1
                    $stmtTable1 = $pdo->prepare("INSERT INTO {$pendingFile['table1']} (filename, user_id, status) VALUES (:filename, :user_id, :status)");
                    $stmtTable1->bindParam(':filename', $pendingFile['name'], PDO::PARAM_STR);
                    $stmtTable1->bindParam(':user_id', $pendingFile['user_id'], PDO::PARAM_INT);
                    $stmtTable1->bindParam(':status', $status, PDO::PARAM_STR); // Adding approve status
                    $stmtTable1->execute();
                    $fileId = $pdo->lastInsertId();

                    // Insert into table2 with full details
                    $stmtTable2 = $pdo->prepare("INSERT INTO {$pendingFile['table2']} 
                        (file_id, version_no, download_path, file_path, datetime, file_size, name, filename, status)
                        VALUES (:file_id, :version_no, :download_path, :file_path, :datetime, :file_size, :name, :filename, :status)");
                    $stmtTable2->bindParam(':file_id', $fileId, PDO::PARAM_INT);
                    $stmtTable2->bindParam(':version_no', $pendingFile['version_no'], PDO::PARAM_STR);
                    $stmtTable2->bindParam(':download_path', $pendingFile['download_path'], PDO::PARAM_STR);
                    $stmtTable2->bindParam(':file_path', $pendingFile['file_path'], PDO::PARAM_STR);
                    $stmtTable2->bindParam(':datetime', $pendingFile['datetime'], PDO::PARAM_STR);
                    $stmtTable2->bindParam(':file_size', $pendingFile['file_size'], PDO::PARAM_STR);
                    $stmtTable2->bindParam(':name', $pendingFile['name'], PDO::PARAM_STR);
                    $stmtTable2->bindParam(':filename', $pendingFile['filename'], PDO::PARAM_STR);
                    $stmtTable2->bindParam(':status', $status, PDO::PARAM_STR); // Adding approve status
                    $stmtTable2->execute();

                    // Save data in file_preview_mapping
                    $stmtMapping = $pdo->prepare("INSERT INTO file_preview_mapping (filename, table_name, record_id, created_at) 
                        VALUES (:filename, :table_name, :record_id, NOW())");
                    $stmtMapping->bindParam(':filename', $pendingFile['file_path'], PDO::PARAM_STR);
                    $stmtMapping->bindParam(':table_name', $pendingFile['table2'], PDO::PARAM_STR);
                    $stmtMapping->bindParam(':record_id', $fileId, PDO::PARAM_INT);
                    $stmtMapping->execute();
                } else {
                    // Update the status in table2 if version_no > 1
                    $updateTable2 = $pdo->prepare("UPDATE {$pendingFile['table2']} 
                        SET status = :status 
                        WHERE file_id = :file_id AND version_no = :version_no");
                    $updateTable2->bindParam(':status', $status, PDO::PARAM_STR);
                    $updateTable2->bindParam(':file_id', $pendingFile['file_id'], PDO::PARAM_INT);
                    $updateTable2->bindParam(':version_no', $pendingFile['version_no'], PDO::PARAM_STR);
                    $updateTable2->execute();
                }
            }

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'File not found']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
