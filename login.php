
<?php

session_start();
require_once('config/db.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Step 1: Find user by email
    $sql = "SELECT * FROM users WHERE email = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $email);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    // Step 2: Check if user exists
    if ($user = mysqli_fetch_assoc($result)) {

        // Step 3: Verify password
        if (password_verify($password, $user['password'])) {

            // Step 4: Create session
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Step 5: Redirect according to role
            if ($user['role'] == 'student') {

                header("Location: student/dashboard.php");
                exit;

            } elseif ($user['role'] == 'admin') {

                header("Location: admin/dashboard.php");
                exit;

            } elseif ($user['role'] == 'maintenance') {

                header("Location: maintenance/dashboard.php");
                exit;

            } else {

                $error = "Invalid user role.";

            }

        } else {

            $error = "Incorrect email or password.";

        }

    } else {

        $error = "Incorrect email or password.";

    }

    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Campus - Login</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css?v=4">
</head>
<body>

    <div class="login-container">

        <div class="brand-header">
            <div class="brand-logo"> <img src="include/smart campus logo.png" height="100px " alt="Smart Campus Logo"></div>
            <h1>Smart Campus</h1>
            <p>Facility & Maintenance Management Portal</p>
        </div>

        <form method="POST" action="">

            <?php if (!empty($error)) { ?>
                <p style="color: red;">
                    <?php echo htmlspecialchars($error); ?>
                </p>
            <?php } ?>

            <div class="form-group">
                <label for="email">Campus Email</label>

                <div class="input-wrapper">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        placeholder="name@gmail.com"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>

                <div class="input-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="••••••••"
                        required
                    >
                </div>
            </div>

            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember">
                    Remember me
                </label>

                <div class="forgot-link">
    <a href="forget_password.php">Forgot Password?</a>
</div>
            </div>

            <button type="submit" class="btn-submit">
                Sign In to Portal
            </button>

            <p class="signup-prompt">
    Don't have an account? <a href="register.php" class="signup-link">Sign Up</a>
</p>

        </form>

        <div class="footer-text">
            Role-based access for
            <span>Students, Staff & Admin</span>
        </div>

    </div>

</body>
</html>
