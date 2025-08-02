<?php
// Ensure that file_path is provided in the URL
if (isset($_GET['file_path'])) {
    $filePath = $_GET['file_path'];

    // Build the absolute file path - account for west2es directory
    $documentRoot = $_SERVER['DOCUMENT_ROOT'];
    $west2esPath = $documentRoot . '/west2es/';
    $fileAbsolutePath = $west2esPath . ltrim($filePath, '/');

    // Check if the file exists
    if (!file_exists($fileAbsolutePath)) {
        die('<div class="alert alert-warning text-center" role="alert">File not found on the server. Path: ' . htmlspecialchars($fileAbsolutePath) . '</div>');
    }
} else {
    die('<div class="alert alert-warning text-center" role="alert">File path not provided.</div>');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Viewer</title>
    <link rel="icon" type="image/png" href="logo1.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3>PDF Document Viewer</h3>
            </div>
            <div class="card-body">
                <!-- Embed the PDF file -->
                <embed src="<?php echo $filePath; ?>" type="application/pdf" width="100%" height="600px" />
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
