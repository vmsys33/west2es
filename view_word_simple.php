<?php
// Start session and verify user access
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo '<div class="alert alert-danger text-center" role="alert">Unauthorized Access</div>';
    exit;
}

// Ensure that file_path is properly retrieved
if (isset($_GET['file_path'])) {
    $filePath = $_GET['file_path'];

    // Process the file - account for west2es directory
    $documentRoot = $_SERVER['DOCUMENT_ROOT'];
    
    // Handle different path formats
    if (strpos($filePath, '/west2es/') === 0) {
        // Path already includes /west2es/
        $fileAbsolutePath = $documentRoot . $filePath;
    } else {
        // Path is relative, add /west2es/
        $west2esPath = $documentRoot . '/west2es/';
        $fileAbsolutePath = $west2esPath . ltrim($filePath, '/');
    }

    if (!file_exists($fileAbsolutePath)) {
        die('<div class="alert alert-warning text-center" role="alert">
            <strong>File not found on the server.</strong><br>
            <strong>Requested path:</strong> ' . htmlspecialchars($filePath) . '<br>
            <strong>Absolute path:</strong> ' . htmlspecialchars($fileAbsolutePath) . '<br>
            <strong>Document root:</strong> ' . htmlspecialchars($documentRoot) . '
        </div>');
    }

    // Get the filename
    $fileName = basename($fileAbsolutePath);
    
    // Get file size
    $fileSize = filesize($fileAbsolutePath);
    $fileSizeFormatted = formatFileSize($fileSize);
    
    // Get file modification time
    $fileTime = date('F j, Y g:i A', filemtime($fileAbsolutePath));
    
} else {
    die('<div class="alert alert-warning text-center" role="alert">File path not provided.</div>');
}

function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($fileName) ?> - File Information</title>
    <link rel="icon" type="image/png" href="assets/images/logo1.png">  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #f4f7fb; 
            font-family: "Arial", sans-serif; 
        }
        .card { 
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header { 
            background: linear-gradient(135deg, #007bff, #0056b3); 
            color: white; 
            font-size: 1.5rem; 
            font-weight: bold; 
        }
        .card-body { 
            background-color: #ffffff; 
            padding: 2rem; 
            border-radius: 10px; 
        }
        .file-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        .info-value {
            color: #6c757d;
        }
        .btn-download {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
            color: white;
        }
        .alert {
            font-size: 1rem; 
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header text-center">
                <h3><i class="fas fa-file-word me-2"></i><?= htmlspecialchars($fileName) ?></h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info text-center" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Word Document Viewer</strong><br>
                    Due to server configuration, direct preview is not available. 
                    Please download the file to view its contents.
                </div>
                
                <div class="file-info">
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-file me-2"></i>File Name:</span>
                        <span class="info-value"><?= htmlspecialchars($fileName) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-weight-hanging me-2"></i>File Size:</span>
                        <span class="info-value"><?= $fileSizeFormatted ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-calendar me-2"></i>Last Modified:</span>
                        <span class="info-value"><?= $fileTime ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-file-word me-2"></i>File Type:</span>
                        <span class="info-value">Microsoft Word Document</span>
                    </div>
                </div>
                
                <div class="text-center">
                    <a href="<?= htmlspecialchars($filePath) ?>" class="btn btn-download" download>
                        <i class="fas fa-download me-2"></i>Download Document
                    </a>
                    <a href="javascript:window.close();" class="btn btn-secondary ms-2">
                        <i class="fas fa-times me-2"></i>Close
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
