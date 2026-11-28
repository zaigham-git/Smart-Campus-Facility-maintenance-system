
<?php
require_once 'includes/auth.php';

$message = '';

$stmt = $conn->prepare("
    SELECT name, email, created_at
    FROM users
    WHERE id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid name and email.";

    } else {

        $check = $conn->prepare("
            SELECT id FROM users
            WHERE email = ? AND id != ?
        ");

        $check->bind_param("si", $email, $user_id);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {

            $message = "This email is already registered.";

        } else {

            $update = $conn->prepare("
                UPDATE users
                SET name = ?, email = ?
                WHERE id = ?
            ");

            $update->bind_param("ssi", $name, $email, $user_id);

            if ($update->execute()) {

                $_SESSION['name'] = $name;

                $user['name'] = $name;
                $user['email'] = $email;

                $message = "Profile updated successfully!";
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
    <title>My Profile</title>
    <link rel="stylesheet" href="css/maintenance.css?v=4">
</head>
<body>

<div class="dashboard-container">

    <?php include 'includes/header.php'; ?>

    <main class="main-content">

        <div class="topbar">
            <div>
                <h2>My Profile</h2>
                <p>Manage your personal information.</p>
            </div>
        </div>

        <div class="task-card profile-card">

            <h3>Maintenance Staff Information</h3>

            <?php if ($message !== ''): ?>
                <p class="profile-message">
                    <?php echo htmlspecialchars($message); ?>
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

                <label>Account Role</label>

                <input type="text" value="Maintenance" disabled>

                <label>Registered On</label>

                <input type="text"
                    value="<?php echo htmlspecialchars($user['created_at']); ?>"
                    disabled>

                <button type="submit" class="update-btn">
                    Save Changes
                </button>

            </form>

        </div>

    </main>

</div>

</body>
</html>
