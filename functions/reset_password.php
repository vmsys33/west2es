<?php
require_once 'db_connection.php';

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Step 2: Reset Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $token = $_POST['token'] ?? null;
    $newPassword = $_POST['new_password'] ?? null;
    $userType = $_POST['user_type'] ?? '';

    if (!$token || !$newPassword) {
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Missing Information</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Information',
                    text: 'Token or password is missing. Please use the reset link from your email.',
                    showConfirmButton: true,
                    confirmButtonText: 'Go to Login'
                }).then(() => {
                    window.location.href = '../index.php';
                });
            </script>
        </body>
        </html>";
        exit;
    }

    // Validate password strength
    if (strlen($newPassword) < 8) {
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Weak Password</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Password Too Weak',
                    text: 'Password must be at least 8 characters long.',
                    showConfirmButton: true,
                    confirmButtonText: 'Try Again'
                }).then(() => {
                    window.history.back();
                });
            </script>
        </body>
        </html>";
        exit;
    }

    // Validate user type
    if (!in_array($userType, ['admin', 'faculty'])) {
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Invalid User Type</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid User Type',
                    text: 'The reset link is not valid for this user type.',
                    showConfirmButton: true,
                    confirmButtonText: 'Go to Login'
                }).then(() => {
                    window.location.href = '../index.php';
                });
            </script>
        </body>
        </html>";
        exit;
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Verify token in user_data table with additional checks
    $stmt = $pdo->prepare("SELECT * FROM user_data WHERE reset_token = ? AND status = 'active'");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    // Additional validation: check if user type matches the role
    if ($user && $userType && $user['role'] !== $userType) {
        // Show error message for role mismatch
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Invalid User Type</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid User Type',
                    text: 'The reset link is not valid for this user type.',
                    showConfirmButton: true,
                    confirmButtonText: 'Go to Login'
                }).then(() => {
                    window.location.href = '../index.php';
                });
            </script>
        </body>
        </html>";
        exit;
    }

    if ($user) {
        // Update password in user_data table and clear reset token
        $stmt = $pdo->prepare("UPDATE user_data SET password = ?, reset_token = NULL WHERE reset_token = ?");

        if ($stmt->execute([$hashedPassword, $token])) {
            // Show success message and redirect to homepage
            echo "<!DOCTYPE html>
            <html>
            <head>
                <title>Password Reset Success</title>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            </head>
            <body>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Reset Successful!',
                        html: `
                            <div style='text-align: left;'>
                                <p><strong>Your password has been successfully reset!</strong></p>
                                <p>You can now log in with your new password.</p>
                                <div style='background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; padding: 15px; margin: 15px 0;'>
                                    <p style='margin: 0; color: #155724;'><strong>✓ Password updated successfully</strong></p>
                                    <p style='margin: 5px 0 0 0; color: #155724;'>✓ Reset token cleared for security</p>
                                    <p style='margin: 5px 0 0 0; color: #155724;'>✓ Account: " . ucfirst($userType) . "</p>
                                </div>
                                <p style='color: #6c757d; font-size: 14px;'><em>You will be redirected to the login page in a few seconds...</em></p>
                            </div>
                        `,
                        showConfirmButton: true,
                        confirmButtonText: 'Go to Login',
                        allowOutsideClick: false,
                        width: '500px'
                    }).then(() => {
                        window.location.href = '../index.php';
                    });
                </script>
            </body>
            </html>";
        } else {
            // Show error message
            echo "<!DOCTYPE html>
            <html>
            <head>
                <title>Password Reset Error</title>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            </head>
            <body>
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Reset Failed',
                        text: 'Failed to update password. Please try again or contact the system administrator.',
                        showConfirmButton: true,
                        confirmButtonText: 'Try Again'
                    }).then(() => {
                        window.history.back();
                    });
                </script>
            </body>
            </html>";
        }
    } else {
        // Show invalid token message
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Invalid Token</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid or Expired Token',
                    html: `
                        <div style='text-align: left;'>
                            <p>The password reset link you used is either:</p>
                            <ul style='text-align: left; margin: 10px 0;'>
                                <li>Invalid or has expired</li>
                                <li>Already been used</li>
                                <li>No longer valid</li>
                                <li>Associated with an inactive account</li>
                            </ul>
                            <p><strong>What to do:</strong></p>
                            <p>Please request a new password reset link from the login page.</p>
                        </div>
                    `,
                    showConfirmButton: true,
                    confirmButtonText: 'Go to Login',
                    width: '500px'
                }).then(() => {
                    window.location.href = '../index.php';
                });
            </script>
        </body>
        </html>";
    }
} else {
    // Show invalid request message
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Invalid Request</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Invalid Request',
                text: 'This request is not valid. Please use the password reset link from your email.',
                showConfirmButton: true,
                confirmButtonText: 'Go to Login'
            }).then(() => {
                window.location.href = '../index.php';
            });
        </script>
    </body>
    </html>";
}
?>
