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
        // SQL Query with unique placeholders for each UNION ALL section
        $stmt = $pdo->prepare("
            SELECT 'admin_files' AS source_table, id, filename
            FROM admin_files
            WHERE LOWER(filename) LIKE LOWER(:query1) AND user_id = :user_id1
            UNION ALL
            SELECT 'aeld_files' AS source_table, id, filename
            FROM aeld_files
            WHERE LOWER(filename) LIKE LOWER(:query2) AND user_id = :user_id2
            UNION ALL
            SELECT 'cild_files' AS source_table, id, filename
            FROM cild_files
            WHERE LOWER(filename) LIKE LOWER(:query3) AND user_id = :user_id3
            UNION ALL
            SELECT 'if_proposals_files' AS source_table, id, filename
            FROM if_proposals_files
            WHERE LOWER(filename) LIKE LOWER(:query4) AND user_id = :user_id4
            UNION ALL
            SELECT 'lulr_files' AS source_table, id, filename
            FROM lulr_files
            WHERE LOWER(filename) LIKE LOWER(:query5) AND user_id = :user_id5
            UNION ALL
            SELECT 'rp_completed_berf_files' AS source_table, id, filename
            FROM rp_completed_berf_files
            WHERE LOWER(filename) LIKE LOWER(:query6) AND user_id = :user_id6
            UNION ALL
            SELECT 'rp_completed_nonberf_files' AS source_table, id, filename
            FROM rp_completed_nonberf_files
            WHERE LOWER(filename) LIKE LOWER(:query7) AND user_id = :user_id7
            UNION ALL
            SELECT 'rp_proposal_berf_files' AS source_table, id, filename
            FROM rp_proposal_berf_files
            WHERE LOWER(filename) LIKE LOWER(:query8) AND user_id = :user_id8
            UNION ALL
            SELECT 't_lr_files' AS source_table, id, filename
            FROM t_lr_files
            WHERE LOWER(filename) LIKE LOWER(:query9) AND user_id = :user_id9
            UNION ALL
            SELECT 't_pp_files' AS source_table, id, filename
            FROM t_pp_files
            WHERE LOWER(filename) LIKE LOWER(:query10) AND user_id = :user_id10
            UNION ALL
            SELECT 't_rs_files' AS source_table, id, filename
            FROM t_rs_files
            WHERE LOWER(filename) LIKE LOWER(:query11) AND user_id = :user_id11
            ORDER BY filename ASC
            LIMIT 108
        ");

        // Bind parameters for each placeholder
        $params = [];
        for ($i = 1; $i <= 11; $i++) {
            $params["query$i"] = $query;
            $params["user_id$i"] = $user_id;
        }

        // Execute the query
        $stmt->execute($params);

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
