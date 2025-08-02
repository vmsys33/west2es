<?php
/**
 * Password Reset Manager Class
 * Handles password reset functionality for both admin and faculty users
 * with database verification and OOP principles
 */

require_once 'db_connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../vendor/autoload.php';

class PasswordResetManager {
    private $pdo;
    private $email;
    private $userType;
    private $user;
    
    public function __construct($email, $userType) {
        global $pdo;
        $this->pdo = $pdo;
        $this->email = trim($email);
        $this->userType = strtolower(trim($userType));
        $this->user = null;
    }
    
    /**
     * Validate email format
     */
    public function validateEmailFormat() {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * Validate user type
     */
    public function validateUserType() {
        return in_array($this->userType, ['admin', 'faculty']);
    }
    
    /**
     * Check if email exists in database for the specified user type
     */
    public function verifyUserInDatabase() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM user_data 
                WHERE email = ? AND role = ? AND status = 'active'
            ");
            $stmt->execute([$this->email, $this->userType]);
            $this->user = $stmt->fetch();
            
            return $this->user !== false;
        } catch (PDOException $e) {
            error_log("Database error in verifyUserInDatabase: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if user already has a pending reset token
     */
    public function hasPendingReset() {
        return !empty($this->user['reset_token']);
    }
    
    /**
     * Generate and store reset token
     */
    public function generateResetToken() {
        try {
            $token = bin2hex(random_bytes(32));
            
            $stmt = $this->pdo->prepare("
                UPDATE user_data 
                SET reset_token = ? 
                WHERE email = ?
            ");
            $stmt->execute([$token, $this->email]);
            
            return $token;
        } catch (PDOException $e) {
            error_log("Database error in generateResetToken: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send password reset email
     */
    public function sendResetEmail($token) {
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'vb48gointomasterlevel@gmail.com';
            $mail->Password = 'znou lgeq lvwh kwgm';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            // Recipients
            $mail->setFrom('vb48gointomasterlevel@gmail.com', 'West 2 Elementary School System');
            $mail->addAddress($this->email);
            
            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request - ' . ucfirst($this->userType) . ' Account';
            
            $resetLink = "http://localhost/west2es/pages/reset_password.php?token=$token&user_type={$this->userType}";
            
            $mail->Body = $this->getEmailBody($resetLink);
            $mail->AltBody = $this->getEmailAltBody($resetLink);
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get HTML email body
     */
    private function getEmailBody($resetLink) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background-color: #f8f9fa; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h2 style='color: #0d6efd; margin: 0;'>West 2 Elementary School</h2>
                <p style='color: #6c757d; margin: 10px 0 0 0;'>Password Reset Request</p>
            </div>
            <div style='background-color: #ffffff; padding: 30px; border: 1px solid #dee2e6; border-radius: 0 0 10px 10px;'>
                <h3 style='color: #333; margin-top: 0;'>Hello {$this->user['first_name']}!</h3>
                <p style='color: #555; line-height: 1.6;'>
                    We received a request to reset the password for your <strong>" . ucfirst($this->userType) . "</strong> account.
                </p>
                <p style='color: #555; line-height: 1.6;'>
                    If you didn't make this request, you can safely ignore this email.
                </p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='$resetLink' style='background-color: #0d6efd; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;'>
                        Reset Password
                    </a>
                </div>
                <p style='color: #6c757d; font-size: 14px; margin-top: 30px;'>
                    <strong>Important:</strong>
                </p>
                <ul style='color: #6c757d; font-size: 14px; line-height: 1.6;'>
                    <li>This link will expire in 1 hour for security reasons</li>
                    <li>If the button doesn't work, copy and paste this link: <br><span style='color: #0d6efd; word-break: break-all;'>$resetLink</span></li>
                    <li>For security, this link can only be used once</li>
                </ul>
                <hr style='border: none; border-top: 1px solid #dee2e6; margin: 30px 0;'>
                <p style='color: #6c757d; font-size: 12px; text-align: center; margin: 0;'>
                    This is an automated message. Please do not reply to this email.
                </p>
            </div>
        </div>";
    }
    
    /**
     * Get plain text email body
     */
    private function getEmailAltBody($resetLink) {
        return "Password Reset Request for your " . ucfirst($this->userType) . " account.\n\nClick the link below to reset your password:\n$resetLink\n\nThis link will expire in 1 hour for security reasons.\n\nIf you didn't make this request, you can safely ignore this email.";
    }
    
    /**
     * Process password reset request
     */
    public function processResetRequest() {
        $result = [
            'status' => 'error',
            'message' => '',
            'type' => 'error'
        ];
        
        // Step 1: Validate email format
        if (!$this->validateEmailFormat()) {
            $result['message'] = 'Please enter a valid email address.';
            return $result;
        }
        
        // Step 2: Validate user type
        if (!$this->validateUserType()) {
            $result['message'] = 'Please select a valid user type.';
            return $result;
        }
        
        // Step 3: Verify user exists in database
        if (!$this->verifyUserInDatabase()) {
            $userTypeDisplay = ucfirst($this->userType);
            $result['message'] = "This email is not registered as an active {$userTypeDisplay} user. Please check your email address or contact the system administrator.";
            return $result;
        }
        
        // Step 4: Check for pending reset
        if ($this->hasPendingReset()) {
            $result['type'] = 'warning';
            $result['message'] = 'A password reset has already been requested for this account. Please check your email or wait a few minutes before requesting another reset.';
            return $result;
        }
        
        // Step 5: Generate reset token
        $token = $this->generateResetToken();
        if (!$token) {
            $result['message'] = 'Failed to generate reset token. Please try again later.';
            return $result;
        }
        
        // Step 6: Send reset email
        if (!$this->sendResetEmail($token)) {
            $result['message'] = 'We encountered an error while sending the reset email. Please try again later or contact the system administrator.';
            return $result;
        }
        
        // Success
        $result['status'] = 'success';
        $result['type'] = 'success';
        $result['message'] = $this->email;
        return $result;
    }
    
    /**
     * Get user information
     */
    public function getUser() {
        return $this->user;
    }
    
    /**
     * Get user type
     */
    public function getUserType() {
        return $this->userType;
    }
    
    /**
     * Get email
     */
    public function getEmail() {
        return $this->email;
    }
}
?> 