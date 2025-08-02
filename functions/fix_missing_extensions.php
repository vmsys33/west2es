<?php
// Run this script manually to fix missing file extensions in your database and on disk
require_once __DIR__ . '/db_connection.php';

$tables = [
    'admin_files', 'aeld_files', 'cild_files', 'if_completed_files', 'if_proposals_files', 'lulr_files',
    'rp_completed_berf_files', 'rp_completed_nonberf_files', 'rp_proposal_berf_files', 'rp_proposal_nonberf_files',
    't_lr_files', 't_pp_files', 't_rs_files'
];

$uploadBase = $_SERVER['DOCUMENT_ROOT'] . '/west2es/uploads/files/';

foreach ($tables as $table) {
    echo "Checking table: $table\n";
    $stmt = $pdo->query("SELECT id, filename FROM $table");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $filename = $row['filename'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (empty($ext)) {
            // Try to find the file on disk with a known extension
            $dir = $uploadBase . $table . '/';
            $base = pathinfo($filename, PATHINFO_FILENAME);
            $found = false;
            foreach (['pdf','docx','doc','xlsx','xls','pptx','ppt'] as $tryExt) {
                $tryFile = $dir . $base . '.' . $tryExt;
                if (file_exists($tryFile)) {
                    $newFilename = $base . '.' . $tryExt;
                    // Update DB
                    $update = $pdo->prepare("UPDATE $table SET filename = ? WHERE id = ?");
                    $update->execute([$newFilename, $id]);
                    // Optionally rename the file on disk (if needed)
                    if ($filename !== $newFilename && file_exists($dir . $filename)) {
                        rename($dir . $filename, $tryFile);
                    }
                    echo "Fixed: $filename -> $newFilename in $table (id $id)\n";
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                echo "Could not fix: $filename in $table (id $id)\n";
            }
        }
    }
}
echo "Done.\n";