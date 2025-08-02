<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['file'])) {
    // Define the base directory for allowed file access
    $allowedBaseDirectory = realpath($_SERVER['DOCUMENT_ROOT'] . '/west2es/uploads/');
    $filePath = $_GET['file'];

    // Normalize the requested file path
    $realPath = realpath($allowedBaseDirectory . '/' . $filePath);

    // Debugging output (optional for testing)
    echo "Allowed Base Directory: " . $allowedBaseDirectory . "<br>";
    echo "Requested File Path: " . $filePath . "<br>";
    echo "Resolved Real Path: " . $realPath . "<br>";

    // Validate the resolved path
    if ($realPath && strpos($realPath, $allowedBaseDirectory) === 0 && file_exists($realPath)) {
        // Serve the file for download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($realPath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($realPath));
        readfile($realPath);
        exit;
    } else {
        // Forbidden access or file does not exist
        http_response_code(403);
        echo "Access denied or file does not exist.<br>";
        echo "File exists check: " . (file_exists($realPath) ? 'Yes' : 'No') . "<br>";
    }
} else {
    // Invalid request
    http_response_code(400);
    echo "Invalid request.";
}
?>
