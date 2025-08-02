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
            $pdf = new TCPDF();

            // Set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('West II Elementary School');
            $pdf->SetTitle('Files Report');
            $pdf->SetSubject('Report');

            // Set header data
            $logoPath = '../images/logo.png'; // Replace with the actual path to your logo
            $pdf->SetHeaderData($logoPath, 30, 'West II Elementary School', "Date Range: $startDate to $endDate");

            // Set header and footer fonts
            $pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
            $pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);

            // Set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // Set margins
            $pdf->SetMargins(15, 27, 15);
            $pdf->SetHeaderMargin(10);
            $pdf->SetFooterMargin(10);

            // Set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            // Add a page
            $pdf->AddPage();

            // Set font
            $pdf->SetFont('helvetica', '', 10);

            // Create table header
            $html = '<h3 style="text-align:center;">Files Report</h3>';
            $html .= '<table style="border-collapse:collapse;width:100%;">';
            $html .= '<thead>
                        <tr style="background-color:#f8f9fa;color:#212529;text-align:left;">
                            <th style="border:1px solid #dee2e6;padding:8px;">Filename</th>
                            <th style="border:1px solid #dee2e6;padding:8px;">Version</th>
                            <th style="border:1px solid #dee2e6;padding:8px;">Date</th>
                            <th style="border:1px solid #dee2e6;padding:8px;">Uploaded By</th>
                            <th style="border:1px solid #dee2e6;padding:8px;">Source Table</th>
                        </tr>
                      </thead>';
            $html .= '<tbody>';

            // Add table rows
            foreach ($results as $row) {
                $humanDate = date("F d, Y", strtotime($row['datetime']));
                $sourceTableLabel = getSourceTableLabel($row['source_table']);

                $html .= '<tr>
                    <td style="border:1px solid #dee2e6;padding:8px;">' . htmlspecialchars($row['filename']) . '</td>
                    <td style="border:1px solid #dee2e6;padding:8px;">' . htmlspecialchars($row['version_no']) . '</td>
                    <td style="border:1px solid #dee2e6;padding:8px;">' . htmlspecialchars($humanDate) . '</td>
                    <td style="border:1px solid #dee2e6;padding:8px;">' . htmlspecialchars($row['uploader_name']) . '</td>
                    <td style="border:1px solid #dee2e6;padding:8px;">' . htmlspecialchars($sourceTableLabel) . '</td>
                </tr>';
            }

            $html .= '</tbody></table>';

            // Output the HTML as PDF content
            $pdf->writeHTML($html, true, false, true, false, '');

            // Clean the buffer before generating PDF
            ob_end_clean();

            // Display the PDF in the browser
            $pdf->Output('Files_Report.pdf', 'I'); // Display in a new tab
        } else {
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
