<?php

require_once 'db_connection.php';

header('Content-Type: application/json');

// Define the allowed tables and their respective columns
$allowedTables = [
    'admin_files_versions' => ['id', 'filename', 'version_no', 'datetime', 'file_size'],
    'cild_files_versions' => ['id', 'filename', 'version_no', 'datetime', 'file_size'],
    // ... other tables
];

$resultData = [];

try {
    foreach ($allowedTables as $table => $columns) {
        // Check if the table contains a `status` column
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE 'status'");
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // If the `status` column exists, fetch data with status 'pending'
            $columnsList = implode(', ', $columns);
            $query = "SELECT $columnsList FROM `$table` WHERE `status` = :status";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['status' => 'pending']);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Add the results to the output array
            if (!empty($data)) {
                $resultData[$table] = $data;
            }
        } else {
            // Log an error for the table if it does not have a 'status' column
            $resultData[$table] = [
                'status' => 'error',
                'message' => "Table '$table' does not have a 'status' column."
            ];
        }
    }

    // Return consolidated results
    echo json_encode($resultData);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}

?>