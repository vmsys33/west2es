<?php
/**
 * Test script for forget password functionality
 * This script tests the database checks for admin and faculty email verification
 */

require_once '../functions/db_connection.php';

echo "<h2>Testing Forget Password Database Checks</h2>\n";
echo "<hr>\n";

// Test data from the documentation
$testEmails = [
    'admin' => 'vb48gointomasterlevel@gmail.com',
    'faculty' => 'cygnusha@deped.gov.ph'
];

$testDepedIds = [
    'admin' => '461118',
    'faculty' => '237482'
];

function testEmailCheck($pdo, $email, $userType) {
    echo "<h3>Testing $userType Email: $email</h3>\n";
    
    // Check if email exists in user_data table with correct role and active status
    $stmt = $pdo->prepare("SELECT * FROM user_data WHERE email = ? AND role = ? AND status = 'active'");
    $stmt->execute([$email, $userType]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "✅ <strong>SUCCESS:</strong> Email '$email' found as active $userType user<br>\n";
        echo "   - User ID: {$user['id_no']}<br>\n";
        echo "   - DepEd ID: {$user['deped_id_no']}<br>\n";
        echo "   - Name: {$user['first_name']} {$user['last_name']}<br>\n";
        echo "   - Role: {$user['role']}<br>\n";
        echo "   - Status: {$user['status']}<br>\n";
        echo "   - Has reset token: " . (!empty($user['reset_token']) ? 'Yes' : 'No') . "<br>\n";
    } else {
        echo "❌ <strong>FAILED:</strong> Email '$email' not found as active $userType user<br>\n";
        
        // Check if user exists but with different role
        $stmt = $pdo->prepare("SELECT * FROM user_data WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "   - User exists but with role: {$user['role']}<br>\n";
            echo "   - User status: {$user['status']}<br>\n";
        } else {
            echo "   - User does not exist in database<br>\n";
        }
    }
    echo "<br>\n";
}

function testInvalidEmail($pdo, $email, $userType) {
    echo "<h3>Testing Invalid $userType Email: $email</h3>\n";
    
    $stmt = $pdo->prepare("SELECT * FROM user_data WHERE email = ? AND role = ? AND status = 'active'");
    $stmt->execute([$email, $userType]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo "✅ <strong>SUCCESS:</strong> Invalid email '$email' correctly rejected for $userType<br>\n";
    } else {
        echo "❌ <strong>FAILED:</strong> Invalid email '$email' incorrectly accepted for $userType<br>\n";
    }
    echo "<br>\n";
}

// Test valid emails
foreach ($testEmails as $userType => $email) {
    testEmailCheck($pdo, $email, $userType);
}

// Test invalid emails
testInvalidEmail($pdo, 'nonexistent@deped.gov.ph', 'admin');
testInvalidEmail($pdo, 'nonexistent@deped.gov.ph', 'faculty');
testInvalidEmail($pdo, 'admin@wrongdomain.com', 'admin');
testInvalidEmail($pdo, 'faculty@wrongdomain.com', 'faculty');

// Test cross-role validation (admin email for faculty role and vice versa)
echo "<h3>Testing Cross-Role Validation</h3>\n";
foreach ($testEmails as $userType => $email) {
    $otherRole = ($userType === 'admin') ? 'faculty' : 'admin';
    $stmt = $pdo->prepare("SELECT * FROM user_data WHERE email = ? AND role = ? AND status = 'active'");
    $stmt->execute([$email, $otherRole]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo "✅ <strong>SUCCESS:</strong> $userType email '$email' correctly rejected for $otherRole role<br>\n";
    } else {
        echo "❌ <strong>FAILED:</strong> $userType email '$email' incorrectly accepted for $otherRole role<br>\n";
    }
}
echo "<br>\n";

// Test database table structure
echo "<h3>Database Table Structure Check</h3>\n";
try {
    $stmt = $pdo->query("DESCRIBE user_data");
    $columns = $stmt->fetchAll();
    
    $requiredColumns = ['id_no', 'deped_id_no', 'email', 'role', 'status', 'password', 'reset_token'];
    $foundColumns = array_column($columns, 'Field');
    
    foreach ($requiredColumns as $column) {
        if (in_array($column, $foundColumns)) {
            echo "✅ Column '$column' exists<br>\n";
        } else {
            echo "❌ Column '$column' missing<br>\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error checking table structure: " . $e->getMessage() . "<br>\n";
}
echo "<br>\n";

// Test sample data
echo "<h3>Sample Data Check</h3>\n";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM user_data");
    $result = $stmt->fetch();
    echo "Total users in database: {$result['total']}<br>\n";
    
    $stmt = $pdo->query("SELECT role, COUNT(*) as count FROM user_data GROUP BY role");
    $results = $stmt->fetchAll();
    
    foreach ($results as $row) {
        echo "Users with role '{$row['role']}': {$row['count']}<br>\n";
    }
    
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM user_data GROUP BY status");
    $results = $stmt->fetchAll();
    
    foreach ($results as $row) {
        echo "Users with status '{$row['status']}': {$row['count']}<br>\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking sample data: " . $e->getMessage() . "<br>\n";
}

echo "<hr>\n";
echo "<p><strong>Test completed.</strong> Check the results above to verify that the forget password database checks are working correctly.</p>\n";
?> 