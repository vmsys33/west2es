<?php
/**
 * Clear Reset Token Script
 * Clears the reset token for a specific email to allow new password reset requests
 */

require_once __DIR__ . '/../functions/db_connection.php';

$email = 'vb48gointomasterlevel@gmail.com';

echo "<h2>Clear Reset Token</h2>\n";
echo "<hr>\n";

echo "<h3>Clearing reset token for: $email</h3>\n";

try {
    // First, check current status
    $stmt = $pdo->prepare("SELECT email, role, status, reset_token FROM user_data WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<strong>Before clearing:</strong><br>\n";
        echo "  - Email: {$user['email']}<br>\n";
        echo "  - Role: {$user['role']}<br>\n";
        echo "  - Status: {$user['status']}<br>\n";
        echo "  - Reset Token: " . (!empty($user['reset_token']) ? 'EXISTS' : 'None') . "<br>\n";
        
        if (!empty($user['reset_token'])) {
            // Clear the reset token
            $updateStmt = $pdo->prepare("UPDATE user_data SET reset_token = NULL WHERE email = ?");
            $updateStmt->execute([$email]);
            
            echo "<br><strong>✅ Reset token cleared successfully!</strong><br>\n";
            
            // Verify the token was cleared
            $stmt->execute([$email]);
            $userAfter = $stmt->fetch();
            
            echo "<br><strong>After clearing:</strong><br>\n";
            echo "  - Reset Token: " . (!empty($userAfter['reset_token']) ? 'STILL EXISTS' : 'None') . "<br>\n";
            
            if (empty($userAfter['reset_token'])) {
                echo "<br><strong>🎉 Success!</strong> You can now request a new password reset.<br>\n";
            }
        } else {
            echo "<br><strong>No reset token found.</strong> The user is already clear to request a reset.<br>\n";
        }
    } else {
        echo "<strong>❌ Error:</strong> No user found with email: $email<br>\n";
    }
    
} catch (Exception $e) {
    echo "<strong>❌ Database Error:</strong> " . $e->getMessage() . "<br>\n";
}

echo "<hr>\n";
echo "<h3>Next Steps:</h3>\n";
echo "<ol>\n";
echo "<li><strong>Try the password reset again:</strong> Go to admin login → Forgot password → Enter $email</li>\n";
echo "<li><strong>Check your email:</strong> Look in inbox and spam folder</li>\n";
echo "<li><strong>If no email:</strong> Check SMTP settings in the code</li>\n";
echo "</ol>\n";
?> 