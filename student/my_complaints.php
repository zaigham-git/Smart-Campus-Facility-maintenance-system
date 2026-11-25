
<?php
require_once 'includes/auth.php';

$sql = "SELECT c.*, l.location_name, b.building_name
        FROM complaints c
        JOIN locations l ON c.location_id = l.id
        JOIN buildings b ON l.building_id = b.id
        WHERE c.student_id = ?
        ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$complaints = $stmt->get_result();
?>

<?php include 'includes/header.php'; ?>

<div class="topbar">
    <div>
        <h2>My Complaints</h2>
        <p>Track all your submitted complaints.</p>
    </div>

    <a href="report_issue.php" class="primary-btn">
        + New Complaint
    </a>
</div>

<div class="content-card">

    <?php if (isset($_GET['success'])): ?>
        <p style="color:green; margin-bottom:15px;">
            Complaint submitted successfully!
        </p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Location</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        <?php if ($complaints->num_rows > 0): ?>

            <?php while ($row = $complaints->fetch_assoc()): ?>

                <tr>
                    <td>#<?php echo $row['id']; ?></td>

                    <td>
                        <?php echo htmlspecialchars($row['category']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars(
                            $row['building_name'] . ' - ' .
                            $row['location_name']
                        ); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['priority']); ?>
                    </td>

                    <td>
                        <span class="status">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                    </td>

                    <td>
                        <?php echo date(
                            'd M Y',
                            strtotime($row['created_at'])
                        ); ?>
                    </td>

                    <td>
                        <a href="complaint_details.php?id=<?php echo $row['id']; ?>">
                            View Details
                        </a>
                    </td>
                </tr>

            <?php endwhile; ?>

        <?php else: ?>

            <tr>
                <td colspan="7">
                    You have not submitted any complaints yet.
                </td>
            </tr>

        <?php endif; ?>

        </tbody>
    </table>

</div>

<?php include 'includes/footer.php'; ?>
