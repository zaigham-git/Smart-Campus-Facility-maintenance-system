
<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'maintenance') {
    header("Location: ../login.php");
    exit();
}

include '../config/db.php';

$user_id = (int) $_SESSION['user_id'];
$staff_name = $_SESSION['name'];

// Total Assigned Tasks
$totalQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM complaints
    WHERE assigned_to = $user_id
");

$totalTasks = $totalQuery->fetch_assoc()['total'];

// Pending Tasks
$pendingQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM complaints
    WHERE assigned_to = $user_id
    AND status = 'Pending'
");

$pendingTasks = $pendingQuery->fetch_assoc()['total'];

// In Progress Tasks
$progressQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM complaints
    WHERE assigned_to = $user_id
    AND status = 'In Progress'
");

$progressTasks = $progressQuery->fetch_assoc()['total'];

// Resolved Tasks
$resolvedQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM complaints
    WHERE assigned_to = $user_id
    AND status = 'Resolved'
");

$resolvedTasks = $resolvedQuery->fetch_assoc()['total'];

// Recent Assigned Complaints
$recentQuery = $conn->query("
    SELECT c.id, c.category, c.description,
           c.status, c.priority, c.created_at,
           b.building_name, l.location_name
    FROM complaints c
    JOIN locations l ON c.location_id = l.id
    JOIN buildings b ON l.building_id = b.id
    WHERE c.assigned_to = $user_id
    ORDER BY c.id DESC
    LIMIT 5
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Maintenance Dashboard | Smart Campus</title>

    <link rel="stylesheet" href="css/maintenance.css">
</head>

<body>

<div class="dashboard-container">

    <!-- Sidebar -->
    <aside class="sidebar">

        <div class="logo">
            <h2>Smart Campus</h2>
            <p>Maintenance Panel</p>
        </div>

        <nav>
            <a href="dashboard.php" class="active">Dashboard</a>

            <a href="assigned_tasks.php">Assigned Complaints</a>

            <a href="completed_tasks.php">Completed Tasks</a>

            <a href="profile.php">My Profile</a>
        </nav>

        <a href="logout.php" class="logout">Logout</a>

    </aside>


    <!-- Main Content -->
    <main class="main-content">

        <header class="topbar">
            <div>
                <h2>Maintenance Dashboard</h2>
                <p>Manage your assigned campus complaints.</p>
            </div>

            <div class="user-info">
                <div class="avatar">
                    <?php echo strtoupper(substr($staff_name, 0, 1)); ?>
                </div>

                <span>
                    <?php echo htmlspecialchars($staff_name); ?>
                </span>
            </div>
        </header>


        <!-- Welcome Section -->
        <section class="welcome-section">
            <h2>Welcome, <?php echo htmlspecialchars($staff_name); ?>!</h2>
            <p>Here is an overview of your assigned maintenance tasks.</p>
        </section>


        <!-- Statistics Cards -->
        <section class="stats-grid">

            <div class="stat-card purple">
                <h3>Assigned Tasks</h3>
                <h1><?php echo $totalTasks; ?></h1>
                <p>Total complaints assigned to you</p>
            </div>

            <div class="stat-card orange">
                <h3>Pending Tasks</h3>
                <h1><?php echo $pendingTasks; ?></h1>
                <p>Tasks waiting for action</p>
            </div>

            <div class="stat-card teal">
                <h3>In Progress</h3>
                <h1><?php echo $progressTasks; ?></h1>
                <p>Tasks currently being handled</p>
            </div>

            <div class="stat-card green">
                <h3>Resolved Tasks</h3>
                <h1><?php echo $resolvedTasks; ?></h1>
                <p>Successfully completed tasks</p>
            </div>

        </section>


        <!-- Recent Complaints -->
        <section class="table-section">

            <div class="section-heading">
                <div>
                    <h2>Recent Assigned Complaints</h2>
                    <p>Your latest assigned maintenance tasks.</p>
                </div>

                <a href="assigned_tasks.php" class="view-all">
                    View All
                </a>
            </div>

            <div class="table-wrapper">

                <table>
                    <thead>
                        <tr>
                            <th>Complaint ID</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Priority</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if ($recentQuery && $recentQuery->num_rows > 0): ?>

                        <?php while ($row = $recentQuery->fetch_assoc()): ?>

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
                                    <span class="priority">
                                        <?php echo htmlspecialchars($row['priority']); ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="status">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5" class="empty">
                                No complaints assigned to you yet.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>
                </table>

            </div>

        </section>

    </main>

</div>

</body>
</html>
