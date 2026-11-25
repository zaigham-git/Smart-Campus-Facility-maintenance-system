
<?php
require_once 'includes/auth.php';

$complaint_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$sql = "SELECT c.*, l.location_name, b.building_name,
               u.name AS staff_name
        FROM complaints c
        JOIN locations l ON c.location_id = l.id
        JOIN buildings b ON l.building_id = b.id
        LEFT JOIN users u ON c.assigned_to = u.id
        WHERE c.id = ? AND c.student_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $complaint_id, $user_id);
$stmt->execute();

$complaint = $stmt->get_result()->fetch_assoc();

?>

<?php include 'includes/header.php'; ?>

<div class="topbar">
    <div>
        <h2>Complaint Details</h2>
        <p>View your complaint information.</p>
    </div>
</div>

<div class="content-card">

    <?php if (!$complaint): ?>

        <h3>Complaint not found.</h3>

    <?php else: ?>

        <h2>Complaint #<?php echo $complaint['id']; ?></h2>

        <br>

        <p><strong>Category:</strong>
            <?php echo htmlspecialchars($complaint['category']); ?>
        </p>

        <br>

        <p><strong>Description:</strong><br>
            <?php echo nl2br(htmlspecialchars($complaint['description'])); ?>
        </p>

        <br>

        <p><strong>Building:</strong>
            <?php echo htmlspecialchars($complaint['building_name']); ?>
        </p>

        <br>

        <p><strong>Location:</strong>
            <?php echo htmlspecialchars($complaint['location_name']); ?>
        </p>

        <br>

        <p><strong>Room:</strong>
            <?php echo htmlspecialchars($complaint['room'] ?? 'Not specified'); ?>
        </p>

        <br>

        <p><strong>Priority:</strong>
            <?php echo htmlspecialchars($complaint['priority']); ?>
        </p>

        <br>

        <p><strong>Status:</strong>
            <?php echo htmlspecialchars($complaint['status']); ?>
        </p>

        <br>

        <p><strong>Assigned Maintenance Staff:</strong>
            <?php echo htmlspecialchars($complaint['staff_name'] ?? 'Not assigned yet'); ?>
        </p>

        <br>

        <p><strong>Resolution Remarks:</strong><br>
            <?php echo nl2br(htmlspecialchars(
                $complaint['resolution_remarks'] ?? 'No remarks yet.'
            )); ?>
        </p>

        <br>

        <p><strong>Submitted On:</strong>
            <?php echo date('d M Y', strtotime($complaint['created_at'])); ?>
        </p>

        <?php if (!empty($complaint['image_path'])): ?>

            <br>
            <p><strong>Uploaded Image:</strong></p>

            <img
                src="<?php echo htmlspecialchars($complaint['image_path']); ?>"
                alt="Complaint Image"
                style="width:250px; max-width:100%; border-radius:10px;"
            >

        <?php endif; ?>

        <br><br>

        <a href="my_complaints.php" class="primary-btn">
            Back to Complaints
        </a>

    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
