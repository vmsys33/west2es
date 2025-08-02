<?php
/**
 * Specific test for faculty email in admin reset form
 * Tests: Yvon123@deped.gov.ph in admin password reset
 */

require_once __DIR__ . '/../functions/PasswordResetManager.php';

echo "<h2>Testing Faculty Email in Admin Reset Form</h2>\n";
echo "<hr>\n";

// Test the specific email provided by user
$testEmail = 'Yvon123@deped.gov.ph';
$userType = 'admin'; // Testing admin reset form

echo "<h3>Test Case: Faculty Email in Admin Reset Form</h3>\n";
echo "<strong>Email:</strong> $testEmail<br>\n";
echo "<strong>User Type:</strong> $userType<br>\n";
echo "<strong>Expected Result:</strong> Should show error - email not found as admin<br>\n";

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
        } else {
            echo "<br><strong>Database Query Details:</strong><br>\n";
            echo "  - Looking for: email = '$testEmail' AND role = '$userType' AND status = 'active'<br>\n";
            echo "  - Result: No matching record found<br>\n";
        }
    }
    
    // 4. Test complete process
    echo "<br><strong>Complete Process Test:</strong><br>\n";
    $result = $resetManager->processResetRequest();
    echo "  - Status: {$result['status']}<br>\n";
    echo "  - Type: {$result['type']}<br>\n";
    echo "  - Message: {$result['message']}<br>\n";
    
    // 5. Expected behavior analysis
    echo "<br><strong>Analysis:</strong><br>\n";
    if ($result['status'] === 'error') {
        if (strpos($result['message'], 'not registered as an active Admin user') !== false) {
            echo "✅ <strong>CORRECT BEHAVIOR:</strong> System correctly rejected faculty email for admin reset<br>\n";
        } else {
            echo "⚠️ <strong>UNEXPECTED ERROR:</strong> Different error message than expected<br>\n";
        }
    } else {
        echo "❌ <strong>INCORRECT BEHAVIOR:</strong> System should have rejected this email<br>\n";
    }
    
} catch (Exception $e) {
    echo "<br><strong>Error:</strong> " . $e->getMessage() . "<br>\n";
}

// Also test the same email for faculty reset (should work)
echo "<hr>\n";
echo "<h3>Test Case: Same Email in Faculty Reset Form</h3>\n";
echo "<strong>Email:</strong> $testEmail<br>\n";
echo "<strong>User Type:</strong> faculty<br>\n";
echo "<strong>Expected Result:</strong> Should work if email exists as faculty<br>\n";

try {
    $facultyResetManager = new PasswordResetManager($testEmail, 'faculty');
    $facultyResult = $facultyResetManager->processResetRequest();
    
    echo "<br><strong>Faculty Reset Result:</strong><br>\n";
    echo "  - Status: {$facultyResult['status']}<br>\n";
    echo "  - Type: {$facultyResult['type']}<br>\n";
    echo "  - Message: {$facultyResult['message']}<br>\n";
    
    if ($facultyResult['status'] === 'success') {
        echo "✅ <strong>FACULTY RESET WORKS:</strong> Email is valid for faculty reset<br>\n";
    } else {
        echo "❌ <strong>FACULTY RESET FAILED:</strong> Email not found in faculty records<br>\n";
    }
    
} catch (Exception $e) {
    echo "<br><strong>Error:</strong> " . $e->getMessage() . "<br>\n";
}

echo "<hr>\n";
echo "<h3>Summary</h3>\n";
echo "<p>This test confirms that the OOP implementation correctly:</p>\n";
echo "<ul>\n";
echo "<li>Validates email format</li>\n";
echo "<li>Checks user type (admin/faculty)</li>\n";
echo "<li>Verifies email exists in database for the specific user type</li>\n";
echo "<li>Prevents cross-role password resets</li>\n";
echo "<li>Provides appropriate error messages</li>\n";
echo "</ul>\n";
?> 