<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = trim($_GET['query']); 
    $query = "%$query%"; 

    try {
        // Prepare SQL to search across multiple tables
        $stmt = $pdo->prepare("
            SELECT 'admin_files' AS source_table, id, filename
            FROM admin_files
            WHERE LOWER(filename) LIKE LOWER(:query1)
            UNION ALL
            SELECT 'aeld_files' AS source_table, id, filename
            FROM aeld_files
            WHERE LOWER(filename) LIKE LOWER(:query2)
            LIMIT 108
        ");

        // Bind the value separately for each placeholder
        $stmt->execute(['query1' => $query, 'query2' => $query]); 

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($results) > 0) {
            echo json_encode(['status' => 'success', 'data' => $results]);
        } else {
            echo json_encode(['status' => 'no_results', 'message' => 'No files found.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>