
<?php

session_start();

require_once 'config/db.php';

if (!isset($_SESSION['otp_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['otp_email'];

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $otp = trim($_POST['otp'] ?? '');

    if (!preg_match('/^[0-9]{6}$/', $otp)) {

        $message = "Please enter a valid 6-digit OTP.";

    } else {

        $stmt = $conn->prepare("
            SELECT id, otp_hash, expires_at, attempts
            FROM password_resets
            WHERE email = ?
            ORDER BY id DESC
            LIMIT 1
        ");

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $record = $stmt->get_result()->fetch_assoc();

        if (!$record) {

            $message = "OTP not found. Request a new OTP.";

        } elseif ($record['attempts'] >= 5) {

            $message = "Too many incorrect attempts. Request a new OTP.";

        } elseif (strtotime($record['expires_at']) < time()) {

            $message = "OTP has expired. Please request a new OTP.";

        } elseif (password_verify($otp, $record['otp_hash'])) {

            $update = $conn->prepare("
                UPDATE password_resets
                SET verified = 1
                WHERE id = ?
            ");

            $update->bind_param("i", $record['id']);
            $update->execute();

            $_SESSION['reset_verified'] = true;

            header("Location: reset_password.php");
            exit();

        } else {

            $update = $conn->prepare("
                UPDATE password_resets
                SET attempts = attempts + 1
                WHERE id = ?
            ");

            $update->bind_param("i", $record['id']);
            $update->execute();

            $message = "Incorrect OTP. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Verify OTP | Smart Campus</title>

    <link rel="stylesheet" href="style.css?v=4">
</head>

<body>

<div class="forgot-container">

    <div class="forgot-card">

        <h2>Verify OTP</h2>

        <p>Enter the 6-digit code sent to your registered email.</p>

        <?php if ($message !== ''): ?>
            <div class="forgot-message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label>Enter OTP</label>

            <input
                type="text"
                name="otp"
                placeholder="123456"
                maxlength="6"
                inputmode="numeric"
                pattern="[0-9]{6}"
                required
            >

            <button type="submit">
                Verify OTP
            </button>

        </form>

    </div>

</div>

</body>
</html>
