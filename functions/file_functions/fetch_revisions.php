<?php
// Fix the path to work from different locations
$dbPath = __DIR__ . '/../db_connection.php';
if (file_exists($dbPath)) {
    require_once $dbPath;
} else {
    // Try alternative path
    require_once __DIR__ . '/../../functions/db_connection.php';
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['file_id']) || !isset($_GET['file_table'])) {
        echo json_encode(['status' => 'error', 'message' => 'File ID and Table name are required.']);
        exit;
    }

    $fileId = filter_var($_GET['file_id'], FILTER_SANITIZE_NUMBER_INT);
    $fileTable = htmlspecialchars($_GET['file_table'], ENT_QUOTES, 'UTF-8');

    // Define allowed tables
    $allowedTables = [
        'admin_files_versions',
        'aeld_files_versions',
        'cild_files_versions',
        'if_completed_files_versions',
        'if_proposals_files_versions',
        'lulr_files_versions',
        'rp_completed_berf_files_versions',
        'rp_completed_nonberf_files_versions',
        'rp_proposal_berf_files_versions',
        'rp_proposal_nonberf_files_versions',
        't_lr_files_versions',
        't_pp_files_versions',
        't_rs_files_versions',
        'approved_proposal_versions'
    ];

    // Validate table name
    if (!in_array($fileTable, $allowedTables)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid table name.']);
        exit;
    }

    try {
        // Fetch revisions from the dynamically specified table (without file_path column)
        $stmt = $pdo->prepare("SELECT version_no, filename, datetime, file_size, file_id 
                               FROM $fileTable 
                               WHERE file_id = :file_id 
                               ORDER BY version_no DESC");
        $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
        $stmt->execute();

        $revisions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($revisions) {
            // Add the table name and construct file_path for each record
            foreach ($revisions as &$revision) {
                $revision['table_name'] = $fileTable;
                
                // Construct file_path based on table name and filename
                $mainTable = str_replace('_versions', '', $fileTable);
                $revision['file_path'] = "uploads/files/{$mainTable}/{$revision['filename']}";
                $revision['download_path'] = $revision['file_path']; // For download functionality
            }

            echo json_encode(['status' => 'success', 'data' => $revisions]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No revisions found for this file.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
