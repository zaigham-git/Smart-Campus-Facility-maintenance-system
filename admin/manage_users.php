
<?php
require_once 'includes/auth.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $target_id = (int)($_POST['user_id'] ?? 0);
    $new_role = $_POST['role'] ?? '';

    $allowed_roles = ['student', 'maintenance', 'admin'];

    if ($target_id <= 0 || !in_array($new_role, $allowed_roles, true)) {
        $error = "Invalid user or role selected.";
    } elseif ($target_id === $user_id) {
        $error = "You cannot change your own role.";
    } else {

        $check = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $check->bind_param("i", $target_id);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if (!$exists) {
            $error = "User not found.";
        } else {
            $update = $conn->prepare(
                "UPDATE users SET role = ? WHERE id = ?"
            );
            $update->bind_param("si", $new_role, $target_id);

            if ($update->execute()) {
                $success = "User role updated successfully.";
            } else {
                $error = "Could not update user role.";
            }

            $update->close();
        }
    }
}

$users = $conn->query(
    "SELECT id, name, email, role, created_at
     FROM users ORDER BY id DESC"
);

require_once 'includes/header.php';
?>

<section class="page-heading">
    <div>
        <h1>Manage Users</h1>
        <p>Assign roles to registered users.</p>
    </div>
</section>

<div class="content-card">

    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Current Role</th>
                    <th>Registered</th>
                    <th>Assign Role</th>
                </tr>
            </thead>

            <tbody>
            <?php while ($row = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars(ucfirst($row['role'])) ?></td>
                    <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                    <td>
                        <?php if ($row['id'] == $user_id): ?>
                            <small>Current account</small>
                        <?php else: ?>
                            <form method="POST" class="role-form">
                                <input type="hidden" name="user_id"
                                       value="<?= $row['id'] ?>">

                                <select name="role" required>
                                    <option value="student"
                                        <?= $row['role'] == 'student' ? 'selected' : '' ?>>
                                        Student
                                    </option>

                                    <option value="maintenance"
                                        <?= $row['role'] == 'maintenance' ? 'selected' : '' ?>>
                                        Maintenance
                                    </option>

                                    <option value="admin"
                                        <?= $row['role'] == 'admin' ? 'selected' : '' ?>>
                                        Admin
                                    </option>
                                </select>

                                <button type="submit" class="small-btn">
                                    Update
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</main>
</div>
</body>
</html>
