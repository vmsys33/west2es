<?php
ob_start();
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

// Include necessary files
require_once '../functions/db_connection.php';
require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';

// Get the current file name
$currentPage = basename($_SERVER['PHP_SELF']);

require_once '../functions/pageTitle.php';
$pageTitle = getPageTitle($currentPage);

include '../includes/header.php';
include '../includes/top-navbar.php';
include '../includes/sidebar.php';

// Map source table names to their labels
function getSourceTableLabel($sourceTable) {
    switch ($sourceTable) {
       
        
        case "admin_files":
            return "Administrative Files";
        case "cild_files":
            return "Curriculum Implementation and Learning Delivery";
        case "lulr_files":
            return "Localization and Utilization of Learning Resources";                            
        case "aeld_files":
        return "Assessment/Evaluation of Learner's Development";                            

        case "if_proposals_files":
        return "Innovation Files/Proposals";                            

        case "if_completed_files":
        return "Innovation Files/Completed";       
                
        case "rp_proposal_berf_files":
        return "Research Papers/Proposals/Berf";       
        
        case "rp_proposal_nonberf_files":
        return "Research Papers/Proposals/Non Berf";       

        case "rp_completed_berf_files":
        return "Research Papers/Completed/Berf";       
        
        case "rp_completed_nonberf_files":
        return "Research Papers/Completed/Non Berf";       

        case "t_lr_files":
        return "Transparency/Liquidation Report";       

        case "t_pp_files":
        return "Transparency/Project Proposal";       

        case "t_rs_files":
        return "Transparency/Realignment and Supplementals";       
    
    }
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generateReport'])) {
    // Get start and end dates from the form
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    // Validate the dates
    if (!strtotime($startDate) || !strtotime($endDate)) {
        echo '<div class="alert alert-danger">Invalid date format. Please provide valid dates.</div>';
        exit;
    }

    try {
        // Prepare the query with unique placeholders for each UNION block
        $query = "
            SELECT 
                f.filename,
                v.version_no,
                v.datetime,
                CONCAT(u.first_name, ' ', u.last_name) AS uploader_name,
                :source_table AS source_table
            FROM admin_files f
            INNER JOIN admin_files_versions v ON f.id = v.file_id
            INNER JOIN user_data u ON f.user_id = u.id_no
            WHERE v.datetime BETWEEN :startDate1 AND :endDate1
            UNION ALL
            SELECT 
                f.filename,
                v.version_no,
                v.datetime,
                CONCAT(u.first_name, ' ', u.last_name) AS uploader_name,
                :source_table2 AS source_table
            FROM cild_files f
            INNER JOIN cild_files_versions v ON f.id = v.file_id
            INNER JOIN user_data u ON f.user_id = u.id_no
            WHERE v.datetime BETWEEN :startDate2 AND :endDate2
            UNION ALL
            SELECT 
                f.filename,
                v.version_no,
                v.datetime,
                CONCAT(u.first_name, ' ', u.last_name) AS uploader_name,
                :source_table3 AS source_table
            FROM lulr_files f
            INNER JOIN lulr_files_versions v ON f.id = v.file_id
            INNER JOIN user_data u ON f.user_id = u.id_no
            WHERE v.datetime BETWEEN :startDate3 AND :endDate3
            UNION ALL
            SELECT 
                f.filename,
                v.version_no,
                v.datetime,
                CONCAT(u.first_name, ' ', u.last_name) AS uploader_name,
                :source_table4 AS source_table
            FROM aeld_files f
            INNER JOIN aeld_files_versions v ON f.id = v.file_id
            INNER JOIN user_data u ON f.user_id = u.id_no
            WHERE v.datetime BETWEEN :startDate4 AND :endDate4
            UNION ALL
            SELECT 
                f.filename,
                v.version_no,
                v.datetime,
                CONCAT(u.first_name, ' ', u.last_name) AS uploader_name,
                :source_table5 AS source_table
            FROM if_proposals_files f
            INNER JOIN if_proposals_files_versions v ON f.id = v.file_id
            INNER JOIN user_data u ON f.user_id = u.id_no
            WHERE v.datetime BETWEEN :startDate5 AND :endDate5
            UNION ALL
            SELECT 
                f.filename,
                v.version_no,
                v.datetime,
                CONCAT(u.first_name, ' ', u.last_name) AS uploader_name,
                :source_table6 AS source_table
            FROM if_completed_files f
            INNER JOIN if_completed_files_versions v ON f.id = v.file_id
            INNER JOIN user_data u ON f.user_id = u.id_no
            WHERE v.datetime BETWEEN :startDate6 AND :endDate6
            UNION ALL
            SELECT 
                f.filename,
                v.version_no,
                v.datetime,
                CONCAT(u.first_name, ' ', u.last_name) AS uploader_name,
                :source_table7 AS source_table
            FROM rp_proposal_berf_files f
            INNER JOIN rp_proposal_berf_files_versions v ON f.id = v.file_id
            INNER JOIN user_data u ON f.user_id = u.id_no
            WHERE v.datetime BETWEEN :startDate7 AND :endDate7 
            UNION ALL
            SELECT 
                f.filename,
                v.version_no,
                v.datetime,
                CONCAT(u.first_name, ' ', u.last_name) AS uploader_name,
                :source_table8 AS source_table
            FROM rp_completed_berf_files f
            INNER JOIN rp_completed_berf_files_versions v ON f.id = v.file_id
            INNER JOIN user_data u ON f.user_id = u.id_no
            WHERE v.datetime BETWEEN :startDate8 AND :endDate8
            UNION ALL
            SELECT 
                f.filename,
                v.version_no,
                v.datetime,
                CONCAT(u.first_name, ' ', u.last_name) AS uploader_name,
                :source_table9 AS source_table
            FROM rp_completed_nonberf_files f
            INNER JOIN rp_completed_nonberf_files_versions v ON f.id = v.file_id
            INNER JOIN user_data u ON f.user_id = u.id_no
            WHERE v.datetime BETWEEN :startDate9 AND :endDate9
            UNION ALL
            SELECT 
                f.filename,
                v.version_no,
                v.datetime,
                CONCAT(u.first_name, ' ', u.last_name) AS uploader_name,
                :source_table10 AS source_table
            FROM rp_proposal_nonberf_files f
            INNER JOIN rp_proposal_nonberf_files_versions v ON f.id = v.file_id
            INNER JOIN user_data u ON f.user_id = u.id_no
            WHERE v.datetime BETWEEN :startDate10 AND :endDate10
            UNION ALL
            SELECT 
                f.filename,
                v.version_no,
                v.datetime,
                CONCAT(u.first_name, ' ', u.last_name) AS uploader_name,
                :source_table11 AS source_table
            FROM t_lr_files f
            INNER JOIN t_lr_files_versions v ON f.id = v.file_id
            INNER JOIN user_data u ON f.user_id = u.id_no
            WHERE v.datetime BETWEEN :startDate11 AND :endDate11
            UNION ALL
            SELECT 
                f.filename,
                v.version_no,
                v.datetime,
                CONCAT(u.first_name, ' ', u.last_name) AS uploader_name,
                :source_table12 AS source_table
            FROM t_pp_files f
            INNER JOIN t_pp_files_versions v ON f.id = v.file_id
            INNER JOIN user_data u ON f.user_id = u.id_no
            WHERE v.datetime BETWEEN :startDate12 AND :endDate12
            UNION ALL
            SELECT 
                f.filename,
                v.version_no,
                v.datetime,
                CONCAT(u.first_name, ' ', u.last_name) AS uploader_name,
                :source_table13 AS source_table
            FROM t_rs_files f
            INNER JOIN t_rs_files_versions v ON f.id = v.file_id
            INNER JOIN user_data u ON f.user_id = u.id_no
            WHERE v.datetime BETWEEN :startDate13 AND :endDate13
            ORDER BY filename, datetime ASC
        ";


        

        $stmt = $pdo->prepare($query);

        // Bind parameters dynamically for each UNION block
        $params = [
            'startDate1' => $startDate,
            'endDate1' => $endDate,
            'startDate2' => $startDate,
            'endDate2' => $endDate,
            'startDate3' => $startDate,
            'endDate3' => $endDate,
            'startDate4' => $startDate,
            'endDate4' => $endDate,
            'startDate5' => $startDate,
            'endDate5' => $endDate,
            'startDate6' => $startDate,
            'endDate6' => $endDate,
            'startDate7' => $startDate,
            'endDate7' => $endDate,
            'startDate8' => $startDate,
            'endDate8' => $endDate,
            'startDate9' => $startDate,
            'endDate9' => $endDate,
            'startDate10' => $startDate,
            'endDate10' => $endDate,
            'startDate11' => $startDate,
            'endDate11' => $endDate,
            'startDate12' => $startDate,
            'endDate12' => $endDate,
            'startDate13' => $startDate,
            'endDate13' => $endDate,
            'source_table' => 'admin_files',
            'source_table2' => 'cild_files',
            'source_table3' => 'lulr_files',
            'source_table4' => 'aeld_files',
            'source_table5' => 'if_proposals_files',
            'source_table6' => 'if_completed_files',
            'source_table7' => 'rp_proposal_berf_files',
            'source_table8' => 'rp_completed_berf_files',
            'source_table9' => 'rp_completed_nonberf_files',
            'source_table10' => 'rp_proposal_nonberf_files',
            'source_table11' => 't_lr_files',
            'source_table12' => 't_pp_files',
            'source_table13' => 't_rs_files',
        ];

        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);



        if (!empty($results)) {
    // Initialize TCPDF

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
       



    $pdf = new TCPDF();

    // Set document information
    $pdf->SetCreator($website_name);
    $pdf->SetAuthor($website_name); // Dynamic Author
    $pdf->SetTitle('Files Report');
    $pdf->SetSubject("Files Report for Date Range: $startDate to $endDate");

    // Remove the default header
    $pdf->setPrintHeader(false);

    // Set footer fonts
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


    // Generate the professional layout for the PDF
    $html = '
        <div style="text-align: center; font-size: 12px; font-weight: bold;">
        West II Elementary School<br>
        Cadiz City, Negros Occidental
    </div>
        <hr>
        <h2 style="text-align: center;">Files Report</h2>
        <h4 style="text-align: center;">Date Range: ' . htmlspecialchars($startDate) . ' to ' . htmlspecialchars($endDate) . '</h4>
        <table border="1" cellpadding="6" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 12px;">
            <thead>
                <tr style="background-color: #f8f9fa;">
                    <th style="width: 30%;">Filename</th>
                    <th style="width: 7%;">Ver.</th>
                    <th>Date</th>
                    <th>Uploaded By</th>
                    <th>Files Category</th>
                </tr>
            </thead>
            <tbody>';

    // Populate the table with data
    foreach ($results as $row) {
        $humanDate = date("M d, Y", strtotime($row['datetime']));
        $sourceTableLabel = getSourceTableLabel($row['source_table']);

        $html .= '
                <tr>
                    <td style="width: 30%;">' . htmlspecialchars($row['filename']) . '</td>
                    <td style="width: 7%;">' . htmlspecialchars($row['version_no']) . '</td>
                    <td>' . htmlspecialchars($humanDate) . '</td>
                    <td>' . htmlspecialchars($row['uploader_name']) . '</td>
                    <td>' . htmlspecialchars($sourceTableLabel) . '</td>
                </tr>';
    }

    $html .= '
            </tbody>
        </table>
        <br><br>
        <div style="text-align: center; font-size: 10px;">
            &copy; 2025 West II Elementary School. All Rights Reserved.
        </div>
    ';

    // Write the HTML content into the PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Generate the dynamic PDF filename
    $pdf_filename = "Files_Report_" . date("Ymd") . ".pdf";

    // Output the PDF for preview
    ob_end_clean();
    $pdf->Output($pdf_filename, 'I'); // Display the PDF in the browser
}
  
        else {
            echo '<div class="alert alert-warning">No records found for the specified date range.</div>';
        }
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    }
}
?>

<!-- HTML Form -->
<div class="col-md-9 main-content">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3 class="text-center">Generate Files Report</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="" target="_blank">
                <div class="mb-3">
                    <label for="startDate" class="form-label">Start Date:</label>
                    <input type="date" id="startDate" name="startDate" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="endDate" class="form-label">End Date:</label>
                    <input type="date" id="endDate" name="endDate" class="form-control" required>
                </div>
                <div class="text-center">
                    <button type="submit" name="generateReport" class="btn btn-primary btn-lg px-5">Generate Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
