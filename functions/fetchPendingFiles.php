<?php
require_once 'db_connection.php'; // Include database connection

function fetchPendingFiles() {
    global $pdo; // Ensure $pdo is accessible

    $stmt = $pdo->prepare("SELECT id, name, filename, version_no, datetime, file_size, file_path, status FROM pending_files WHERE status = 'pending'");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($result);
}

fetchPendingFiles();
?>
