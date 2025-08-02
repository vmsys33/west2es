<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $deped_id_no = filter_input(INPUT_POST, 'deped_id_no', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    header('Content-Type: application/json');

    // Validate inputs
    if (!$deped_id_no || !$email || !$password) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    try {
        // Check if user exists and is an admin
        $stmt = $pdo->prepare("SELECT password, role FROM user_data WHERE deped_id_no = :deped_id_no AND email = :email");
        $stmt->bindParam(':deped_id_no', $deped_id_no);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Check if role is admin
                if ($user['role'] === 'admin') {
                    echo json_encode(['status' => 'success', 'message' => 'Login successful. Redirecting...']);
                    exit;
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Access denied. Only admins can log in.']);
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
    exit;
}
?>
