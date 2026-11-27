
<?php
require_once 'includes/auth.php';

$stmt = $conn->prepare("
    SELECT c.id, c.category, c.description,
           c.resolution_remarks, c.created_at,
           b.building_name, l.location_name
    FROM complaints c
    JOIN locations l ON c.location_id = l.id
    JOIN buildings b ON l.building_id = b.id
    WHERE c.assigned_to = ?
    AND c.status = 'Resolved'
    ORDER BY c.id ASC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Tasks</title>
    <link rel="stylesheet" href="css/maintenance.css">
</head>
<body>

<div class="dashboard-container">

    <?php include 'includes/header.php'; ?>

    <main class="main-content">

        <div class="topbar">
            <div>
                <h2>Completed Tasks</h2>
                <p>Your successfully resolved complaints.</p>
            </div>
        </div>

        <?php if ($result->num_rows > 0): ?>

            <?php while ($row = $result->fetch_assoc()): ?>

                <div class="task-card">

                    <div class="task-heading">
                        <h3>Complaint #<?php echo $row['id']; ?></h3>
                        <span class="status">Resolved</span>
                    </div>

                    <p><strong>Category:</strong>
                        <?php echo htmlspecialchars($row['category']); ?>
                    </p>

                    <p><strong>Description:</strong>
                        <?php echo nl2br(htmlspecialchars($row['description'])); ?>
                    </p>

                    <p><strong>Location:</strong>
                        <?php echo htmlspecialchars(
                            $row['building_name'] . ' - ' .
                            $row['location_name']
                        ); ?>
                    </p>

                    <p><strong>Resolution Remarks:</strong></p>

                    <p>
                        <?php echo nl2br(htmlspecialchars(
                            $row['resolution_remarks'] ?? ''
                        )); ?>
                    </p>

                    <p><strong>Submitted Date:</strong>
                        <?php echo htmlspecialchars($row['created_at']); ?>
                    </p>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="task-card">
                <p>No completed tasks available.</p>
            </div>

        <?php endif; ?>

    </main>

</div>

</body>
</html>
