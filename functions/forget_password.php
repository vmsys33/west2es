<?php
/**
 * Password Reset Handler
 * Uses OOP PasswordResetManager class for clean, maintainable code
 * Returns JSON responses for better frontend handling
 */

require_once 'PasswordResetManager.php';

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_reset'])) {
    $email = $_POST['email'] ?? '';
    $userType = $_POST['user_type'] ?? '';
    
    // Set JSON header
    header('Content-Type: application/json');
    
    // Create PasswordResetManager instance
    $resetManager = new PasswordResetManager($email, $userType);
    
    // Process the reset request
    $result = $resetManager->processResetRequest();
    
    // Return JSON response
    echo json_encode($result);
    exit;
}
?>
