
<?php
require_once 'includes/auth.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_building'])) {

        $building = trim($_POST['building_name'] ?? '');

        if ($building === '') {
            $error = "Enter a building name.";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO buildings (building_name) VALUES (?)"
            );
            $stmt->bind_param("s", $building);

            if ($stmt->execute()) {
                $success = "Building added successfully.";
            } else {
                $error = "Could not add building.";
            }
            $stmt->close();
        }

    } elseif (isset($_POST['add_location'])) {

        $building_id = (int)($_POST['building_id'] ?? 0);
        $location = trim($_POST['location_name'] ?? '');

        if ($building_id <= 0 || $location === '') {
            $error = "Select a building and enter a location.";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO locations (building_id, location_name)
                 VALUES (?, ?)"
            );
            $stmt->bind_param("is", $building_id, $location);

            if ($stmt->execute()) {
                $success = "Location added successfully.";
            } else {
                $error = "Could not add location.";
            }
            $stmt->close();
        }
    }
}

$buildings = $conn->query(
    "SELECT id, building_name FROM buildings ORDER BY building_name"
);

$all_locations = $conn->query(
    "SELECT l.location_name, b.building_name
     FROM locations l
     JOIN buildings b ON l.building_id = b.id
     ORDER BY b.building_name"
);

require_once 'includes/header.php';
?>

<section class="page-heading">
    <div>
        <h1>Buildings & Locations</h1>
        <p>Manage campus buildings and their locations.</p>
    </div>
</section>

<?php if ($error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="message success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="two-column">

    <div class="content-card">
        <h3>Add New Building</h3>

        <form method="POST">
            <label>Building Name</label>
            <input type="text" name="building_name"
                   placeholder="Enter building name" required>

            <button type="submit" name="add_building" class="primary-btn">
                Add Building
            </button>
        </form>
    </div>

    <div class="content-card">
        <h3>Add New Location</h3>

        <form method="POST">
            <label>Select Building</label>

            <select name="building_id" required>
                <option value="">Select Building</option>

                <?php while ($b = $buildings->fetch_assoc()): ?>
                    <option value="<?= $b['id'] ?>">
                        <?= htmlspecialchars($b['building_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Location / Room Name</label>
            <input type="text" name="location_name"
                   placeholder="e.g. Computer Lab" required>

            <button type="submit" name="add_location" class="primary-btn">
                Add Location
            </button>
        </form>
    </div>

</div>

<div class="content-card">
    <h3>Existing Campus Locations</h3>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Building</th>
                    <th>Location</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($all_locations->num_rows > 0): ?>
                    <?php while ($loc = $all_locations->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($loc['building_name']) ?></td>
                            <td><?= htmlspecialchars($loc['location_name']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="2">No locations added yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</main>
</div>
</body>
</html>
