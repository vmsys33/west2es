<?php
require_once('functions/db_connection.php');
require 'vendor/autoload.php'; // Load PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set Headers (Bold + Centered)
$headers = [
    'DepEd ID No', 'Last Name', 'First Name', 'Middle Name', 'Status', 'Email',
    'Suffix', 'Date of Birth', 'Birthplace', 'Sex', 'Position', 'Contact No', 'Personal Gmail',
    'Bachelor\'s Degree', 'Post Graduate', 'Major', 'Employee No', 'Plantilla No',
    'PhilHealth No', 'BP No', 'Pag-IBIG No', 'TIN No', 'PRC No', 'PRC Validity Date',
    'PhilSys ID No', 'Salary Grade', 'Current Step', 'Date of First Appointment',
    'Date of Latest Promotion', 'First Day of Service', 'Retirement Day'
];

$sheet->fromArray([$headers], NULL, 'A1');

// Apply bold & center alignment to headers
$styleArray = [
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
];
$sheet->getStyle('A1:AE1')->applyFromArray($styleArray);

// Prepare SQL Query
$sql = "SELECT 
            u.deped_id_no, u.last_name, u.first_name, u.middle_name, u.status, u.email,
            d.suffix, d.date_of_birth, d.birthplace, d.sex, d.position, d.contact_no, d.personal_gmail_account,
            d.bachelors_degree, d.post_graduate, d.major, d.employee_no, d.plantilla_no,
            d.philhealth_no, d.bp_no, d.pagibig_no, d.tin_no, d.prc_no, d.prc_validity_date,
            d.phlisys_id_no, d.salary_grade, d.current_step_based_on_payslip, d.date_of_first_appointment,
            d.date_of_latest_promotion, d.first_day_of_service, d.retirement_day
        FROM user_data u
        LEFT JOIN user_data_details d ON u.id_no = d.user_id
        WHERE u.role = 'faculty'";

$stmt = $pdo->prepare($sql);
$stmt->execute();

// Fill Data Rows
$rowNum = 2;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sheet->fromArray([$row], NULL, "A$rowNum");
    $rowNum++;
}

// Auto-size Columns
foreach (range('A', 'AE') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Download the Excel file
$filename = 'Faculty_List.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=$filename");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>
