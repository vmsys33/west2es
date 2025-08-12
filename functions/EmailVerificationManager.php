<?php
/**
 * Email Verification Manager Class
 * Handles email verification functionality for user registration
 * Uses the same email infrastructure as PasswordResetManager
 */

require_once 'db_connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../vendor/autoload.php';

class EmailVerificationManager {
    private $pdo;
    private $email;
    private $user;
    
    public function __construct($email) {
        global $pdo;
        $this->pdo = $pdo;
        $this->email = trim($email);
        $this->user = null;
    }
    
    /**
     * Validate email format
     */
    public function validateEmailFormat() {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * Check if email exists in database
     */
    public function verifyUserInDatabase() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM user_data 
                WHERE email = ? AND status = 'pending'
            ");
            $stmt->execute([$this->email]);
            $this->user = $stmt->fetch();
            
            return $this->user !== false;
        } catch (PDOException $e) {
            error_log("Database error in verifyUserInDatabase: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if user already has a pending verification token
     */
    public function hasPendingVerification() {
        return !empty($this->user['verification_token']);
    }
    
    /**
     * Generate and store verification token
     */
    public function generateVerificationToken() {
        try {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
            
            $stmt = $this->pdo->prepare("
                UPDATE user_data 
                SET verification_token = ?, verification_expires = ? 
                WHERE email = ?
            ");
            $stmt->execute([$token, $expires, $this->email]);
            
            return $token;
        } catch (PDOException $e) {
            error_log("Database error in generateVerificationToken: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send verification email
     */
    public function sendVerificationEmail($token) {
        $mail = new PHPMailer(true);
        
        try {
            // Server settings (using your existing configuration)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'vb48gointomasterlevel@gmail.com';
            $mail->Password = 'znou lgeq lvwh kwgm';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            // Recipients
            $mail->setFrom('vb48gointomasterlevel@gmail.com', 'West 2 Elementary School System');
            $mail->addAddress($this->email, $this->user['first_name'] . ' ' . $this->user['last_name']);
            
            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Email Verification - West 2 Elementary School';
            
            $verificationLink = "http://localhost/west2es/verify_email.php?token=$token";
            
            $mail->Body = $this->getEmailBody($verificationLink);
            $mail->AltBody = $this->getEmailAltBody($verificationLink);
            
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
    private function getEmailBody($verificationLink) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background-color: #f8f9fa; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h2 style='color: #0d6efd; margin: 0;'>West 2 Elementary School</h2>
                <p style='color: #6c757d; margin: 10px 0 0 0;'>Email Verification Required</p>
            </div>
            <div style='background-color: #ffffff; padding: 30px; border: 1px solid #dee2e6; border-radius: 0 0 10px 10px;'>
                <h3 style='color: #333; margin-top: 0;'>Hello {$this->user['first_name']}!</h3>
                <p style='color: #555; line-height: 1.6;'>
                    Thank you for registering with the <strong>West 2 Elementary School System</strong>.
                </p>
                <p style='color: #555; line-height: 1.6;'>
                    To complete your registration, please verify your email address by clicking the button below.
                </p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='$verificationLink' style='background-color: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;'>
                        Verify Email Address
                    </a>
                </div>
                <p style='color: #6c757d; font-size: 14px; margin-top: 30px;'>
                    <strong>Important:</strong>
                </p>
                <ul style='color: #6c757d; font-size: 14px; line-height: 1.6;'>
                    <li>This link will expire in 24 hours for security reasons</li>
                    <li>If the button doesn't work, copy and paste this link: <br><span style='color: #0d6efd; word-break: break-all;'>$verificationLink</span></li>
                    <li>After verification, your account will be reviewed by an administrator</li>
                    <li>You will receive notification once your account is approved</li>
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
    private function getEmailAltBody($verificationLink) {
        return "Email Verification for West 2 Elementary School System.\n\nHello {$this->user['first_name']}!\n\nThank you for registering with the West 2 Elementary School System.\n\nTo complete your registration, please verify your email address by clicking the link below:\n$verificationLink\n\nThis link will expire in 24 hours for security reasons.\n\nAfter verification, your account will be reviewed by an administrator.\n\nThis is an automated message. Please do not reply to this email.";
    }
    
    /**
     * Process verification request
     */
    public function processVerificationRequest() {
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
        
        // Step 2: Verify user exists in database
        if (!$this->verifyUserInDatabase()) {
            $result['message'] = 'This email is not registered or the account is not in pending status.';
            return $result;
        }
        
        // Step 3: Check if user already has a pending verification
        if ($this->hasPendingVerification()) {
            $result['message'] = 'A verification email has already been sent. Please check your email or wait before requesting another.';
            $result['type'] = 'warning';
            return $result;
        }
        
        // Step 4: Generate verification token
        $token = $this->generateVerificationToken();
        if (!$token) {
            $result['message'] = 'Failed to generate verification token. Please try again.';
            return $result;
        }
        
        // Step 5: Send verification email
        if (!$this->sendVerificationEmail($token)) {
            $result['message'] = 'Failed to send verification email. Please try again.';
            return $result;
        }
        
        // Success
        $result['status'] = 'success';
        $result['message'] = 'Verification email sent successfully. Please check your email and click the verification link.';
        $result['type'] = 'success';
        
        return $result;
    }
    
    /**
     * Verify email token
     */
    public function verifyToken($token) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM user_data 
                WHERE verification_token = ? AND verification_expires > NOW() AND status = 'pending'
            ");
            $stmt->execute([$token]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['status' => 'error', 'message' => 'Invalid or expired verification token.'];
            }
            
            // Update user to verified
            $stmt = $this->pdo->prepare("
                UPDATE user_data 
                SET email_verified = 1, verification_token = NULL, verification_expires = NULL 
                WHERE id_no = ?
            ");
            $stmt->execute([$user['id_no']]);
            
            return [
                'status' => 'success', 
                'message' => 'Email verified successfully! Your account is now pending admin approval.',
                'user' => $user
            ];
            
        } catch (PDOException $e) {
            error_log("Database error in verifyToken: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Database error occurred during verification.'];
        }
    }
    
    /**
     * Get user data
     */
    public function getUser() {
        return $this->user;
    }
    
    /**
     * Get email
     */
    public function getEmail() {
        return $this->email;
    }
}
?>
