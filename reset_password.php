
<?php

session_start();

require_once 'config/db.php';

if (
    !isset($_SESSION['otp_email']) ||
    !isset($_SESSION['reset_verified']) ||
    $_SESSION['reset_verified'] !== true
) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['otp_email'];

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 8) {

        $message = "Password must contain at least 8 characters.";

    } elseif ($password !== $confirmPassword) {

        $message = "Passwords do not match.";

    } else {

        // Check that OTP verification is still valid
        $check = $conn->prepare("
            SELECT id
            FROM password_resets
            WHERE email = ?
            AND verified = 1
            AND expires_at > NOW()
            ORDER BY id DESC
            LIMIT 1
        ");

        $check->bind_param("s", $email);
        $check->execute();

        $record = $check->get_result()->fetch_assoc();

        if (!$record) {

            $message = "Your OTP verification has expired. Please try again.";

        } else {

            // Hash new password
            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $update = $conn->prepare("
                UPDATE users
                SET password = ?
                WHERE email = ?
            ");

            $update->bind_param("ss", $hashedPassword, $email);

            if ($update->execute() && $update->affected_rows > 0) {

                // Delete used OTP
                $delete = $conn->prepare("
                    DELETE FROM password_resets
                    WHERE email = ?
                ");

                $delete->bind_param("s", $email);
                $delete->execute();

                unset($_SESSION['otp_email']);
                unset($_SESSION['reset_verified']);

                $_SESSION['password_reset_success'] = true;

                header("Location: login.php");
                exit();

            } else {

                $message = "Unable to update password. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reset Password | Smart Campus</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="forgot-container">

    <div class="forgot-card">

        <h2>Reset Password</h2>

        <p>Create a new password for your Smart Campus account.</p>

        <?php if ($message !== ''): ?>
            <div class="forgot-message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label>New Password</label>

            <input
                type="password"
                name="password"
                placeholder="Enter new password"
                minlength="8"
                required
            >

            <label>Confirm Password</label>

            <input
                type="password"
                name="confirm_password"
                placeholder="Confirm new password"
                minlength="8"
                required
            >

            <button type="submit">
                Reset Password
            </button>

        </form>

    </div>

</div>

</body>
</html>
