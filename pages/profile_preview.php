<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

require_once '../functions/db_connection.php';
require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';

// Get the current file name
$currentPage = basename($_SERVER['PHP_SELF']);

require_once '../functions/pageTitle.php';
$pageTitle = getPageTitle($currentPage);

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM user_data WHERE id_no = :id_no");
$stmt->bindParam(':id_no', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch user_data_details from the database
$stmt = $pdo->prepare("SELECT * FROM user_data_details WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user_details = $stmt->fetch(PDO::FETCH_ASSOC) ?: []; // Default to an empty array if no data is found

// Check if the user clicked the "Generate PDF" button
if (isset($_GET['generate_pdf'])) {
    // Create a new TCPDF instance
    $pdf = new \TCPDF();

    // Set document information
    $pdf->SetCreator('Your Application');
    $pdf->SetAuthor('West II Elementary School');
    $pdf->SetTitle('User Report');
    $pdf->SetSubject('User Information');

    // Set default header data
    $pdf->SetHeaderData('', 0, 'West II Elementary School', 'Cadiz City, Negros Occidental');

    // Set header and footer fonts
    $pdf->setHeaderFont(['helvetica', '', 10]);
    $pdf->setFooterFont(['helvetica', '', 8]);

    // Set default monospaced font
    $pdf->SetDefaultMonospacedFont('courier');

    // Set margins
    $pdf->SetMargins(15, 27, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 25);

    // Set font
    $pdf->SetFont('helvetica', '', 12);

    // Add a page
    $pdf->AddPage();

    // Generate the HTML content for the PDF
    $html = '
        <div style="text-align: center; font-size: 18px; font-weight: bold;">
            West II Elementary School<br>
            Cadiz City, Negros Occidental
        </div>
        <hr>
        <h2 style="text-align: center;">User Information</h2>
        <table border="1" cellpadding="6" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 12px;">
            <tr>
                <td><strong>DepEd ID No:</strong></td>
                <td>' . htmlspecialchars($user['deped_id_no'] ?? '') . '</td>
            </tr>
            <tr>
                <td><strong>Last Name:</strong></td>
                <td>' . htmlspecialchars($user['last_name'] ?? '') . '</td>
            </tr>
            <tr>
                <td><strong>First Name:</strong></td>
                <td>' . htmlspecialchars($user['first_name'] ?? '') . '</td>
            </tr>
            <tr>
                <td><strong>Middle Name:</strong></td>
                <td>' . htmlspecialchars($user['middle_name'] ?? '') . '</td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>' . htmlspecialchars($user['email'] ?? '') . '</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>' . htmlspecialchars($user['status'] ?? '') . '</td>
            </tr>
            <tr>
                <td><strong>Role:</strong></td>
                <td>' . htmlspecialchars($user['role'] ?? '') . '</td>
            </tr>
            <tr>
                <td><strong>Suffix:</strong></td>
                <td>' . htmlspecialchars($user_details['suffix'] ?? '') . '</td>
            </tr>
            <tr>
                <td><strong>Date of Birth:</strong></td>
                <td>' . htmlspecialchars($user_details['date_of_birth'] ?? '') . '</td>
            </tr>
            <tr>
                <td><strong>Birthplace:</strong></td>
                <td>' . htmlspecialchars($user_details['birthplace'] ?? '') . '</td>
            </tr>
            <tr>
                <td><strong>Sex:</strong></td>
                <td>' . htmlspecialchars($user_details['sex'] ?? '') . '</td>
            </tr>
            <tr>
                <td><strong>Position:</strong></td>
                <td>' . htmlspecialchars($user_details['position'] ?? '') . '</td>
            </tr>
            <tr>
                <td><strong>Contact No:</strong></td>
                <td>' . htmlspecialchars($user_details['contact_no'] ?? '') . '</td>
            </tr>
            <tr>
                <td><strong>Personal Gmail Account:</strong></td>
                <td>' . htmlspecialchars($user_details['personal_gmail_account'] ?? '') . '</td>
            </tr>
        </table>
        <br><br>
        <div style="text-align: center; font-size: 10px;">
            &copy; 2025 West II Elementary School. All Rights Reserved.
        </div>
    ';

    // Write the HTML content into the PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Output the PDF
    $pdf->Output('User_Report.pdf', 'I'); // Display the PDF in the browser
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Report</title>
</head>
<body>
    <div class="container">
        <h1>User Report</h1>
        <p>Click the button below to generate a PDF of the user report.</p>
        <a href="?generate_pdf=1" style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">Generate PDF</a>
    </div>
</body>
</html>
