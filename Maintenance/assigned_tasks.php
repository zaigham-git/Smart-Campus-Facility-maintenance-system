
<?php
require_once 'includes/auth.php';

$query = $conn->prepare("
    SELECT c.*, u.name AS student_name,
           b.building_name, l.location_name
    FROM complaints c
    JOIN users u ON c.student_id = u.id
    JOIN locations l ON c.location_id = l.id
    JOIN buildings b ON l.building_id = b.id
    WHERE c.assigned_to = ?
    ORDER BY c.id ASC
");

$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Complaints</title>
    <link rel="stylesheet" href="css/maintenance.css">
</head>
<body>

<div class="dashboard-container">

    <?php include 'includes/header.php'; ?>

    <main class="main-content">

        <div class="topbar">
            <div>
                <h2>Assigned Complaints</h2>
                <p>View and manage your assigned campus complaints.</p>
            </div>
        </div>

        <?php if ($result->num_rows > 0): ?>

            <?php while ($row = $result->fetch_assoc()): ?>

                <div class="task-card">

                    <div class="task-heading">
                        <h3>Complaint #<?php echo $row['id']; ?></h3>

                        <span class="status">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                    </div>

                    <p><strong>Student:</strong>
                        <?php echo htmlspecialchars($row['student_name']); ?>
                    </p>

                    <p><strong>Category:</strong>
                        <?php echo htmlspecialchars($row['category']); ?>
                    </p>

                    <p><strong>Description:</strong></p>

                    <p>
                        <?php echo nl2br(htmlspecialchars($row['description'])); ?>
                    </p>

                    <p><strong>Building:</strong>
                        <?php echo htmlspecialchars($row['building_name']); ?>
                    </p>

                    <p><strong>Location:</strong>
                        <?php echo htmlspecialchars($row['location_name']); ?>
                    </p>

                    <p><strong>Room:</strong>
                        <?php echo htmlspecialchars($row['room'] ?? 'Not specified'); ?>
                    </p>

                    <p><strong>Priority:</strong>
                        <?php echo htmlspecialchars($row['priority']); ?>
                    </p>

                    <?php if (!empty($row['image_path'])): ?>

                        <p><strong>Complaint Image:</strong></p>

                        <img
                            src="../student/<?php echo htmlspecialchars($row['image_path']); ?>"
                            alt="Complaint Image"
                            class="complaint-image"
                        >

                    <?php endif; ?>

                    <p><strong>Submitted:</strong>
                        <?php echo htmlspecialchars($row['created_at']); ?>
                    </p>

                    <form action="update_status.php" method="POST">

                        <input type="hidden" name="complaint_id"
                               value="<?php echo $row['id']; ?>">

                        <label>Update Status</label>

                        <select name="status" required
                            <?php echo $row['status'] == 'Resolved' ? 'disabled' : ''; ?>>

                            <option value="In Progress"
                                <?php echo $row['status'] == 'In Progress' ? 'selected' : ''; ?>>
                                In Progress
                            </option>

                            <option value="Resolved">
                                Resolved
                            </option>

                        </select>

                        <label>Resolution Remarks</label>

                        <textarea
                            name="resolution_remarks"
                            rows="3"
                            placeholder="Describe the work performed..."
                            <?php echo $row['status'] == 'Resolved' ? 'disabled' : ''; ?>
                        ><?php echo htmlspecialchars($row['resolution_remarks'] ?? ''); ?></textarea>

                        <?php if ($row['status'] != 'Resolved'): ?>

                            <button type="submit" class="update-btn">
                                Update Complaint
                            </button>

                        <?php else: ?>

                            <p class="resolved-message">
                                This complaint has been resolved.
                            </p>

                        <?php endif; ?>

                    </form>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="task-card">
                <p>No complaints have been assigned to you yet.</p>
            </div>

        <?php endif; ?>

    </main>

</div>

</body>
</html>
