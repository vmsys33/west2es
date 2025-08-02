<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

require_once 'db_connection.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid faculty ID.']);
    exit;
}

$facultyId = intval($_GET['id']);

try {
    // Fetch data from user_data
    $stmt = $pdo->prepare("SELECT deped_id_no, last_name, first_name, middle_name, email, status FROM user_data WHERE id_no = ?");
    $stmt->execute([$facultyId]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        echo json_encode(['status' => 'error', 'message' => 'Faculty member not found.']);
        exit;
    }

    // Fetch additional details from user_data_details
    $stmt = $pdo->prepare("
        SELECT suffix, date_of_birth, birthplace, sex, position, contact_no, personal_gmail_account, 
               bachelors_degree, post_graduate, major, employee_no, plantilla_no, philhealth_no, 
               bp_no, pagibig_no, tin_no, prc_no, prc_validity_date, phlisys_id_no, salary_grade, 
               current_step_based_on_payslip, date_of_first_appointment, date_of_latest_promotion, 
               first_day_of_service, retirement_day 
        FROM user_data_details WHERE user_id = ?");
    $stmt->execute([$facultyId]);
    $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    // Combine data
    $details = array_merge($userData, $userDetails ? $userDetails : []);

    echo json_encode(['status' => 'success', 'details' => $details]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
