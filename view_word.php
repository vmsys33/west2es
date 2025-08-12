<?php
// Start session and verify user access (optional)
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo '<div class="alert alert-danger text-center" role="alert">Unauthorized Access</div>';
    exit;
}

// Include the PhpWord library (adjust the path to where the vendor folder is located)
require __DIR__ . '/vendor/autoload.php'; // Vendor folder is in the same directory as view_word.php

use PhpOffice\PhpWord\IOFactory;


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

    // Load the Word document (if it's a valid path)
    try {
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($fileAbsolutePath);
    } catch (Exception $e) {
        die('<div class="alert alert-danger text-center" role="alert">Error loading the Word file: ' . htmlspecialchars($e->getMessage()) . '</div>');
    }

    // Extract content from the Word document
    $content = '';
    foreach ($phpWord->getSections() as $section) {
        $elements = $section->getElements();
        foreach ($elements as $element) {
            if (method_exists($element, 'getText')) {
                $text = htmlspecialchars($element->getText(), ENT_NOQUOTES, 'UTF-8');
                $content .= nl2br($text) . "<br>";
            }
        }
    }
} else {
    die('<div class="alert alert-warning text-center" role="alert">File path not provided.</div>');
}


if (empty($content)) {
    echo '<div class="alert alert-info text-center" role="alert">No content available in this document.</div>';
} else {
    // Output the document content
    echo '<!DOCTYPE html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>' . htmlspecialchars($fileName) . ' - View</title>'; // Display file name in the title
    echo '<link rel="icon" type="image/png" href="assets/images/logo1.png">';  
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '<style>';
    echo 'body { background-color: #f4f7fb; font-family: "Arial", sans-serif; }';
    echo '.card { border-radius: 10px; }';
    echo '.card-header { background-color: #007bff; color: white; font-size: 1.5rem; font-weight: bold; }';
    echo '.card-body { background-color: #ffffff; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }';
    echo '.document-content { font-size: 1rem; line-height: 1.6; }';
    echo '.alert { font-size: 1rem; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    echo '<div class="container mt-5">';
    echo '<div class="card shadow">';
    echo '<div class="card-header text-center">';
    echo '<h3>' . htmlspecialchars($fileName) . '</h3>';
    echo '</div>';
    echo '<div class="card-body">';
    echo '<div class="document-content">';
    echo nl2br($content);
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>';
    echo '</body>';
    echo '</html>';
}
?>
