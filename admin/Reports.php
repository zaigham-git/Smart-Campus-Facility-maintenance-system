
<?php
require_once 'includes/auth.php';

if (isset($_GET['download']) && $_GET['download'] === 'csv') {

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=campus_complaints_report.csv');

    $output = fopen('php://output', 'w');

    fputcsv($output, [
        'Complaint ID', 'Student', 'Category', 'Building',
        'Location', 'Status', 'Priority', 'Date'
    ]);

    $result = $conn->query(
    "SELECT c.id, u.name, c.category, c.description,
            b.building_name, l.location_name,
            c.status, c.priority, c.created_at
     FROM complaints c
     JOIN users u ON c.student_id = u.id
     JOIN locations l ON c.location_id = l.id
     JOIN buildings b ON l.building_id = b.id
     ORDER BY c.id ASC"
);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}

function reportCount($conn, $status) {
    $stmt = $conn->prepare(
        "SELECT COUNT(*) AS total FROM complaints WHERE status = ?"
    );
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    return $count;
}

$total = $conn->query(
    "SELECT COUNT(*) AS total FROM complaints"
)->fetch_assoc()['total'];

$pending = reportCount($conn, 'Pending');
$progress = reportCount($conn, 'In Progress');
$resolved = reportCount($conn, 'Resolved');

require_once 'includes/header.php';
?>

<section class="page-heading">
    <div>
        <h1>Reports & Analytics</h1>
        <p>Review campus complaint statistics and export records.</p>
    </div>

    <a href="reports.php?download=csv" class="primary-btn">
        Download CSV Report
    </a>
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
</div>

<div class="content-card">
    <h3>Complaint Status Summary</h3>
    <p>Total submitted complaints: <strong><?= $total ?></strong></p>
    <p>Pending complaints: <strong><?= $pending ?></strong></p>
    <p>In Progress complaints: <strong><?= $progress ?></strong></p>
    <p>Resolved complaints: <strong><?= $resolved ?></strong></p>
</div>

</main>
</div>
</body>
</html>
