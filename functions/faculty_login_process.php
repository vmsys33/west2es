<?php
session_start(); // Start the session
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // $deped_id_no = filter_input(INPUT_POST, 'deped_id_no', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $deped_id_no = filter_input(INPUT_POST, 'deped_id_no', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    header('Content-Type: application/json');

    if (!$deped_id_no || !$email || !$password) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Validate deped_id_no format (exactly 7 digits)
    if (!preg_match('/^\d{7}$/', $deped_id_no)) {
        echo json_encode(['status' => 'error', 'message' => 'DepEd ID No. must be exactly 7 digits.']);
        exit;
    }

    try {
        // Query the database for the faculty account
        $stmt = $pdo->prepare("SELECT * FROM user_data WHERE deped_id_no = :deped_id_no AND email = :email");
        $stmt->bindParam(':deped_id_no', $deped_id_no);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                if ($user['role'] === 'faculty') {
                    // Set session variables for authenticated faculty
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_id'] = $user['id_no'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['email'] = $email;
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];


                       // Fetch general settings from the database
                    $settings_stmt = $pdo->prepare("SELECT website_name, email_address, school_logo, admin_name FROM general_setting WHERE id = 1");
                    $settings_stmt->execute();
                    $settings = $settings_stmt->fetch(PDO::FETCH_ASSOC);

                    if ($settings) {
                        $_SESSION['website_name'] = $settings['website_name'];
                        $_SESSION['email_address'] = $settings['email_address'];
                        $_SESSION['school_logo'] = $settings['school_logo'];
                        $_SESSION['admin_name'] = $settings['admin_name'];
                    }


                    echo json_encode(['status' => 'success', 'message' => 'Welcome, Faculty! Redirecting...']);
                    exit;
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Access denied. Only faculty members can log in.']);
                    exit;
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid password.']);
                exit;
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No account found with the provided DepEd ID No. and email.']);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'An error occurred. Please try again later.']);
    }
}
?>
