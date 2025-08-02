<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Autoload PHPMailer

function sendTestEmail() {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'vb48gointomasterlevel@gmail.com'; // Replace with your Gmail address
        $mail->Password = 'znou lgeq lvwh kwgm'; // Replace with your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('vb48gointomasterlevel@gmail.com', 'Van Baltazar'); // Replace with your details
        $mail->addAddress('vb48gointomasterlevel@gmail.com', 'Van Baltazar'); // Replace with recipient's email and name

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Test Email from PHPMailer';
        $mail->Body = 'This is a test email sent using <b>PHPMailer</b> with Gmail SMTP.';
        $mail->AltBody = 'This is a test email sent using PHPMailer with Gmail SMTP.';

        // Send email
        $mail->send();
        echo 'Test email sent successfully.';
    } catch (Exception $e) {
        echo "Email could not be sent. Error: {$mail->ErrorInfo}";
    }
}

sendTestEmail();
?>
