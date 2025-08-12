<?php
require_once 'db_connection.php';
require_once 'upload_user_photo.php'; // Include photo upload functions

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $deped_id_no = filter_input(INPUT_POST, 'deped_id_no', FILTER_SANITIZE_NUMBER_INT);  // Use FILTER_SANITIZE_NUMBER_INT for numeric inputs

    function validateName($name) {
        return preg_match("/^[A-Za-zÀ-ž\s-]+$/", $name);
    }

    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'] ?? ''; // Optional field
    $email_prefix = filter_input(INPUT_POST, 'email_prefix', FILTER_SANITIZE_EMAIL);  // Use FILTER_SANITIZE_EMAIL for email prefix
    $email = $email_prefix . "@deped.gov.ph";  // Concatenate to form full email
    $contact_no = $_POST['contact_no'] ?? '';
    $password = $_POST['password'];  // Password input should not be sanitized
    $confirm_password = $_POST['confirm_password'];  // Password input should not be sanitized
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    header('Content-Type: application/json');

    // Validate names
    if (!validateName($last_name) || !validateName($first_name) || (!empty($middle_name) && !validateName($middle_name))) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid name format. Only letters, spaces, and hyphens are allowed.']);
        exit;
    }

    // Validate deped_id_no for exactly 7 digits
    if (!preg_match('/^\d{7}$/', $deped_id_no)) {
        echo json_encode(['status' => 'error', 'message' => 'DepEd ID No. must be exactly 7 digits.']);
        exit;
    }

    // Validate email prefix (no @, not empty, valid chars)
    if (empty($email_prefix) || strpos($email_prefix, '@') !== false || !preg_match('/^[a-zA-Z0-9._-]+$/', $email_prefix)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email prefix. Do not include @ and use only letters, numbers, dot, dash, or underscore.']);
        exit;
    }

    // Validate contact number (10-15 digits, numeric only)
    if (!preg_match('/^\d{10,15}$/', $contact_no)) {
        echo json_encode(['status' => 'error', 'message' => 'Contact number must be 10-15 digits.']);
        exit;
    }

    // Validate password length
    if (strlen($password) < 8) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters.']);
        exit;
    }

    // Validate passwords match
    if ($password !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Check for duplicate DepEd ID No. or email before insert
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_data WHERE deped_id_no = :deped_id_no OR email = :email");
        $stmt->bindParam(':deped_id_no', $deped_id_no);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            // Check which field is duplicate
            $stmt2 = $pdo->prepare("SELECT deped_id_no, email FROM user_data WHERE deped_id_no = :deped_id_no OR email = :email");
            $stmt2->bindParam(':deped_id_no', $deped_id_no);
            $stmt2->bindParam(':email', $email);
            $stmt2->execute();
            while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                if ($row['deped_id_no'] == $deped_id_no) {
                    echo json_encode(['status' => 'error', 'message' => 'The DepEd ID No. is already registered.']);
                    exit;
                }
                if ($row['email'] == $email) {
                    echo json_encode(['status' => 'error', 'message' => 'The email address is already registered.']);
                    exit;
                }
            }
            echo json_encode(['status' => 'error', 'message' => 'A duplicate record exists.']);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'An error occurred while checking for duplicates. Please try again.']);
        exit;
    }

    // Insert the new user into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO user_data (deped_id_no, last_name, first_name, middle_name, email, role, password, contact_no) 
                               VALUES (:deped_id_no, :last_name, :first_name, :middle_name, :email, 'faculty', :password, :contact_no)");
        $stmt->bindParam(':deped_id_no', $deped_id_no);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':middle_name', $middle_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':contact_no', $contact_no);
        $stmt->execute();
        
        // Get the newly inserted user ID
        $newUserId = $pdo->lastInsertId();
        
        // Handle photo upload if provided
        if (!empty($_FILES['photo']['name'])) {
            $photoResult = uploadUserPhoto($newUserId, $_FILES['photo']);
            if (!$photoResult['success']) {
                // If photo upload fails, still create the user but log the error
                error_log("Photo upload failed for user $newUserId: " . $photoResult['message']);
            }
        }

        // Send verification email
        require_once 'EmailVerificationManager.php';
        $verificationManager = new EmailVerificationManager($email);
        $verificationResult = $verificationManager->processVerificationRequest();
        
        if ($verificationResult['status'] === 'success') {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Your account has been created successfully. Please check your email to verify your account before it can be approved by an administrator.'
            ]);
        } else {
            // Account created but email verification failed
            echo json_encode([
                'status' => 'warning', 
                'message' => 'Your account has been created successfully, but there was an issue sending the verification email. Please contact the administrator.'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'An error occurred while saving your data. PDO: ' . $e->getMessage()]);
    }
    exit;
}
?>
