
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | Smart Campus</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<div class="layout">

    <aside class="sidebar">
        <div class="brand">Smart Campus</div>

        <div class="user-info">
            <div class="avatar">
                <?= strtoupper(substr($_SESSION['name'], 0, 1)) ?>
            </div>
            <div>
                <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>
                <small>Administrator</small>
            </div>
        </div>

        <nav>
            <a class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"
               href="dashboard.php">Dashboard</a>

            <a class="<?= $current_page == 'complaints.php' ? 'active' : '' ?>"
               href="complaints.php">All Complaints</a>

            <a class="<?= $current_page == 'manage_users.php' ? 'active' : '' ?>"
               href="manage_users.php">Manage Users</a>

            <a class="<?= $current_page == 'assignments.php' ? 'active' : '' ?>"
               href="assignments.php">Assign Complaints</a>

            <a class="<?= $current_page == 'locations.php' ? 'active' : '' ?>"
               href="locations.php">Buildings & Locations</a>

            <a class="<?= $current_page == 'reports.php' ? 'active' : '' ?>"
               href="reports.php">Generate Reports</a>
        </nav>

        <a href="logout.php" class="logout">Logout</a>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <h2>Admin Portal</h2>
            <span>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></span>
        </header>
