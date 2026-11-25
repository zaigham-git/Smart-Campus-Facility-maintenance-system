
<?php
require_once 'includes/auth.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $complaint_id = (int)($_POST['complaint_id'] ?? 0);
    $staff_id = (int)($_POST['staff_id'] ?? 0);

    $check = $conn->prepare(
        "SELECT id FROM users WHERE id = ? AND role = 'maintenance'"
    );
    $check->bind_param("i", $staff_id);
    $check->execute();
    $valid_staff = $check->get_result()->num_rows > 0;
    $check->close();

    if ($complaint_id <= 0 || !$valid_staff) {
        $error = "Please select a valid complaint and maintenance staff.";
    } else {

        $stmt = $conn->prepare(
            "SELECT id FROM complaints WHERE id = ?"
        );
        $stmt->bind_param("i", $complaint_id);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if (!$exists) {
            $error = "Complaint not found.";
        } else {

            $update = $conn->prepare(
                "UPDATE complaints SET assigned_to = ? WHERE id = ?"
            );
            $update->bind_param("ii", $staff_id, $complaint_id);

            if ($update->execute()) {

                $message = "Complaint #$complaint_id has been assigned to you.";

                $notify = $conn->prepare(
                    "INSERT INTO notifications (user_id, complaint_id, message)
                     VALUES (?, ?, ?)"
                );
                $notify->bind_param("iis", $staff_id, $complaint_id, $message);
                $notify->execute();
                $notify->close();

                $success = "Complaint assigned successfully.";
            } else {
                $error = "Could not assign complaint.";
            }

            $update->close();
        }
    }
}

$staff = $conn->query(
    "SELECT id, name FROM users WHERE role = 'maintenance' ORDER BY name"
);

$complaints = $conn->query(
    "SELECT c.id, c.category, c.status, u.name AS student_name,
            staff.name AS staff_name
     FROM complaints c
     JOIN users u ON c.student_id = u.id
     LEFT JOIN users staff ON c.assigned_to = staff.id
     ORDER BY c.created_at DESC"
);

require_once 'includes/header.php';
?>

<section class="page-heading">
    <div>
        <h1>Assign Complaints</h1>
        <p>Assign campus issues to maintenance team members.</p>
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
                    <th>Complaint</th>
                    <th>Student</th>
                    <th>Status</th>
                    <th>Assigned Staff</th>
                    <th>Assign / Reassign</th>
                </tr>
            </thead>

            <tbody>
            <?php while ($row = $complaints->fetch_assoc()): ?>
                <tr>
                    <td>
                        #<?= $row['id'] ?> -
                        <?= htmlspecialchars($row['category']) ?>
                    </td>

                    <td><?= htmlspecialchars($row['student_name']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['staff_name'] ?? 'Not assigned') ?></td>

                    <td>
                        <form method="POST" class="role-form">
                            <input type="hidden" name="complaint_id"
                                   value="<?= $row['id'] ?>">

                            <select name="staff_id" required>
                                <option value="">Select Staff</option>

                                <?php
                                $staff->data_seek(0);
                                while ($person = $staff->fetch_assoc()):
                                ?>
                                    <option value="<?= $person['id'] ?>">
                                        <?= htmlspecialchars($person['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>

                            <button class="small-btn" type="submit">
                                Assign
                            </button>
                        </form>
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
