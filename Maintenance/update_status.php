
<?php
require_once 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: assigned_tasks.php");
    exit();
}

$complaint_id = (int) ($_POST['complaint_id'] ?? 0);
$status = $_POST['status'] ?? '';
$remarks = trim($_POST['resolution_remarks'] ?? '');

if (
    $complaint_id <= 0 ||
    !in_array($status, ['In Progress', 'Resolved'], true) ||
    ($status === 'Resolved' && $remarks === '')
) {
    header("Location: assigned_tasks.php");
    exit();
}

// Update only complaints assigned to this staff member
$stmt = $conn->prepare("
    UPDATE complaints
    SET status = ?, resolution_remarks = ?
    WHERE id = ? AND assigned_to = ?
");

$stmt->bind_param(
    "ssii",
    $status,
    $remarks,
    $complaint_id,
    $user_id
);

$stmt->execute();

if ($stmt->affected_rows > 0) {
    header("Location: assigned_tasks.php?success=1");
} else {
    header("Location: assigned_tasks.php?error=1");
}

exit();
?>
