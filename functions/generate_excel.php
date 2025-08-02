<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

require_once '../functions/db_connection.php';
require_once '../vendor/autoload.php'; // Make sure this path points to your Composer autoload file

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Check if user_id is passed in the URL
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    die('Invalid user ID.');
}

$user_id = intval($_GET['user_id']); // Safely cast to integer

// Fetch user data from the database
$stmt = $pdo->prepare("SELECT * FROM user_data WHERE id_no = :id_no");
$stmt->bindParam(':id_no', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('User not found.');
}

// Fetch user_data_details from the database
$stmt = $pdo->prepare("SELECT * FROM user_data_details WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user_details = $stmt->fetch(PDO::FETCH_ASSOC) ?: []; // Default to an empty array if no data is found

// Get website name and school logo from session
$website_name = $_SESSION['website_name'] ?? 'Default Website';
$first_name = $_SESSION['first_name'] ?? 'First';
$last_name = $_SESSION['last_name'] ?? 'Last';

// Create a new Spreadsheet instance
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set document properties
$spreadsheet->getProperties()
    ->setCreator('Your Application')
    ->setLastModifiedBy($website_name) // Dynamic website name
    ->setTitle('Profile Details')
    ->setSubject("$first_name $last_name Profile Data");

// Add header data
$sheet->setCellValue('A1', 'West II Elementary School')
    ->setCellValue('A2', 'Cadiz City, Negros Occidental')
    ->mergeCells('A1:C1')
    ->mergeCells('A2:C2')
    ->getStyle('A1:A2')->getFont()->setBold(true);
$sheet->setCellValue('A4', $first_name . ' ' . $last_name . ' Profile Data')
    ->mergeCells('A4:C4')
    ->getStyle('A4')->getFont()->setBold(true);

// Add table headers
$sheet->setCellValue('A6', 'Field')
    ->setCellValue('B6', 'Value')
    ->getStyle('A6:B6')->getFont()->setBold(true);

// Add first_name, middle_name, and last_name at the beginning
$user_details = array_merge([
    'first_name' => $user['first_name'],
    'middle_name' => $user['middle_name'],
    'last_name' => $user['last_name']
], $user_details);

$row = 7; // Starting row for user details
foreach ($user_details as $key => $value) {
    if ($key === 'id' || $key === 'user_id') {
        continue; // Skip the `id` and `user_id` fields
    }
    $formatted_key = ucfirst(str_replace('_', ' ', $key)); // Format key to a readable format
    $sheet->setCellValue('A' . $row, $formatted_key)
          ->setCellValue('B' . $row, $value ?? '-');
    $row++;
}

// Set columns auto width
foreach (range('A', 'B') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Generate the dynamic Excel filename
$excel_filename = $first_name . '_' . $last_name . '_Profile.xlsx';

// Output the Excel file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $excel_filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
