<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['file'])) {
    $filePath = $_GET['file'];

    // Base directory for allowed files
    $allowedDirectory = realpath($_SERVER['DOCUMENT_ROOT'] . '/uploads/files/admin_files/'); // Adjust path as needed

    // Sanitize and validate the file path
    $realPath = realpath($filePath);

    // Check if the file exists and is within the allowed directory
    if ($realPath && strpos($realPath, $allowedDirectory) === 0 && file_exists($realPath)) {
        // Set headers for file download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($realPath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($realPath));
        
        // Read the file and output its content
        readfile($realPath);
        exit;
    } else {
        http_response_code(403); // Forbidden
        echo "Access denied or file does not exist.";
        exit;
    }
} else {
    http_response_code(400); // Bad request
    echo "Invalid request.";
}

?>
