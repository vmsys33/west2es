<?php
// Start session and verify user access (optional)
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo '<div class="alert alert-danger text-center" role="alert">Unauthorized Access</div>';
    exit;
}

// Include PhpSpreadsheet library (adjust path based on your project structure)
require __DIR__ . '/vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\IOFactory;

// Ensure that file_path is properly retrieved
if (isset($_GET['file_path'])) {
    $filePath = $_GET['file_path'];

    // Process the file - account for west2es directory
    $documentRoot = $_SERVER['DOCUMENT_ROOT'];
    $west2esPath = $documentRoot . '/west2es/';
    $fileAbsolutePath = $west2esPath . ltrim($filePath, '/');

    if (!file_exists($fileAbsolutePath)) {
        die('<div class="alert alert-warning text-center" role="alert">File not found on the server. Path: ' . htmlspecialchars($fileAbsolutePath) . '</div>');
    }

    // Load the Excel file
    try {
        $spreadsheet = IOFactory::load($fileAbsolutePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();
    } catch (Exception $e) {
        die('<div class="alert alert-danger text-center" role="alert">Error loading the Excel file: ' . htmlspecialchars($e->getMessage()) . '</div>');
    }
} else {
    die('<div class="alert alert-warning text-center" role="alert">File path not provided.</div>');
}

// Start HTML output
echo '<!DOCTYPE html>';
echo '<html lang="en">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<title>Excel Document - View</title>';
echo '<link rel="icon" type="image/png" href="assets/images/logo1.png">';  
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">';
echo '<style>';
echo 'body { background-color: #f4f7fb; font-family: "Arial", sans-serif; }';
echo '.card { border-radius: 10px; }';
echo '.card-header { background-color: #007bff; color: white; font-size: 1.5rem; font-weight: bold; }';
echo '.card-body { background-color: #ffffff; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }';
echo 'table { width: 100%; border-collapse: collapse; }';
echo 'th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }';
echo 'th { background-color: #007bff; color: white; }';
echo '</style>';
echo '</head>';
echo '<body>';
echo '<div class="container mt-5">';
echo '<div class="card shadow">';
echo '<div class="card-header text-center">';
echo '<h3>View Excel Document Content</h3>';
echo '</div>';
echo '<div class="card-body">';
echo '<div class="table-responsive">';
echo '<table class="table table-bordered">';

// Display Excel content as an HTML table
if (!empty($data)) {
    foreach ($data as $rowIndex => $row) {
        echo '<tr>';
        foreach ($row as $cell) {
            $cellValue = htmlspecialchars($cell, ENT_NOQUOTES, 'UTF-8');
            echo $rowIndex === 0 ? "<th>{$cellValue}</th>" : "<td>{$cellValue}</td>";
        }
        echo '</tr>';
    }
} else {
    echo '<tr><td class="text-center text-muted" colspan="100%">No data available in this document.</td></tr>';
}

echo '</table>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>';
echo '</body>';
echo '</html>';
?>
