<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

require_once '../functions/db_connection.php';
require_once '../vendor/autoload.php';

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
$user_details = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

// Get website name and user name from session
$website_name = $_SESSION['website_name'] ?? 'Default Website';
$first_name = $user['first_name'] ?? 'First';
$middle_name = $user['middle_name'] ?? 'Middle';
$last_name = $user['last_name'] ?? 'Last';

// Create a new Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set document properties
$spreadsheet->getProperties()
    ->setCreator("Your Application")
    ->setLastModifiedBy($website_name)
    ->setTitle("Profile Details")
    ->setSubject("$first_name $last_name Profile Data");

// Add Header Row
$headers = ["Field", "Value"];
$sheet->fromArray($headers, NULL, 'A1');

// Make Header Bold
$sheet->getStyle("A1:B1")->getFont()->setBold(true);

// Start from row 2
$row = 2;

// First, add Name fields
$name_fields = [
    'First Name' => $first_name,
    'Middle Name' => $middle_name,
    'Last Name' => $last_name
];

foreach ($name_fields as $key => $value) {
    $sheet->setCellValue("A$row", $key);
    $sheet->setCellValue("B$row", $value);
    $row++;
}

// Next, add the rest of the user details (excluding id and user_id)
foreach ($user_details as $key => $value) {
    if ($key === 'id' || $key === 'user_id' || in_array($key, ['first_name', 'middle_name', 'last_name'])) {
        continue; // Skip unnecessary fields
    }
    $formatted_key = ucfirst(str_replace('_', ' ', $key)); // Format key to readable text
    $sheet->setCellValue("A$row", $formatted_key);
    $sheet->setCellValue("B$row", $value ?? "-");
    $row++;
}

// Auto-size columns
foreach (range('A', 'B') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Generate the filename dynamically
$excel_filename = $first_name . '_' . $last_name . '_Profile.xlsx';

// Send the file to the browser for download
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=\"$excel_filename\"");
header("Cache-Control: max-age=0");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
?>
