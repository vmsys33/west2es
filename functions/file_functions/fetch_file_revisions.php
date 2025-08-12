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

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Validate required parameters
    if (!isset($_GET['file_id']) || !isset($_GET['file_table'])) {
        echo json_encode(['status' => 'error', 'message' => 'File ID and File Table are required']);
        exit;
    }

    // Sanitize and validate inputs
    $fileId = filter_var($_GET['file_id'], FILTER_SANITIZE_NUMBER_INT);
    $fileTable = htmlspecialchars($_GET['file_table'], ENT_QUOTES, 'UTF-8');

    // Validate file ID
    if (!$fileId || $fileId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file ID']);
        exit;
    }

    // Define allowed tables to prevent SQL injection
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
        echo json_encode(['status' => 'error', 'message' => 'Invalid table name']);
        exit;
    }
    
    try {
        // Verify the file exists in the main table
        $mainTable = str_replace('_versions', '', $fileTable);
        $stmtCheckFile = $pdo->prepare("SELECT id FROM $mainTable WHERE id = :file_id");
        $stmtCheckFile->bindParam(':file_id', $fileId, PDO::PARAM_INT);
        $stmtCheckFile->execute();
        
        if (!$stmtCheckFile->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'File not found']);
            exit;
        }

        // Fetch revisions from the specified table (without file_path column)
        $stmt = $pdo->prepare("SELECT id, filename, version_no, file_id, datetime, file_size 
                               FROM $fileTable 
                               WHERE file_id = :file_id 
                               ORDER BY version_no DESC");
        $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
        $stmt->execute();
        $revisions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($revisions) {
            // Validate file paths exist and construct file_path
            foreach ($revisions as &$revision) {
                // Construct file_path based on table name and filename
                $revision['file_path'] = "uploads/files/{$mainTable}/{$revision['filename']}";
                
                $physicalPath = $_SERVER['DOCUMENT_ROOT'] . '/west2es/' . $revision['file_path'];
                $revision['file_exists'] = file_exists($physicalPath);
                
                // Format datetime for better readability
                if ($revision['datetime']) {
                    $revision['formatted_datetime'] = date('F j, Y, h:i A', strtotime($revision['datetime']));
                }
            }

            echo json_encode([
                'status' => 'success', 
                'data' => $revisions,
                'total_revisions' => count($revisions)
            ]);
        } else {
            echo json_encode([
                'status' => 'success', 
                'data' => [],
                'message' => 'No revisions found for this file',
                'total_revisions' => 0
            ]);
        }
    } catch (Exception $e) {
        error_log("Error in fetch_file_revisions.php: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
