<?php
/**
 * Test Admin Email Reset
 * Tests: vb48gointomasterlevel@gmail.com in admin password reset
 */

require_once __DIR__ . '/../functions/PasswordResetManager.php';

echo "<h2>Testing Admin Email Reset</h2>\n";
echo "<hr>\n";

// Test the admin email
$testEmail = 'vb48gointomasterlevel@gmail.com';
$userType = 'admin';

echo "<h3>Test Case: Admin Email in Admin Reset Form</h3>\n";
echo "<strong>Email:</strong> $testEmail<br>\n";
echo "<strong>User Type:</strong> $userType<br>\n";

try {
    // Create PasswordResetManager instance
    $resetManager = new PasswordResetManager($testEmail, $userType);
    
    // Test individual validations
    echo "<br><strong>Step-by-step validation:</strong><br>\n";
    
    // 1. Email format validation
    $emailValid = $resetManager->validateEmailFormat();
    echo "1. <strong>Email Format Validation:</strong> " . ($emailValid ? '✅ Valid' : '❌ Invalid') . "<br>\n";
    
    // 2. User type validation
    $userTypeValid = $resetManager->validateUserType();
    echo "2. <strong>User Type Validation:</strong> " . ($userTypeValid ? '✅ Valid' : '❌ Invalid') . "<br>\n";
    
    // 3. Database verification
    if ($emailValid && $userTypeValid) {
        $userExists = $resetManager->verifyUserInDatabase();
        echo "3. <strong>Database Verification:</strong> " . ($userExists ? '✅ User Found' : '❌ User Not Found') . "<br>\n";
        
        if ($userExists) {
            $user = $resetManager->getUser();
            echo "<br><strong>User Details Found:</strong><br>\n";
            echo "  - ID: {$user['id_no']}<br>\n";
            echo "  - DepEd ID: {$user['deped_id_no']}<br>\n";
            echo "  - Name: {$user['first_name']} {$user['last_name']}<br>\n";
            echo "  - Role: {$user['role']}<br>\n";
            echo "  - Status: {$user['status']}<br>\n";
            echo "  - Has Reset Token: " . (!empty($user['reset_token']) ? 'Yes' : 'No') . "<br>\n";
            
            if (!empty($user['reset_token'])) {
                echo "  - Reset Token: " . substr($user['reset_token'], 0, 20) . "...<br>\n";
            }
            
            // Check for pending reset
            if ($resetManager->hasPendingReset()) {
                echo "<br><strong>⚠️ PENDING RESET DETECTED:</strong> User already has a reset token<br>\n";
                echo "This is why you're getting the 'already requested' message.<br>\n";
            }
        }
    }
    
    // 4. Test complete process
    echo "<br><strong>Complete Process Test:</strong><br>\n";
    $result = $resetManager->processResetRequest();
    echo "  - Status: {$result['status']}<br>\n";
    echo "  - Type: {$result['type']}<br>\n";
    echo "  - Message: {$result['message']}<br>\n";
    
    // 5. Analysis
    echo "<br><strong>Analysis:</strong><br>\n";
    if ($result['status'] === 'error') {
        echo "❌ <strong>ERROR:</strong> " . $result['message'] . "<br>\n";
    } elseif ($result['type'] === 'warning') {
        echo "⚠️ <strong>WARNING:</strong> " . $result['message'] . "<br>\n";
        echo "This means there's already a pending reset token.<br>\n";
    } elseif ($result['status'] === 'success') {
        echo "✅ <strong>SUCCESS:</strong> Reset link would be sent to " . $result['message'] . "<br>\n";
    }
    
} catch (Exception $e) {
    echo "<br><strong>Error:</strong> " . $e->getMessage() . "<br>\n";
}

// Check database directly for reset tokens
echo "<hr>\n";
echo "<h3>Database Check for Reset Tokens</h3>\n";

try {
    global $pdo;
    $stmt = $pdo->prepare("SELECT email, role, status, reset_token FROM user_data WHERE email = ?");
    $stmt->execute([$testEmail]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<strong>Database Record:</strong><br>\n";
        echo "  - Email: {$user['email']}<br>\n";
        echo "  - Role: {$user['role']}<br>\n";
        echo "  - Status: {$user['status']}<br>\n";
        echo "  - Reset Token: " . (!empty($user['reset_token']) ? 'EXISTS' : 'None') . "<br>\n";
        
        if (!empty($user['reset_token'])) {
            echo "  - Token Preview: " . substr($user['reset_token'], 0, 20) . "...<br>\n";
            echo "<br><strong>Solution:</strong> Clear the reset token to allow new reset requests.<br>\n";
        }
    } else {
        echo "<strong>No user found with email:</strong> $testEmail<br>\n";
    }
} catch (Exception $e) {
    echo "<strong>Database Error:</strong> " . $e->getMessage() . "<br>\n";
}

echo "<hr>\n";
echo "<h3>Recommendations</h3>\n";
echo "<ol>\n";
echo "<li><strong>Clear Reset Token:</strong> Remove the existing reset token from the database</li>\n";
echo "<li><strong>Check Email Settings:</strong> Verify SMTP configuration is working</li>\n";
echo "<li><strong>Test Email Delivery:</strong> Check if emails are being sent to spam folder</li>\n";
echo "</ol>\n";
?> 