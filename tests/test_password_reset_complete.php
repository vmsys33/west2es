<?php
/**
 * Complete Password Reset Test
 * Tests the full password reset process including token clearing
 */

require_once __DIR__ . '/../functions/db_connection.php';

echo "<h2>Complete Password Reset Test</h2>\n";
echo "<hr>\n";

$testEmail = 'vb48gointomasterlevel@gmail.com';
$newPassword = 'Vb121212';
$userType = 'admin';

echo "<h3>Test Parameters:</h3>\n";
echo "<strong>Email:</strong> $testEmail<br>\n";
echo "<strong>New Password:</strong> $newPassword<br>\n";
echo "<strong>User Type:</strong> $userType<br>\n";

// Step 1: Check current user status
echo "<hr>\n";
echo "<h3>Step 1: Current User Status</h3>\n";

try {
    $stmt = $pdo->prepare("SELECT id_no, deped_id_no, first_name, last_name, email, role, status, reset_token FROM user_data WHERE email = ?");
    $stmt->execute([$testEmail]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<strong>User Found:</strong><br>\n";
        echo "  - ID: {$user['id_no']}<br>\n";
        echo "  - DepEd ID: {$user['deped_id_no']}<br>\n";
        echo "  - Name: {$user['first_name']} {$user['last_name']}<br>\n";
        echo "  - Email: {$user['email']}<br>\n";
        echo "  - Role: {$user['role']}<br>\n";
        echo "  - Status: {$user['status']}<br>\n";
        echo "  - Reset Token: " . (!empty($user['reset_token']) ? 'EXISTS' : 'None') . "<br>\n";
        
        if (!empty($user['reset_token'])) {
            echo "  - Token Preview: " . substr($user['reset_token'], 0, 20) . "...<br>\n";
        }
    } else {
        echo "<strong>❌ Error:</strong> User not found<br>\n";
        exit;
    }
} catch (Exception $e) {
    echo "<strong>❌ Database Error:</strong> " . $e->getMessage() . "<br>\n";
    exit;
}

// Step 2: Generate a new reset token (simulate password reset request)
echo "<hr>\n";
echo "<h3>Step 2: Generate Reset Token</h3>\n";

try {
    $token = bin2hex(random_bytes(32));
    $stmt = $pdo->prepare("UPDATE user_data SET reset_token = ? WHERE email = ?");
    $stmt->execute([$token, $testEmail]);
    
    echo "<strong>✅ Reset Token Generated:</strong><br>\n";
    echo "  - Token: " . substr($token, 0, 20) . "...<br>\n";
    
    // Verify token was saved
    $stmt = $pdo->prepare("SELECT reset_token FROM user_data WHERE email = ?");
    $stmt->execute([$testEmail]);
    $savedToken = $stmt->fetchColumn();
    
    if ($savedToken === $token) {
        echo "  - ✅ Token saved successfully<br>\n";
    } else {
        echo "  - ❌ Token not saved correctly<br>\n";
    }
    
} catch (Exception $e) {
    echo "<strong>❌ Error generating token:</strong> " . $e->getMessage() . "<br>\n";
    exit;
}

// Step 3: Test password reset process
echo "<hr>\n";
echo "<h3>Step 3: Test Password Reset Process</h3>\n";

