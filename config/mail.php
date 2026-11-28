
<?php

// Include PHPMailer manually

require_once __DIR__ . '/../include/PHPMailer/Exception.php';
require_once __DIR__ . '/../include/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../include/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function sendOTPEmail($recipientEmail, $otp)
{
    $mail = new PHPMailer(true);

    try {

        // SMTP Settings
        $mail->isSMTP();

        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // Your Gmail Address
        $mail->Username = 'smartcampushelpcenter@gmail.com';

        // Your Gmail App Password
        $mail->Password = 'ttes jnpp zwno nbay';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender Information
        $mail->setFrom(
            'yourgmail@gmail.com',
            'Smart Campus'
        );

        // Receiver Email
        $mail->addAddress($recipientEmail);

        // Email Content
        $mail->isHTML(true);

        $mail->Subject = 'Smart Campus - Password Reset OTP';

        $mail->Body = "
            <div style='font-family:Arial;padding:25px;'>

                <h2 style='color:#4F46E5;'>
                    Smart Campus
                </h2>

                <p>You requested to reset your password.</p>

                <h1 style='color:#4F46E5;
                    letter-spacing:8px;'>
                    $otp
                </h1>

                <p>Your OTP is valid for 5 minutes.</p>

                <p>If you did not request this,
                please ignore this email.</p>

            </div>
        ";

        $mail->AltBody =
            "Your Smart Campus OTP is $otp. Valid for 5 minutes.";

        // Send Email
        $mail->send();

        return true;

    } catch (Exception $e) {

        error_log("Mailer Error: " . $mail->ErrorInfo);

        return false;
    }
}
