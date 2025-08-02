<?php
/**
 * Test JSON Response from forget_password.php
 */

function testResponse($email, $userType, $description) {
    echo "<h3>Test: $description</h3>\n";
    echo "<strong>Email:</strong> $email<br>\n";
    echo "<strong>User Type:</strong> $userType<br>\n";
    
    // Simulate POST request
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST['email'] = $email;
    $_POST['user_type'] = $userType;
    $_POST['request_reset'] = '1';
    
    // Capture output
    ob_start();
    include_once __DIR__ . '/../functions/forget_password.php';
    $output = ob_get_clean();
    
    echo "<strong>Response:</strong> " . htmlspecialchars($output) . "<br>\n";
    
    $decoded = json_decode($output, true);
    if ($decoded) {
        echo "<strong>Status:</strong> " . $decoded['status'] . "<br>\n";
        echo "<strong>Type:</strong> " . $decoded['type'] . "<br>\n";
        echo "<strong>Message:</strong> " . $decoded['message'] . "<br>\n";
        
        if ($decoded['status'] === 'error') {
            echo "✅ <strong>Correct:</strong> System returned error as expected<br>\n";
        } elseif ($decoded['status'] === 'success') {
            echo "✅ <strong>Correct:</strong> System returned success as expected<br>\n";
        }
    } else {
        echo "❌ <strong>Error:</strong> Invalid JSON response<br>\n";
    }
    echo "<hr>\n";
}

echo "<h2>JSON Response Tests</h2>\n";
echo "<hr>\n";

// Test 1: Faculty email in admin reset (should fail)
testResponse('Yvon123@deped.gov.ph', 'admin', 'Faculty Email in Admin Reset (Should Fail)');

// Test 2: Admin email in admin reset (should succeed if exists)
testResponse('vb48gointomasterlevel@gmail.com', 'admin', 'Admin Email in Admin Reset (Should Succeed)');

// Test 3: Faculty email in faculty reset (should succeed)
testResponse('Yvon123@deped.gov.ph', 'faculty', 'Faculty Email in Faculty Reset (Should Succeed)');

// Test 4: Invalid email format
testResponse('invalid-email', 'admin', 'Invalid Email Format (Should Fail)');
?> 