try {
    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Verify token and update password
    $stmt = $pdo->prepare("SELECT * FROM user_data WHERE reset_token = ? AND status = 'active'");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<strong>✅ Token Validated:</strong><br>\n";
        echo "  - User ID: {$user['id_no']}<br>\n";
        echo "  - User Role: {$user['role']}<br>\n";
        echo "  - User Status: {$user['status']}<br>\n";
        
        // Check role match
        if ($user['role'] === $userType) {
            echo "  - ✅ Role matches user type<br>\n";
            
            // Update password and clear token
            $updateStmt = $pdo->prepare("UPDATE user_data SET password = ?, reset_token = NULL WHERE reset_token = ?");
            $result = $updateStmt->execute([$hashedPassword, $token]);
            
            if ($result) {
                echo "<strong>✅ Password Updated Successfully:</strong><br>\n";
                echo "  - New password hashed and saved<br>\n";
                echo "  - Reset token cleared<br>\n";
                
                // Verify token was cleared
                $stmt = $pdo->prepare("SELECT reset_token FROM user_data WHERE email = ?");
                $stmt->execute([$testEmail]);
                $clearedToken = $stmt->fetchColumn();
                
                if (empty($clearedToken)) {
                    echo "  - ✅ Token successfully cleared<br>\n";
                } else {
                    echo "  - ❌ Token not cleared properly<br>\n";
                }
                
                // Verify password was updated
                $stmt = $pdo->prepare("SELECT password FROM user_data WHERE email = ?");
                $stmt->execute([$testEmail]);
                $updatedPassword = $stmt->fetchColumn();
                
                if (password_verify($newPassword, $updatedPassword)) {
                    echo "  - ✅ Password updated correctly<br>\n";
                } else {
                    echo "  - ❌ Password not updated correctly<br>\n";
                }
                
            } else {
                echo "<strong>❌ Password Update Failed:</strong><br>\n";
                echo "  - Database update failed<br>\n";
            }
        } else {
            echo "<strong>❌ Role Mismatch:</strong><br>\n";
            echo "  - Expected: $userType<br>\n";
            echo "  - Found: {$user['role']}<br>\n";
        }
    } else {
        echo "<strong>❌ Token Validation Failed:</strong><br>\n";
        echo "  - Token not found or user inactive<br>\n";
    }
    
} catch (Exception $e) {
    echo "<strong>❌ Password Reset Error:</strong> " . $e->getMessage() . "<br>\n";
}

// Step 4: Final verification
echo "<hr>\n";
echo "<h3>Step 4: Final Verification</h3>\n";

try {
    $stmt = $pdo->prepare("SELECT email, role, status, reset_token FROM user_data WHERE email = ?");
    $stmt->execute([$testEmail]);
    $finalUser = $stmt->fetch();
    
    if ($finalUser) {
        echo "<strong>Final User Status:</strong><br>\n";
        echo "  - Email: {$finalUser['email']}<br>\n";
        echo "  - Role: {$finalUser['role']}<br>\n";
        echo "  - Status: {$finalUser['status']}<br>\n";
        echo "  - Reset Token: " . (!empty($finalUser['reset_token']) ? 'STILL EXISTS' : 'None') . "<br>\n";
        
        if (empty($finalUser['reset_token'])) {
            echo "  - ✅ Token properly cleared<br>\n";
        } else {
            echo "  - ❌ Token still exists (should be cleared)<br>\n";
        }
        
        // Test password verification
        $stmt = $pdo->prepare("SELECT password FROM user_data WHERE email = ?");
        $stmt->execute([$testEmail]);
        $currentPassword = $stmt->fetchColumn();
        
        if (password_verify($newPassword, $currentPassword)) {
            echo "  - ✅ New password working correctly<br>\n";
        } else {
            echo "  - ❌ New password not working<br>\n";
        }
    }
    
} catch (Exception $e) {
    echo "<strong>❌ Final Verification Error:</strong> " . $e->getMessage() . "<br>\n";
}

echo "<hr>\n";
echo "<h3>Test Summary</h3>\n";
echo "<p>This test verifies:</p>\n";
echo "<ol>\n";
echo "<li>✅ User exists and is active</li>\n";
echo "<li>✅ Reset token can be generated</li>\n";
echo "<li>✅ Password can be updated</li>\n";
echo "<li>✅ Reset token is cleared after password update</li>\n";
echo "<li>✅ New password works correctly</li>\n";
echo "</ol>\n";

echo "<p><strong>If the test shows 'Token still exists', then there's an issue with the token clearing process.</strong></p>\n";
?> 