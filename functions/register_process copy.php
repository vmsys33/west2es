<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $deped_id_no = filter_input(INPUT_POST, 'deped_id_no', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $middle_name = filter_input(INPUT_POST, 'middle_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    header('Content-Type: application/json');

    // Validate passwords
    if ($password !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Insert the new user
        $stmt = $pdo->prepare("INSERT INTO user_data (deped_id_no, last_name, first_name, middle_name, email, role, password) 
                               VALUES (:deped_id_no, :last_name, :first_name, :middle_name, :email, :role, :password)");
        $stmt->bindParam(':deped_id_no', $deped_id_no);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':middle_name', $middle_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();

        // Send success response
        echo json_encode(['status' => 'success', 'message' => 'Your account has been created successfully.']);
    } catch (PDOException $e) {
        // Handle duplicate entry errors
        if ($e->errorInfo[1] == 1062) { // MySQL error code for duplicate entry
            // Check which field caused the duplicate
            if (strpos($e->getMessage(), 'deped_id_no') !== false) {
                echo json_encode(['status' => 'error', 'message' => 'The DepEd ID No. is already registered.']);
            } elseif (strpos($e->getMessage(), 'email') !== false) {
                echo json_encode(['status' => 'error', 'message' => 'The email address is already registered.']);
            } elseif (strpos($e->getMessage(), 'first_name') !== false && strpos($e->getMessage(), 'last_name') !== false && strpos($e->getMessage(), 'middle_name') !== false) {
                echo json_encode(['status' => 'error', 'message' => 'A user with the same name (First, Middle, Last) is already registered.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'A duplicate record exists.']);
            }
        } else {
            // Handle other database errors
            echo json_encode(['status' => 'error', 'message' => 'An error occurred while saving your data. Please try again.']);
        }
    }
    exit;
}
?>
