<?php
require_once '../db_connection.php';

try {
    $stmt = $pdo->query("SELECT f.id, f.filename, f.datetime, f.file_size, u.username AS uploader 
                         FROM admin_files f
                         LEFT JOIN users u ON f.user_id = u.id");
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $files]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch files: ' . $e->getMessage()]);
}
