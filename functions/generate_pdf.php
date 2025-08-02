<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

require_once '../functions/db_connection.php';
require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';

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
$school_logo = $_SESSION['school_logo'] ?? '../assets/images/logo1.png';
$first_name = $_SESSION['first_name'] ?? 'First';
$last_name = $_SESSION['last_name'] ?? 'Last';

// Create a new TCPDF instance
$pdf = new TCPDF();

// Set document information
$pdf->SetCreator('Your Application');
$pdf->SetAuthor($website_name); // Dynamic website name
$pdf->SetTitle('Profile Details');
$pdf->SetSubject("$first_name $last_name"); // Dynamic subject using session data

// Remove default header
$pdf->setPrintHeader(false);

// Set default footer fonts
$pdf->setFooterFont(['helvetica', '', 8]);

// Set default monospaced font
$pdf->SetDefaultMonospacedFont('courier');

// Set margins
$pdf->SetMargins(15, 15, 15);
$pdf->SetFooterMargin(10);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 25);

// Set font
$pdf->SetFont('helvetica', '', 12);

// Add a page
$pdf->AddPage();

// Add the favicon/logo
$pdf->Image($school_logo, 15, 10, 20, 20, '', '', '', true); // Add logo from session data

// Add the school name and address
$html = '
    <div style="text-align: center; font-size: 18px; font-weight: bold;">
        West II Elementary School<br>
        Cadiz City, Negros Occidental
    </div>
    <hr>
    <h2 style="text-align: center;">' . htmlspecialchars($first_name . ' ' . $last_name) . ' Profile Data</h2>
    <table border="1" cellpadding="6" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 12px;">';


    // Add user details dynamically, including `first_name`, `middle_name`, and `last_name`
$user_details['first_name'] = $user['first_name'];
$user_details['middle_name'] = $user['middle_name'];
$user_details['last_name'] = $user['last_name'];

// Add user details dynamically, excluding `id` and `user_id`
foreach ($user_details as $key => $value) {
    if ($key === 'id' || $key === 'user_id') {
        continue; // Skip the `id` and `user_id` fields
    }
    $formatted_key = ucfirst(str_replace('_', ' ', $key)); // Format key to a readable format
    $html .= '
        <tr>
            <td><strong>' . htmlspecialchars($formatted_key) . '</strong></td>
            <td>' . htmlspecialchars($value ?? '-') . '</td>
        </tr>';
}

$html .= '</table>
    <br><br>
    <div style="text-align: center; font-size: 10px;">
        &copy; 2025 West II Elementary School. All Rights Reserved.
    </div>
';

// Write the HTML content into the PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Generate the dynamic PDF filename
$pdf_filename = $first_name . '_' . $last_name . '_Profile.pdf';

// Output the PDF
$pdf->Output($pdf_filename, 'I'); // Display the PDF in the browser
exit;
?>
