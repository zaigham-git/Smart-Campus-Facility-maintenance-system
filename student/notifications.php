
<?php
require_once 'includes/auth.php';

// Mark notification as read
if (isset($_GET['read'])) {

    $notification_id = (int) $_GET['read'];

    $stmt = $conn->prepare(
        "UPDATE notifications
         SET is_read = 1
         WHERE id = ? AND user_id = ?"
    );

    $stmt->bind_param("ii", $notification_id, $user_id);
    $stmt->execute();

    header("Location: notifications.php");
    exit;
}

// Fetch notifications
$sql = "SELECT * FROM notifications
        WHERE user_id = ?
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$notifications = $stmt->get_result();
?>

<?php include 'includes/header.php'; ?>

<div class="topbar">
    <div>
        <h2>Notifications</h2>
        <p>Stay updated about your complaints.</p>
    </div>
</div>

<div class="content-card">

    <?php if ($notifications->num_rows > 0): ?>

        <?php while ($row = $notifications->fetch_assoc()): ?>

            <div style="padding:18px; border-bottom:1px solid #eee;
                        margin-bottom:10px;">

                <p>
                    <?php echo htmlspecialchars($row['message']); ?>
                </p>

                <small style="color:#888;">
                    <?php echo date(
                        'd M Y h:i A',
                        strtotime($row['created_at'])
                    ); ?>
                </small>

                <?php if ($row['is_read'] == 0): ?>

                    <span style="color:#089B91; margin-left:10px;">
                        New
                    </span>

                    <a href="notifications.php?read=<?php echo $row['id']; ?>"
                       style="margin-left:10px;">
                        Mark as Read
                    </a>

                <?php else: ?>

                    <span style="color:#888; margin-left:10px;">
                        Read
                    </span>

                <?php endif; ?>

                <?php if (!empty($row['complaint_id'])): ?>

                    <br><br>

                    <a href="complaint_details.php?id=<?php echo $row['complaint_id']; ?>">
                        View Complaint
                    </a>

                <?php endif; ?>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <p>No notifications available.</p>

    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
