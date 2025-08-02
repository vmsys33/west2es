<?php
require_once 'db_connection.php'; // Ensure the database connection is included
require_once 'upload_user_photo.php'; // Include photo upload functions
header('Content-Type: application/json');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize and validate input fields
        $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
        if (!$user_id) {
            throw new Exception('Invalid user ID.');
        }

        $suffix = $_POST['suffix'] ?? null;
        $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
        $birthplace = $_POST['birthplace'] ?? null;
        $sex = $_POST['sex'] ?? null;
        $position = $_POST['position'] ?? null;
        $contact_no = $_POST['contact_no'] ?? null;
        $personal_gmail_account = $_POST['personal_gmail_account'] ?? null;
        $bachelors_degree = $_POST['bachelors_degree'] ?? null;
        $post_graduate = $_POST['post_graduate'] ?? null;
        $major = $_POST['major'] ?? null;
        $employee_no = $_POST['employee_no'] ?? null;
        $plantilla_no = $_POST['plantilla_no'] ?? null;
        $philhealth_no = $_POST['philhealth_no'] ?? null;
        $bp_no = $_POST['bp_no'] ?? null;
        $pagibig_no = $_POST['pagibig_no'] ?? null;
        $tin_no = $_POST['tin_no'] ?? null;
        $prc_no = $_POST['prc_no'] ?? null;
        $prc_validity_date = !empty($_POST['prc_validity_date']) ? $_POST['prc_validity_date'] : null;
        $phlisys_id_no = $_POST['phlisys_id_no'] ?? null;
        $salary_grade = $_POST['salary_grade'] ?? null;
        $current_step = $_POST['current_step_based_on_payslip'] ?? null;
        $first_appointment = !empty($_POST['date_of_first_appointment']) ? $_POST['date_of_first_appointment'] : null;
        $latest_promotion = !empty($_POST['date_of_latest_promotion']) ? $_POST['date_of_latest_promotion'] : null;
        $first_service = !empty($_POST['first_day_of_service']) ? $_POST['first_day_of_service'] : null;
        $retirement_day = !empty($_POST['retirement_day']) ? $_POST['retirement_day'] : null;

        // Handle password update if provided
        $password = null;
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        }

        // Handle photo upload if provided
        $photoResult = null;
        if (!empty($_FILES['photo']['name'])) {
            $photoResult = uploadUserPhoto($user_id, $_FILES['photo']);
            if (!$photoResult['success']) {
                echo json_encode(['status' => 'error', 'message' => $photoResult['message']]);
                exit;
            }
        }

        // Update user_data table
        $updateUserSQL = "UPDATE user_data SET
                            deped_id_no = :deped_id_no,
                            last_name = :last_name,
                            first_name = :first_name,
                            middle_name = :middle_name,
                            email = :email,
                            status = :status,
                            role = :role";

        if ($password) {
            $updateUserSQL .= ", password = :password";
        }

        $updateUserSQL .= " WHERE id_no = :user_id";

        $stmt = $pdo->prepare($updateUserSQL);
        $deped_id_no = $_POST['deped_id_no'] ?? null;
        $last_name = $_POST['last_name'] ?? null;
        $first_name = $_POST['first_name'] ?? null;
        $middle_name = $_POST['middle_name'] ?? null;
        $email = $_POST['email'] ?? null;
        $status = $_POST['status'] ?? null;
        $role = $_POST['role'] ?? null;

        $stmt->bindParam(':deped_id_no', $deped_id_no);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':middle_name', $middle_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':role', $role);
        if ($password) {
            $stmt->bindParam(':password', $password);
        }
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Update user_data_details table
        $updateDetailsSQL = "INSERT INTO user_data_details (
                                user_id, suffix, date_of_birth, birthplace, sex, position, contact_no,
                                personal_gmail_account, bachelors_degree, post_graduate, major, employee_no,
                                plantilla_no, philhealth_no, bp_no, pagibig_no, tin_no, prc_no, prc_validity_date,
                                phlisys_id_no, salary_grade, current_step_based_on_payslip, date_of_first_appointment,
                                date_of_latest_promotion, first_day_of_service, retirement_day
                             ) VALUES (
                                :user_id, :suffix, :date_of_birth, :birthplace, :sex, :position, :contact_no,
                                :personal_gmail_account, :bachelors_degree, :post_graduate, :major, :employee_no,
                                :plantilla_no, :philhealth_no, :bp_no, :pagibig_no, :tin_no, :prc_no, :prc_validity_date,
                                :phlisys_id_no, :salary_grade, :current_step, :first_appointment,
                                :latest_promotion, :first_service, :retirement_day
                             ) ON DUPLICATE KEY UPDATE
                                suffix = VALUES(suffix),
                                date_of_birth = VALUES(date_of_birth),
                                birthplace = VALUES(birthplace),
                                sex = VALUES(sex),
                                position = VALUES(position),
                                contact_no = VALUES(contact_no),
                                personal_gmail_account = VALUES(personal_gmail_account),
                                bachelors_degree = VALUES(bachelors_degree),
                                post_graduate = VALUES(post_graduate),
                                major = VALUES(major),
                                employee_no = VALUES(employee_no),
                                plantilla_no = VALUES(plantilla_no),
                                philhealth_no = VALUES(philhealth_no),
                                bp_no = VALUES(bp_no),
                                pagibig_no = VALUES(pagibig_no),
                                tin_no = VALUES(tin_no),
                                prc_no = VALUES(prc_no),
                                prc_validity_date = VALUES(prc_validity_date),
                                phlisys_id_no = VALUES(phlisys_id_no),
                                salary_grade = VALUES(salary_grade),
                                current_step_based_on_payslip = VALUES(current_step_based_on_payslip),
                                date_of_first_appointment = VALUES(date_of_first_appointment),
                                date_of_latest_promotion = VALUES(date_of_latest_promotion),
                                first_day_of_service = VALUES(first_day_of_service),
                                retirement_day = VALUES(retirement_day)";

        $stmt = $pdo->prepare($updateDetailsSQL);

        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':suffix', $suffix);
        $stmt->bindParam(':date_of_birth', $date_of_birth);
        $stmt->bindParam(':birthplace', $birthplace);
        $stmt->bindParam(':sex', $sex);
        $stmt->bindParam(':position', $position);
        $stmt->bindParam(':contact_no', $contact_no);
        $stmt->bindParam(':personal_gmail_account', $personal_gmail_account);
        $stmt->bindParam(':bachelors_degree', $bachelors_degree);
        $stmt->bindParam(':post_graduate', $post_graduate);
        $stmt->bindParam(':major', $major);
        $stmt->bindParam(':employee_no', $employee_no);
        $stmt->bindParam(':plantilla_no', $plantilla_no);
        $stmt->bindParam(':philhealth_no', $philhealth_no);
        $stmt->bindParam(':bp_no', $bp_no);
        $stmt->bindParam(':pagibig_no', $pagibig_no);
        $stmt->bindParam(':tin_no', $tin_no);
        $stmt->bindParam(':prc_no', $prc_no);
        $stmt->bindParam(':prc_validity_date', $prc_validity_date);
        $stmt->bindParam(':phlisys_id_no', $phlisys_id_no);
        $stmt->bindParam(':salary_grade', $salary_grade);
        $stmt->bindParam(':current_step', $current_step);
        $stmt->bindParam(':first_appointment', $first_appointment);
        $stmt->bindParam(':latest_promotion', $latest_promotion);
        $stmt->bindParam(':first_service', $first_service);
        $stmt->bindParam(':retirement_day', $retirement_day);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'User details updated successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
