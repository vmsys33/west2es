<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = trim($_GET['query']);
    
    // If query is empty, respond with an error
    if (empty($query)) {
        echo json_encode(['status' => 'error', 'message' => 'Query cannot be empty.']);
        exit;
    }

    // Use query% to prioritize results starting with the query
    $query = "$query%";

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
            UNION ALL
            SELECT 'cild_files' AS source_table, id, filename
            FROM cild_files
            WHERE LOWER(filename) LIKE LOWER(:query3)
            UNION ALL
            SELECT 'if_proposal_files' AS source_table, id, filename
            FROM if_proposals_files
            WHERE LOWER(filename) LIKE LOWER(:query4)
            UNION ALL
            SELECT 'lulr_files' AS source_table, id, filename
            FROM lulr_files
            WHERE LOWER(filename) LIKE LOWER(:query5)
            UNION ALL
            SELECT 'rp_completed_berf_files' AS source_table, id, filename
            FROM rp_completed_berf_files
            WHERE LOWER(filename) LIKE LOWER(:query6)
            UNION ALL
            SELECT 'rp_completed_nonberf_files' AS source_table, id, filename
            FROM rp_completed_nonberf_files
            WHERE LOWER(filename) LIKE LOWER(:query7)
            UNION ALL
            SELECT 'rp_proposal_berf_files' AS source_table, id, filename
            FROM rp_proposal_berf_files
            WHERE LOWER(filename) LIKE LOWER(:query8)
            UNION ALL
            SELECT 't_lr_files' AS source_table, id, filename
            FROM t_lr_files
            WHERE LOWER(filename) LIKE LOWER(:query9)
            UNION ALL
            SELECT 't_pp_files' AS source_table, id, filename
            FROM t_pp_files
            WHERE LOWER(filename) LIKE LOWER(:query10)
            UNION ALL
            SELECT 't_rs_files' AS source_table, id, filename
            FROM t_rs_files
            WHERE LOWER(filename) LIKE LOWER(:query11)
            UNION ALL
            SELECT 'rp_proposal_nonberf_files' AS source_table, id, filename
            FROM rp_proposal_nonberf_files
            WHERE LOWER(filename) LIKE LOWER(:query12)
            ORDER BY filename ASC
            LIMIT 108
        ");

        // Bind the value separately for each placeholder
        $stmt->execute([
            'query1' => $query,
            'query2' => $query,
            'query3' => $query,
            'query4' => $query,
            'query5' => $query,
            'query6' => $query,
            'query7' => $query,
            'query8' => $query,
            'query9' => $query,
            'query10' => $query,
            'query11' => $query,
            'query12' => $query
        ]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($results)) {
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
