
<?php
require_once 'includes/auth.php';

// Complaint statistics
$sql = "SELECT
        COUNT(*) AS total,
        SUM(status = 'Pending') AS pending,
        SUM(status = 'In Progress') AS in_progress,
        SUM(status = 'Resolved') AS resolved
        FROM complaints
        WHERE student_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$stats = $stmt->get_result()->fetch_assoc();

// Recent complaints
$sql = "SELECT id, category, status, priority, created_at
        FROM complaints
        WHERE student_id = ?
        ORDER BY created_at DESC
        LIMIT 5";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$complaints = $stmt->get_result();
?>

<?php include 'includes/header.php'; ?>

<div class="topbar">
    <div>
        <h2>Student Dashboard</h2>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
    </div>

    <div class="user-info">
        Student Portal
    </div>
</div>

<div class="welcome-card">
    <div>
        <h2>Welcome to Smart Campus!</h2>
        <p>Report campus issues and track their progress easily.</p>
        <a href="report_issue.php" class="primary-btn">
            + Report New Issue
        </a>
    </div>
</div>

<div class="stats-container">

    <div class="stat-card">
        <h3>Total Complaints</h3>
        <h1><?php echo $stats['total'] ?? 0; ?></h1>
    </div>

    <div class="stat-card pending">
        <h3>Pending</h3>
        <h1><?php echo $stats['pending'] ?? 0; ?></h1>
    </div>

    <div class="stat-card progress">
        <h3>In Progress</h3>
        <h1><?php echo $stats['in_progress'] ?? 0; ?></h1>
    </div>

    <div class="stat-card resolved">
        <h3>Resolved</h3>
        <h1><?php echo $stats['resolved'] ?? 0; ?></h1>
    </div>

</div>

<div class="content-card">

    <div class="section-heading">
        <h2>Recent Complaints</h2>
        <a href="my_complaints.php">View All</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Category</th>
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
                        <?php echo htmlspecialchars($row['priority']); ?>
                    </td>

                    <td>
                        <span class="status">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                    </td>

                    <td>
                        <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                    </td>

                    <td>
                        <a href="complaint_details.php?id=<?php echo $row['id']; ?>">
                            View
                        </a>
                    </td>
                </tr>

            <?php endwhile; ?>

        <?php else: ?>

            <tr>
                <td colspan="6">You have not submitted any complaints yet.</td>
            </tr>

        <?php endif; ?>

        </tbody>
    </table>

</div>

<?php include 'includes/footer.php'; ?>
