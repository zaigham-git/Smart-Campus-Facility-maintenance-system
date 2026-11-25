
<?php
require_once 'includes/auth.php';

function getCount($conn, $sql) {
    $result = $conn->query($sql);
    return (int) $result->fetch_assoc()['total'];
}

$total = getCount($conn, "SELECT COUNT(*) AS total FROM complaints");

$pending = getCount($conn,
    "SELECT COUNT(*) AS total FROM complaints WHERE status='Pending'");

$progress = getCount($conn,
    "SELECT COUNT(*) AS total FROM complaints WHERE status='In Progress'");

$resolved = getCount($conn,
    "SELECT COUNT(*) AS total FROM complaints WHERE status='Resolved'");

$students = getCount($conn,
    "SELECT COUNT(*) AS total FROM users WHERE role='student'");

$staff = getCount($conn,
    "SELECT COUNT(*) AS total FROM users WHERE role='maintenance'");

$recent = $conn->query(
    "SELECT c.id, c.category, c.status, c.priority, c.created_at,
            u.name AS student_name
     FROM complaints c
     JOIN users u ON c.student_id = u.id
     ORDER BY c.created_at DESC
     LIMIT 6"
);

require_once 'includes/header.php';
?>

<section class="page-heading">
    <div>
        <h1>Dashboard Overview</h1>
        <p>Monitor campus complaints and system activity.</p>
    </div>
</section>

<div class="stats-grid">

    <div class="stat-card">
        <span>Total Complaints</span>
        <h2><?= $total ?></h2>
    </div>

    <div class="stat-card">
        <span>Pending</span>
        <h2><?= $pending ?></h2>
    </div>

    <div class="stat-card">
        <span>In Progress</span>
        <h2><?= $progress ?></h2>
    </div>

    <div class="stat-card">
        <span>Resolved</span>
        <h2><?= $resolved ?></h2>
    </div>

    <div class="stat-card">
        <span>Total Students</span>
        <h2><?= $students ?></h2>
    </div>

    <div class="stat-card">
        <span>Maintenance Staff</span>
        <h2><?= $staff ?></h2>
    </div>

</div>

<div class="content-card">
    <div class="card-heading">
        <h3>Recent Complaints</h3>
        <a href="complaints.php">View All</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Category</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($recent->num_rows > 0): ?>
                <?php while ($row = $recent->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['student_name']) ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td><?= htmlspecialchars($row['priority']) ?></td>
                        <td>
                            <span class="status <?= strtolower(str_replace(' ', '-', $row['status'])) ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>
                        <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No complaints submitted yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</main>
</div>
</body>
</html>
