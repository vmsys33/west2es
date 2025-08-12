<?php
/**
 * Script: scripts/auto_add_admin_files.php
 * Purpose: Automatically processes any new files found in "incoming_admin_files/" by
 *          copying them into "uploads/files/admin_files/" and inserting a matching
 *          pending row into the database, effectively performing the same actions
 *          as the Add-File feature in pages/content.php.
 *
 * How to run (CLI):
 *      php scripts/auto_add_admin_files.php
 *
 * Optionally you can pass a user-id (the uploader) as the first CLI argument:
 *      php scripts/auto_add_admin_files.php 3
 *
 * Requirements:
 *  • The folder   incoming_admin_files/   must exist at the project root (same
 *    level as uploads/ and functions/). This acts as the “inbox” folder – drop
 *    any new files there and the script will pick them up.
 *  • The script re-uses existing helper functions (setFileTableVariables, logNotification, etc.)
 *  • Duplicate detection logic mirrors that in functions/file_functions/add_file.php
 */

// --- bootstrap -------------------------------------------------------------
require_once __DIR__ . '/../functions/db_connection.php';
require_once __DIR__ . '/../functions/notification_helper.php';
require_once __DIR__ . '/../functions/file_operations.php';

// ---------------------------------------------------------------------------
// Configuration – adjust if needed
// ---------------------------------------------------------------------------
$table1  = 'admin_files';              // hard-coded because this script is only for Administrative Files
$userId  = (int)($argv[1] ?? 1);      // default uploader user-id = 1 (admin) if not provided
$role    = 'system';                  // role used in notifications

// Folder that contains new files waiting to be processed
$incomingDir = __DIR__ . '/../incoming_admin_files/';

if (!is_dir($incomingDir)) {
    echo "[ERROR] Incoming directory not found: {$incomingDir}\n";
    exit(1);
}

// Scan for any regular files (skip . .. and sub-dirs)
$pendingFiles = array_filter(scandir($incomingDir), function ($item) use ($incomingDir) {
    return is_file($incomingDir . $item) && $item[0] !== '.'; // ignore hidden files
});

if (empty($pendingFiles)) {
    echo "[INFO] No new files to process.\n";
    exit(0);
}

try {
    // -----------------------------------------------------------------------
    // Begin single DB transaction for all files
    // -----------------------------------------------------------------------
    $pdo->beginTransaction();

    foreach ($pendingFiles as $fileNameOnDisk) {
        $sourcePath   = $incomingDir . $fileNameOnDisk;

        // Use the helper to derive target directories & DB table names
        $vars       = setFileTableVariables($table1, ['name' => $fileNameOnDisk]);
        $uploadDir  = $vars['uploadDir2'];      // relative path inside project
        $file_path  = $vars['file_path'];       // absolute path (for DB)
        $dl_path    = $vars['download_path'];
        $table2     = $vars['table2'];

        // -------------------------------------------------------------------
        // Duplicate checks (matching logic from add_file.php)
        // -------------------------------------------------------------------
        // Duplicate check in master_files using two separate placeholders to avoid HY093 error
        $stmt = $pdo->prepare("SELECT COUNT(*) AS duplicate_count FROM master_files WHERE filename = :fn1 OR name = :fn2");
        $stmt->execute([':fn1' => $fileNameOnDisk, ':fn2' => $fileNameOnDisk]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)['duplicate_count'] > 0) {
            echo "[SKIP] {$fileNameOnDisk} already exists in master_files.\n";
            continue;
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) AS duplicate_count FROM pending_files WHERE name = :fn1 OR filename = :fn2");
        $stmt->execute([':fn1' => $fileNameOnDisk, ':fn2' => $fileNameOnDisk]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)['duplicate_count'] > 0) {
            echo "[SKIP] {$fileNameOnDisk} already pending.\n";
            continue;
        }

        // -------------------------------------------------------------------
        // Ensure destination folder exists; copy file
        // -------------------------------------------------------------------
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $destPath = $uploadDir . basename($fileNameOnDisk);
        if (!copy($sourcePath, $destPath)) {
            echo "[ERROR] Failed to copy {$fileNameOnDisk} -> {$destPath}\n";
            continue;
        }

        // -------------------------------------------------------------------
        // Compute file size (human-readable like original code)
        // -------------------------------------------------------------------
        $bytes = filesize($destPath);
        if ($bytes < 1024) {
            $fileSize = $bytes . ' Bytes';
        } elseif ($bytes < 1024 * 1024) {
            $fileSize = round($bytes / 1024, 2) . ' KB';
        } else {
            $fileSize = round($bytes / (1024 * 1024), 2) . ' MB';
        }

        $currentDatetime = date('Y-m-d H:i:s');

        // -------------------------------------------------------------------
        // Insert into pending_files
        // -------------------------------------------------------------------
        $stmt = $pdo->prepare(
            "INSERT INTO pending_files (name, filename, user_id, version_no, file_path, download_path, datetime, file_size, table1, table2)\n             VALUES (:name, :filename, :user_id, 1, :file_path, :download_path, :datetime, :file_size, :table1, :table2)"
        );
        $stmt->execute([
            ':name'          => $fileNameOnDisk,
            ':filename'      => $fileNameOnDisk,
            ':user_id'       => $userId,
            ':file_path'     => $file_path,
            ':download_path' => $dl_path,
            ':datetime'      => $currentDatetime,
            ':file_size'     => $fileSize,
            ':table1'        => $table1,
            ':table2'        => $table2,
        ]);

        // -------------------------------------------------------------------
        // Log notification
        // -------------------------------------------------------------------
        logNotification($userId, $role, 'add', "Auto-imported admin file: {$fileNameOnDisk}");

        // After successful DB insert, remove original file from inbox
        unlink($sourcePath);

        echo "[OK]   {$fileNameOnDisk} imported.\n";
    }

    $pdo->commit();
    echo "\n[SUCCESS] All new files processed and committed.\n";
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "[FATAL] " . $e->getMessage() . "\n";
    exit(1);
}
