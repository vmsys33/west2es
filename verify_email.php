<?php
/**
 * Email Verification Endpoint
 * Handles email verification for registration
 */

require_once 'functions/EmailVerificationManager.php';

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Email Verification - West 2 Elementary School</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<style>";
echo ".verification-container { max-width: 600px; margin: 50px auto; padding: 20px; }";
echo ".verification-card { border-radius: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }";
echo ".verification-header { background: linear-gradient(135deg, #0d6efd, #0b5ed7); color: white; padding: 30px; border-radius: 15px 15px 0 0; text-align: center; }";
echo ".verification-body { padding: 30px; background: white; border-radius: 0 0 15px 15px; }";
echo ".success-icon { font-size: 4rem; color: #28a745; margin-bottom: 20px; }";
echo ".error-icon { font-size: 4rem; color: #dc3545; margin-bottom: 20px; }";
echo "</style>";
echo "</head>";
echo "<body style='background-color: #f8f9fa;'>";

echo "<div class='verification-container'>";
echo "<div class='verification-card'>";

// Get token from URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    // No token provided
    echo "<div class='verification-header'>";
    echo "<h2>❌ Invalid Verification Link</h2>";
    echo "<p>No verification token provided</p>";
    echo "</div>";
    echo "<div class='verification-body text-center'>";
    echo "<div class='error-icon'>❌</div>";
    echo "<h3>Verification Failed</h3>";
    echo "<p class='text-muted'>The verification link is invalid or missing the required token.</p>";
    echo "<a href='index.php' class='btn btn-primary'>Return to Home</a>";
    echo "</div>";
} else {
    // Process verification
    $verificationManager = new EmailVerificationManager('');
    $result = $verificationManager->verifyToken($token);
    
    if ($result['status'] === 'success') {
        // Verification successful
        $user = $result['user'];
        echo "<div class='verification-header'>";
        echo "<h2>✅ Email Verified Successfully!</h2>";
        echo "<p>Welcome to West 2 Elementary School</p>";
        echo "</div>";
        echo "<div class='verification-body text-center'>";
        echo "<div class='success-icon'>✅</div>";
        echo "<h3>Email Verification Complete!</h3>";
        echo "<p class='text-muted'>Hello <strong>{$user['first_name']} {$user['last_name']}</strong>, your email has been verified successfully.</p>";
        echo "<div class='alert alert-success mt-3'>";
        echo "<strong>Account Status:</strong> Your account is now pending administrator approval.<br>";
        echo "You will receive notification once your account is approved and activated.";
        echo "</div>";
        echo "<p class='text-muted mt-3'>";
        echo "<strong>Next Steps:</strong><br>";
        echo "• Your account will be reviewed by an administrator<br>";
        echo "• You will receive an email notification when approved<br>";
        echo "• Once approved, you can login to access the system";
        echo "</p>";
        echo "<a href='index.php' class='btn btn-success mt-3'>Return to Home</a>";
        echo "</div>";
    } else {
        // Verification failed
        echo "<div class='verification-header'>";
        echo "<h2>❌ Verification Failed</h2>";
        echo "<p>Invalid or expired token</p>";
        echo "</div>";
        echo "<div class='verification-body text-center'>";
        echo "<div class='error-icon'>❌</div>";
        echo "<h3>Verification Failed</h3>";
        echo "<p class='text-muted'>{$result['message']}</p>";
        echo "<div class='alert alert-warning mt-3'>";
        echo "<strong>Possible reasons:</strong><br>";
        echo "• The verification link has expired (24 hours)<br>";
        echo "• The link has already been used<br>";
        echo "• The account is no longer in pending status";
        echo "</div>";
        echo "<p class='text-muted mt-3'>";
        echo "If you need assistance, please contact the system administrator.";
        echo "</p>";
        echo "<a href='index.php' class='btn btn-primary mt-3'>Return to Home</a>";
        echo "</div>";
    }
}

echo "</div>";
echo "</div>";

echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>";
echo "</body>";
echo "</html>";
?>
