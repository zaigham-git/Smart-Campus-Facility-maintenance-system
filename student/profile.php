
<?php
require_once 'includes/auth.php';

$error = "";
$success = "";

$stmt = $conn->prepare(
    "SELECT name, email FROM users WHERE id = ?"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid name and email.";

    } else {

        // Check whether email belongs to another user
        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ? AND id != ?"
        );
        $check->bind_param("si", $email, $user_id);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {

            $error = "This email is already registered.";

        } else {

            $update = $conn->prepare(
                "UPDATE users SET name = ?, email = ? WHERE id = ?"
            );

            $update->bind_param("ssi", $name, $email, $user_id);

            if ($update->execute()) {

                $_SESSION['name'] = $name;

                $user['name'] = $name;
                $user['email'] = $email;

                $success = "Profile updated successfully.";

            } else {
                $error = "Unable to update profile.";
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="topbar">
    <div>
        <h2>My Profile</h2>
        <p>Manage your account information.</p>
    </div>
</div>

<div class="content-card">

    <?php if ($error): ?>
        <p style="color:red; margin-bottom:15px;">
            <?php echo htmlspecialchars($error); ?>
        </p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green; margin-bottom:15px;">
            <?php echo htmlspecialchars($success); ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <label>Full Name</label>
        <input type="text" name="name"
               value="<?php echo htmlspecialchars($user['name']); ?>"
               required>

        <label>Email Address</label>
        <input type="email" name="email"
               value="<?php echo htmlspecialchars($user['email']); ?>"
               required>

        <br><br>

        <button type="submit" class="primary-btn">
            Update Profile
        </button>

    </form>

</div>

<?php include 'includes/footer.php'; ?>
