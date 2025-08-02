<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query']) && isset($_GET['user_id'])) {
    $query = trim($_GET['query']);
    $user_id = (int)$_GET['user_id']; // Cast user_id to integer for security

    // Validate query and user_id
    if (empty($query)) {
        echo json_encode(['status' => 'error', 'message' => 'Query cannot be empty.']);
        exit;
    }

    // Use query% to prioritize results starting with the query
    $query = "$query%";

    try {
        // Simplified SQL for debugging
        $stmt = $pdo->prepare("
            SELECT id, filename
            FROM admin_files
            WHERE LOWER(filename) LIKE LOWER(:query) AND user_id = :user_id
        ");

        // Bind parameters explicitly
        $stmt->bindValue(':query', $query, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($results)) {
            echo json_encode(['status' => 'success', 'data' => $results]);
        } else {
            echo json_encode(['status' => 'no_results', 'message' => 'No files found.']);
        }
    } catch (PDOException $e) {
        // Log and return the error
        error_log("Error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
