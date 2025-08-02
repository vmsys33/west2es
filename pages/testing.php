<?php

require_once '../functions/db_connection.php';
$stmt = $pdo->prepare("SELECT id, filename FROM admin_files WHERE LOWER(filename) LIKE LOWER(:query) AND user_id = :user_id");
$stmt->execute([
    'query' => '%test%', // Replace with test data
    'user_id' => 1       // Replace with a valid user_id
]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($results);
?>