<?php
// Start session and verify user access (optional)
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo '<div class="alert alert-danger text-center" role="alert">Unauthorized Access</div>';
    exit;
}

// Include PhpPresentation library (adjust path based on your project structure)
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpPresentation\IOFactory;

// Ensure that file_path is properly retrieved
if (isset($_GET['file_path'])) {
    $filePath = $_GET['file_path'];

    // Process the file
    $fileAbsolutePath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . ltrim($filePath, DIRECTORY_SEPARATOR);

    if (!file_exists($fileAbsolutePath)) {
        die('<div class="alert alert-warning text-center" role="alert">File not found on the server.</div>');
    }

    // Load the PowerPoint file
    try {
        $presentation = IOFactory::load($fileAbsolutePath);
        $slides = $presentation->getAllSlides();
    } catch (Exception $e) {
        die('<div class="alert alert-danger text-center" role="alert">Error loading the PowerPoint file: ' . htmlspecialchars($e->getMessage()) . '</div>');
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
echo '<title>PowerPoint Document - View</title>';
echo '<link rel="icon" type="image/png" href="assets/images/logo1.png">';  
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">';
echo '<style>';
echo 'body { background-color: #f4f7fb; font-family: "Arial", sans-serif; }';
echo '.card { border-radius: 10px; }';
echo '.card-header { background-color: #007bff; color: white; font-size: 1.5rem; font-weight: bold; }';
echo '.card-body { background-color: #ffffff; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }';
echo '.ppt-slide { margin-bottom: 30px; }';
echo '.ppt-slide-title { font-size: 1.3rem; font-weight: bold; }';
echo '.ppt-slide-content { font-size: 1rem; line-height: 1.6; }';
echo '</style>';
echo '</head>';
echo '<body>';
echo '<div class="container mt-5">';
echo '<div class="card shadow">';
echo '<div class="card-header text-center">';
echo '<h3>View PowerPoint Document Content</h3>';
echo '</div>';
echo '<div class="card-body">';

// Loop through slides and display their content
foreach ($slides as $slideIndex => $slide) {
    echo '<div class="ppt-slide">';
    echo '<div class="ppt-slide-title">Slide ' . ($slideIndex + 1) . '</div>';
    echo '<div class="ppt-slide-content">';

    // Extract text from the slide
    $slideText = '';
    foreach ($slide->getShapeCollection() as $shape) {
        if ($shape instanceof PhpOffice\PhpPresentation\Shape\TextBox) {
            $slideText .= $shape->getText() . "<br>";
        }
    }

    // Display slide content
    echo htmlspecialchars($slideText, ENT_NOQUOTES, 'UTF-8');
    echo '</div>';
    echo '</div>';
}

echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>';
echo '</body>';
echo '</html>';
?>
