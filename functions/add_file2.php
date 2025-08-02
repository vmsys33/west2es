<?php
session_start(); // Start the session
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $deped_id_no = filter_input(INPUT_POST, 'deped_id_no', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    header('Content-Type: application/json');

    if (!$deped_id_no || !$email || !$password) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM user_data WHERE deped_id_no = :deped_id_no AND email = :email");
        $stmt->bindParam(':deped_id_no', $deped_id_no);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                if ($user['role'] === 'admin') {
                    // Set session variables for authentication
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_id'] = $user['id_no'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['email'] = $email;
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];

                    // If file upload is part of this request
                    if (!empty($_FILES['fileInput'])) {
                        $file = $_FILES['fileInput'];

                        // Capture full file name with extension
                        $fileName = basename($file['name']); // Full file name including extension
                        $uploadDir = '../uploads/files/admin_files/';

                        // Ensure upload directory exists
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        // Define the file path
                        $filePath = $uploadDir . $fileName;

                        // Move the uploaded file to the uploads directory
                        if (move_uploaded_file($file['tmp_name'], $filePath)) {
                            // Save full file name including the extension
                            $stmt = $pdo->prepare("INSERT INTO uploaded_files (filename, user_id) VALUES (:filename, :user_id)");
                            $stmt->bindParam(':filename', $fileName);
                            $stmt->bindParam(':user_id', $_SESSION['user_id']);
                            $stmt->execute();

                            echo json_encode(['status' => 'success', 'message' => 'File uploaded and saved successfully.']);
                            exit;
                        } else {
                            echo json_encode(['status' => 'error', 'message' => 'Failed to upload the file.']);
                            exit;
                        }
                    }

                    echo json_encode(['status' => 'success', 'message' => 'Welcome, Admin! Redirecting...']);
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
}
?>
