
<?php
require_once 'includes/auth.php';

$message = "";
$error = "";

// Fetch buildings and locations
$sql = "SELECT locations.id, locations.location_name,
               buildings.building_name
        FROM locations
        JOIN buildings ON locations.building_id = buildings.id
        ORDER BY buildings.building_name";

$locations = $conn->query($sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $location_id = (int) $_POST['location_id'];
    $room = trim($_POST['room']);

    $allowed_categories = [
        'Electrical', 'Plumbing', 'Furniture',
        'Cleaning', 'Other'
    ];

    if (
        !in_array($category, $allowed_categories) ||
        empty($description) ||
        $location_id <= 0
    ) {
        $error = "Please fill all required fields correctly.";
    } else {

        // Check selected location exists
        $check = $conn->prepare(
            "SELECT id FROM locations WHERE id = ?"
        );
        $check->bind_param("i", $location_id);
        $check->execute();

        if ($check->get_result()->num_rows === 0) {
            $error = "Invalid location selected.";
        } else {

            $image_path = NULL;

            // Image upload
            if (
                isset($_FILES['issue_image']) &&
                $_FILES['issue_image']['error'] !== UPLOAD_ERR_NO_FILE
            ) {

                if ($_FILES['issue_image']['error'] !== UPLOAD_ERR_OK) {
                    $error = "Image upload failed.";
                } elseif ($_FILES['issue_image']['size'] > 3 * 1024 * 1024) {
                    $error = "Image size must be less than 3 MB.";
                } else {

                    $tmp = $_FILES['issue_image']['tmp_name'];
                    $imageInfo = getimagesize($tmp);

                    $allowed_types = [
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/webp' => 'webp'
                    ];

                    $mime = $imageInfo['mime'] ?? '';

                    if (!isset($allowed_types[$mime])) {
                        $error = "Only JPG, PNG and WEBP images are allowed.";
                    } else {

                        $uploadDir = __DIR__ . '/uploads/';

                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        $fileName = bin2hex(random_bytes(16))
                                  . '.' . $allowed_types[$mime];

                        if (move_uploaded_file(
                            $tmp,
                            $uploadDir . $fileName
                        )) {
                            $image_path = 'uploads/' . $fileName;
                        } else {
                            $error = "Unable to save image.";
                        }
                    }
                }
            }

            if (empty($error)) {

                $sql = "INSERT INTO complaints
                        (student_id, category, description,
                         image_path, location_id, room)
                        VALUES (?, ?, ?, ?, ?, ?)";

                $stmt = $conn->prepare($sql);

                $stmt->bind_param(
                    "isssis",
                    $user_id,
                    $category,
                    $description,
                    $image_path,
                    $location_id,
                    $room
                );

                if ($stmt->execute()) {
                    header("Location: my_complaints.php?success=1");
                    exit;
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="topbar">
    <div>
        <h2>Report New Issue</h2>
        <p>Submit a campus maintenance complaint.</p>
    </div>
</div>

<div class="content-card">

    <?php if ($error): ?>
        <p style="color:red; margin-bottom:15px;">
            <?php echo htmlspecialchars($error); ?>
        </p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <label>Issue Category *</label>
        <select name="category" required>
            <option value="">Select Category</option>
            <option value="Electrical">Electrical</option>
            <option value="Plumbing">Plumbing</option>
            <option value="Furniture">Furniture</option>
            <option value="Cleaning">Cleaning</option>
            <option value="Other">Other</option>
        </select>

        <label>Issue Description *</label>
        <textarea name="description" rows="5"
                  placeholder="Describe your issue..."
                  required></textarea>

        <label>Building / Location *</label>
        <select name="location_id" required>
            <option value="">Select Location</option>

            <?php while ($loc = $locations->fetch_assoc()): ?>
                <option value="<?php echo $loc['id']; ?>">
                    <?php echo htmlspecialchars(
                        $loc['building_name'] . ' - ' .
                        $loc['location_name']
                    ); ?>
                </option>
            <?php endwhile; ?>

        </select>

        <label>Room Number</label>
        <input type="text" name="room"
               placeholder="Enter room number">

        <label>Upload Issue Image</label>
        <input type="file" name="issue_image"
               accept=".jpg,.jpeg,.png,.webp">

        <button type="submit" class="primary-btn">
            Submit Complaint
        </button>

    </form>

</div>

<?php include 'includes/footer.php'; ?>
