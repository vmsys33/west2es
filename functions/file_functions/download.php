<?php
// Ensure that file_path is provided in the URL
if (isset($_GET['file_path'])) {
    $filePath = $_GET['file_path'];

    // Build the absolute file path
    $fileAbsolutePath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . ltrim($filePath, DIRECTORY_SEPARATOR);

    // Check if the file exists
    if (!file_exists($fileAbsolutePath)) {
        die('<div class="alert alert-warning text-center" role="alert">File not found on the server.</div>');
    }

    // Set headers to force download
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Content-Length: ' . filesize($fileAbsolutePath));
    header('Pragma: no-cache');
    header('Cache-Control: private, must-revalidate');
    header('Expires: 0');

    // Read the file and send it to the output buffer
    readfile($fileAbsolutePath);
    exit;
} else {
    die('<div class="alert alert-warning text-center" role="alert">File path not provided.</div>');
}
