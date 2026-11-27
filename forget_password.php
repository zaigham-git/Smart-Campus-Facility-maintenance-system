
<?php

session_start();

require_once 'config/db.php';
require_once 'config/mail.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";

    } else {

        $stmt = $conn->prepare("
            SELECT id FROM users WHERE email = ?
        ");

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $user = $stmt->get_result()->fetch_assoc();

        if ($user) {

            // Remove previous OTP requests for this email
            $delete = $conn->prepare("
                DELETE FROM password_resets WHERE email = ?
            ");

            $delete->bind_param("s", $email);
            $delete->execute();

            // Generate 6 digit OTP
            $otp = (string) random_int(100000, 999999);

            // Hash OTP before saving
            $otpHash = password_hash($otp, PASSWORD_DEFAULT);

            // Expiry after 5 minutes
            $insert = $conn->prepare("
                INSERT INTO password_resets
                (email, otp_hash, expires_at)
                VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 5 MINUTE))
            ");

            $insert->bind_param("ss", $email, $otpHash);

            if ($insert->execute()) {

                if (sendOTPEmail($email, $otp)) {

                    $_SESSION['otp_email'] = $email;

                    header("Location: verify_otp.php");
                    exit();

                } else {

                    $delete = $conn->prepare("
                        DELETE FROM password_resets WHERE email = ?
                    ");

                    $delete->bind_param("s", $email);
                    $delete->execute();

                    $message = "Unable to send email. Please try again later.";
                }
            }

        } else {

            // Do not reveal whether an account exists
            $message = "If this email is registered, an OTP will be sent.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Forgot Password | Smart Campus</title>

    <link rel="stylesheet" href="style.css?v=3">
</head>

<body>

<div class="forgot-container">

    <div class="forgot-card">

        <h2>Forgot Password?</h2>

        <p>Enter your registered email address to receive an OTP.</p>

        <?php if ($message !== ''): ?>
            <div class="forgot-message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label>Email Address</label>

            <input
                type="email"
                name="email"
                placeholder="name@tuf.edu.pk"
                required
            >

            <button type="submit">
                Send OTP
            </button>

        </form>

        <p class="back-login">
            <a href="login.php">Back to Login</a>
        </p>

    </div>

</div>

</body>
</html>
