
<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$staff_name = $_SESSION['name'] ?? 'Maintenance Staff';
?>

<aside class="sidebar">

    <div class="logo">
        <h2>Smart Campus</h2>
        <p>Maintenance Panel</p>
    </div>

    <nav>
        <a href="dashboard.php"
           class="<?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
           Dashboard
        </a>

        <a href="assigned_tasks.php"
           class="<?php echo $currentPage == 'assigned_tasks.php' ? 'active' : ''; ?>">
           Assigned Complaints
        </a>

        <a href="completed_tasks.php"
           class="<?php echo $currentPage == 'completed_tasks.php' ? 'active' : ''; ?>">
           Completed Tasks
        </a>

        <a href="profile.php"
           class="<?php echo $currentPage == 'profile.php' ? 'active' : ''; ?>">
           My Profile
        </a>
    </nav>

    <a href="logout.php" class="logout">Logout</a>

</aside>
