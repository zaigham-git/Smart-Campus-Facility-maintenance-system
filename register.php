
<?php

session_start();

require_once 'config/db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Receive form data
    $name = trim($_POST['name']);
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 2. Validate fields
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {

        $error = "All fields are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (strlen($password) < 8) {

        $error = "Password must be at least 8 characters.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {

        // 3. Check if email already exists
        $sql = "SELECT id FROM users WHERE email = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $error = "This email is already registered.";

        } else {

            // 4. Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 5. Insert user into database
            $role = "student";

            $sql = "INSERT INTO users (name, email, password, role)
                    VALUES (?, ?, ?, ?)";

            $insert = $conn->prepare($sql);

            $insert->bind_param(
                "ssss",
                $name,
                $email,
                $hashed_password,
                $role
            );

            if ($insert->execute()) {

                $success = "Registration successful! You can now login.";

            } else {

                $error = "Registration failed. Please try again.";

            }

            $insert->close();
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Smart Campus - Register</title>

    <link rel="stylesheet" href="registercss.css">
</head>

<body>

    <div class="register-container">

        <div class="brand-header">

            <div class="brand-badge">SC</div>

            <h1>Smart Campus</h1>

            <p>Facility & Maintenance Management Portal</p>

        </div>

        <div class="form-heading">
            <h2>Create Account</h2>
            <p>Register as a Student</p>
        </div>

        <?php if (!empty($error)) { ?>

            <div class="message error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php } ?>

        <?php if (!empty($success)) { ?>

            <div class="message success">
                <?php echo htmlspecialchars($success); ?>
                <br>
                <a href="login.php">Go to Login</a>
            </div>

        <?php } ?>

        <form method="POST" action="">

            <div class="form-group">

                <label for="name">Full Name</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your full name"
                    value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label for="email">Campus Email</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="name@tuf.edu.pk"
                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimum 8 characters"
                    minlength="8"
                    required
                >

            </div>

            <div class="form-group">

                <label for="confirm_password">Confirm Password</label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Re-enter your password"
                    required
                >

            </div>

            <button type="submit" class="btn-submit">
                Create Account
            </button>

        </form>

        <div class="footer-text">

            Already have an account?

            <a href="login.php">Login</a>

        </div>

    </div>

</body>
</html>
