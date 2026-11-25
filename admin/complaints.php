
<?php
require_once 'includes/auth.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $complaint_id = (int)($_POST['complaint_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $priority = $_POST['priority'] ?? '';

    $allowed_status = ['Pending', 'In Progress', 'Resolved'];
    $allowed_priority = ['Low', 'Medium', 'High'];

    if (
        $complaint_id <= 0 ||
        !in_array($status, $allowed_status, true) ||
        !in_array($priority, $allowed_priority, true)
    ) {
        $error = "Invalid complaint details.";
    } else {

        $stmt = $conn->prepare(
            "SELECT student_id, status FROM complaints WHERE id = ?"
        );
        $stmt->bind_param("i", $complaint_id);
        $stmt->execute();
        $complaint = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$complaint) {
            $error = "Complaint not found.";
        } else {
            $old_status = $complaint['status'];

            $update = $conn->prepare(
                "UPDATE complaints SET status = ?, priority = ? WHERE id = ?"
            );
            $update->bind_param("ssi", $status, $priority, $complaint_id);

            if ($update->execute()) {

                if ($old_status !== $status) {
                    $message = "Your complaint #$complaint_id status changed to $status.";

                    $notify = $conn->prepare(
                        "INSERT INTO notifications (user_id, complaint_id, message)
                         VALUES (?, ?, ?)"
                    );
                    $notify->bind_param(
                        "iis",
                        $complaint['student_id'],
                        $complaint_id,
                        $message
                    );
                    $notify->execute();
                    $notify->close();
                }

                $success = "Complaint updated successfully.";
            } else {
                $error = "Could not update complaint.";
            }

            $update->close();
        }
    }
}

$complaints = $conn->query(
    "SELECT c.*, u.name AS student_name,
            l.location_name, b.building_name,
            staff.name AS staff_name
     FROM complaints c
     JOIN users u ON c.student_id = u.id
     JOIN locations l ON c.location_id = l.id
     JOIN buildings b ON l.building_id = b.id
     LEFT JOIN users staff ON c.assigned_to = staff.id
     ORDER BY c.created_at DESC"
);

require_once 'includes/header.php';
?>

<section class="page-heading">
    <div>
        <h1>All Complaints</h1>
        <p>Review complaints and manage their priority and status.</p>
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
                    <th>ID / Student</th>
                    <th>Issue</th>
                    <th>Location</th>
                    <th>Assigned Staff</th>
                    <th>Manage</th>
                </tr>
            </thead>

            <tbody>
            <?php while ($row = $complaints->fetch_assoc()): ?>
                <tr>
                    <td>
                        #<?= $row['id'] ?><br>
                        <?= htmlspecialchars($row['student_name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['category']) ?><br>
                        <small><?= htmlspecialchars($row['priority']) ?> priority</small>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['building_name']) ?><br>
                        <?= htmlspecialchars($row['location_name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['staff_name'] ?? 'Not assigned') ?>
                    </td>

                    <td>
                        <form method="POST" class="manage-form">
                            <input type="hidden" name="complaint_id"
                                   value="<?= $row['id'] ?>">

                            <select name="status">
                                <?php foreach (['Pending', 'In Progress', 'Resolved'] as $s): ?>
                                    <option value="<?= $s ?>"
                                        <?= $row['status'] == $s ? 'selected' : '' ?>>
                                        <?= $s ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <select name="priority">
                                <?php foreach (['Low', 'Medium', 'High'] as $p): ?>
                                    <option value="<?= $p ?>"
                                        <?= $row['priority'] == $p ? 'selected' : '' ?>>
                                        <?= $p ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <button type="submit" class="small-btn">
                                Save
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
