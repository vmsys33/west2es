<?php
require_once 'db_connection.php';

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Step 2: Reset Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $token = $_POST['token'] ?? null;
    $newPassword = $_POST['new_password'] ?? null;

    if (!$token || !$newPassword) {
        echo "Token or password is missing.";
        exit;
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Verify token
    $stmt = $pdo->prepare("SELECT * FROM user_data WHERE reset_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // Update password

        $stmt = $pdo->prepare("UPDATE user_data SET password = ?, reset_token = '' WHERE reset_token = ?");

        if ($stmt->execute([$hashedPassword, $token])) {
            echo "Password reset successful.";
        } else {
            echo "Failed to update password.";
        }
    } else {
        echo "Invalid or expired token.";
    }
} else {
    echo "Invalid request.";
}
?>


