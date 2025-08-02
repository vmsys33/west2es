<?php
require_once 'db_connection.php';
header('Content-Type: application/json');

try {
    // Query to fetch faculty data
    $stmt = $pdo->prepare("
        SELECT id_no, deped_id_no, last_name, first_name, middle_name, status, email 
        FROM user_data 
        WHERE role = 'faculty'
        ORDER BY last_name ASC
    ");
    $stmt->execute();

    $faculty = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $faculty]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch faculty: ' . $e->getMessage()]);
}
?>
