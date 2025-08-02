<?php
require_once '../vendor/autoload.php'; 

use PhpOffice\PhpWord\IOFactory;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Preview</title>
    <link rel="icon" type="image/png" href="../assets/images/logo1.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #0056b3;
        }
        .error {
            color: #b00020;
            font-weight: bold;
        }
        .content {
            overflow-x: auto;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        try {
            if (!isset($_GET['file']) || empty($_GET['file'])) {
                throw new Exception('No file specified. Please provide a valid file path.');
            }

            $filePath = $_GET['file']; // Get the file path from the query parameter

            if (!file_exists($filePath)) {
                throw new Exception('The specified file does not exist.');
            }

            $documentName = basename($filePath); // Extract the file name

            echo "<h1>Document Preview: " . htmlspecialchars($documentName) . "</h1>";

            $phpWord = IOFactory::load($filePath); 
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');

            echo '<div class="content">';
            $htmlWriter->save('php://output');
            echo '</div>';
        } catch (\Exception $e) {
            echo '<p class="error">Error loading or converting document: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>
</body>
</html>
