<?php
require_once 'db_connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

function sendPasswordResetEmail($email, $resetLink) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP(); // Use SMTP
        $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'vb48gointomasterlevel@gmail.com'; // Your Gmail address
        $mail->Password = 'znou lgeq lvwh kwgm'; // Your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption method
        $mail->Port = 587; // SMTP port

        // Recipients
        $mail->setFrom('vb48gointomasterlevel@gmail.com', 'Email System'); // Sender
        $mail->addAddress($email); // Recipient

        // Email content
        $mail->isHTML(true); // Enable HTML email
        $mail->Subject = 'Password Reset Request';
        $mail->Body = "Click the link below to reset your password:<br><a href=\"$resetLink\">$resetLink</a>";
        $mail->AltBody = "Click the link below to reset your password: $resetLink"; // Plain text alternative

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Email could not be sent. Error: {$mail->ErrorInfo}";
    }
}

// Step 1: Request Password Reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_reset'])) {
    $email = $_POST['email'];

    // Check if email exists
    $stmt = $pdo->prepare("SELECT * FROM user_data WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate token
        $token = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare("UPDATE user_data SET reset_token = ? WHERE email = ?");
        $stmt->execute([$token, $email]);

        // Create reset link
        $resetLink = "http://localhost/west2es/pages/reset_password.php?token=$token";

        // Send email
        $emailStatus = sendPasswordResetEmail($email, $resetLink);
        if ($emailStatus === true) {
            // SweetAlert for success
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Password reset link sent to your email.',
                    showConfirmButton: true
                }).then(() => {
                    window.location.href = 'index.php';
                });
            </script>";
        } else {
            // SweetAlert for error
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error sending email: $emailStatus',
                    showConfirmButton: true
                }).then(() => {
                    window.location.href = 'index.php';
                });
            </script>";
        }
    } else {
        // SweetAlert for email not found
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Email not found.',
                showConfirmButton: true
            }).then(() => {
                window.location.href = 'index.php';
            });
        </script>";
    }
}
?>
