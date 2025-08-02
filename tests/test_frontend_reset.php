<?php
/**
 * Frontend Test for Password Reset
 * Tests the actual form submission and response handling
 */

// Simulate a POST request to test the password reset functionality
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['email'] = 'Yvon123@deped.gov.ph';
$_POST['user_type'] = 'admin';
$_POST['request_reset'] = '1';

// Capture the output
ob_start();

// Include the forget_password.php file
include_once __DIR__ . '/../functions/forget_password.php';

// Get the output
$output = ob_get_clean();

echo "<h2>Frontend Password Reset Test</h2>\n";
echo "<hr>\n";

echo "<h3>Test Parameters:</h3>\n";
echo "<strong>Email:</strong> {$_POST['email']}<br>\n";
echo "<strong>User Type:</strong> {$_POST['user_type']}<br>\n";
echo "<strong>Request Reset:</strong> {$_POST['request_reset']}<br>\n";

echo "<h3>Response Output:</h3>\n";
echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #dee2e6;'>\n";
echo "<pre>" . htmlspecialchars($output) . "</pre>\n";
echo "</div>\n";

echo "<h3>Analysis:</h3>\n";

if (strpos($output, 'Swal.fire') !== false) {
    echo "✅ <strong>SweetAlert Response Generated:</strong> The system is generating a proper response<br>\n";
    
    if (strpos($output, 'error') !== false) {
        echo "✅ <strong>Error Response:</strong> System correctly returned an error for invalid admin email<br>\n";
    } elseif (strpos($output, 'success') !== false) {
        echo "⚠️ <strong>Success Response:</strong> System returned success (this might be unexpected)<br>\n";
    }
} else {
    echo "❌ <strong>No SweetAlert Response:</strong> The system is not generating a proper response<br>\n";
}

if (strpos($output, 'Yvon123@deped.gov.ph') !== false) {
    echo "✅ <strong>Email in Response:</strong> The email address is included in the response<br>\n";
}

if (strpos($output, 'not registered as an active Admin user') !== false) {
    echo "✅ <strong>Correct Error Message:</strong> System shows the expected error message<br>\n";
}

echo "<hr>\n";
echo "<h3>Expected Behavior:</h3>\n";
echo "<p>When you click 'Send Reset Link' in the admin password reset form with email 'Yvon123@deped.gov.ph':</p>\n";
echo "<ol>\n";
echo "<li>The form should submit via AJAX to functions/forget_password.php</li>\n";
echo "<li>The system should check if the email exists as an admin user</li>\n";
echo "<li>Since Yvon123@deped.gov.ph is a faculty email, it should return an error</li>\n";
echo "<li>A SweetAlert should appear with the error message</li>\n";
echo "</ol>\n";

echo "<h3>Troubleshooting Steps:</h3>\n";
echo "<ol>\n";
echo "<li><strong>Check Browser Console:</strong> Press F12 and look for JavaScript errors</li>\n";
echo "<li><strong>Check Network Tab:</strong> See if the AJAX request is being sent</li>\n";
echo "<li><strong>Check Response:</strong> Verify the server is returning a response</li>\n";
echo "<li><strong>Check SweetAlert:</strong> Make sure SweetAlert2 is loaded</li>\n";
echo "</ol>\n";
?> 