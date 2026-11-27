
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'maintenance'
) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../../config/db.php';

$user_id = (int) $_SESSION['user_id'];
?>
