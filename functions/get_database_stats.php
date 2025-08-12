<?php
/**
 * Get Database Statistics
 * Returns statistics about files, versions, and storage usage
 */

require_once 'db_connection.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Admin privileges required']);
    exit;
}

try {
    $stats = [];
    
    // Define all file tables
    $fileTables = [
        'admin_files',
        'aeld_files', 
        'cild_files',
        'if_completed_files',
        'if_proposals_files',
        'lulr_files',
        'rp_completed_berf_files',
        'rp_completed_nonberf_files',
        'rp_proposal_berf_files',
        'rp_proposal_nonberf_files',
        't_lr_files',
        't_pp_files',
        't_rs_files',
        'approved_proposal'
    ];
    
    $versionTables = [
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
    
    // Count total files
    $totalFiles = 0;
    foreach ($fileTables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalFiles += $result['count'];
        } catch (Exception $e) {
            // Table might not exist, continue
        }
    }
    
    // Count total versions
    $totalVersions = 0;
    foreach ($versionTables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalVersions += $result['count'];
        } catch (Exception $e) {
            // Table might not exist, continue
        }
    }
    
    // Count pending files
    $pendingFiles = 0;
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM pending_files");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $pendingFiles = $result['count'];
    } catch (Exception $e) {
        // Table might not exist
    }
    
    // Calculate total file size
    $totalSize = 0;
    $totalSizeFormatted = '0 B';
    
    try {
        // Get file sizes from version tables
        $sizeQuery = "SELECT file_size FROM (";
        $unionQueries = [];
        
        foreach ($versionTables as $table) {
            $unionQueries[] = "SELECT file_size FROM $table WHERE file_size IS NOT NULL AND file_size != ''";
        }
        
        if (!empty($unionQueries)) {
            $sizeQuery .= implode(" UNION ALL ", $unionQueries) . ") as combined_sizes";
            
            $stmt = $pdo->query($sizeQuery);
            $fileSizes = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($fileSizes as $sizeStr) {
                // Parse file size string (e.g., "1.5 MB", "256 KB", etc.)
                if (preg_match('/(\d+(?:\.\d+)?)\s*(B|KB|MB|GB)/i', $sizeStr, $matches)) {
                    $size = floatval($matches[1]);
                    $unit = strtoupper($matches[2]);
                    
                    switch ($unit) {
                        case 'B':
                            $totalSize += $size;
                            break;
                        case 'KB':
                            $totalSize += $size * 1024;
                            break;
                        case 'MB':
                            $totalSize += $size * 1024 * 1024;
                            break;
                        case 'GB':
                            $totalSize += $size * 1024 * 1024 * 1024;
                            break;
                    }
                }
            }
        }
        
        // Format total size
        if ($totalSize < 1024) {
            $totalSizeFormatted = round($totalSize, 2) . ' B';
        } elseif ($totalSize < 1024 * 1024) {
            $totalSizeFormatted = round($totalSize / 1024, 2) . ' KB';
        } elseif ($totalSize < 1024 * 1024 * 1024) {
            $totalSizeFormatted = round($totalSize / (1024 * 1024), 2) . ' MB';
        } else {
            $totalSizeFormatted = round($totalSize / (1024 * 1024 * 1024), 2) . ' GB';
        }
        
    } catch (Exception $e) {
        // Error calculating size, use default
    }
    
    // Get table-specific counts
    $tableCounts = [];
    foreach ($fileTables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $tableCounts[$table] = $result['count'];
        } catch (Exception $e) {
            $tableCounts[$table] = 0;
        }
    }
    
    $stats = [
        'total_files' => $totalFiles,
        'total_versions' => $totalVersions,
        'pending_files' => $pendingFiles,
        'total_size' => $totalSizeFormatted,
        'total_size_bytes' => $totalSize,
        'table_counts' => $tableCounts,
        'last_updated' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode([
        'status' => 'success',
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    error_log("Database stats error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to get database statistics: ' . $e->getMessage()
    ]);
}
?>
