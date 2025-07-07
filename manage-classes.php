<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Role-based access control for admin/staff
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff') {
    header('Location: index.php');
    exit();
}

$message = '';
// Handle Add/Edit Class
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $schedule_datetime = $_POST['schedule_datetime'];
    $max_capacity = (int)$_POST['max_capacity'];
    $class_id = isset($_POST['class_id']) ? (int)$_POST['class_id'] : 0;

    if ($class_id > 0) { // Update
        $sql = "UPDATE classes SET name=?, description=?, schedule_datetime=?, max_capacity=? WHERE class_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssis", $name, $description, $schedule_datetime, $max_capacity, $class_id);
        $message = "<div class='alert alert-success'>Class updated successfully.</div>";
    } else { // Insert
        $sql = "INSERT INTO classes (name, description, schedule_datetime, max_capacity) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $description, $schedule_datetime, $max_capacity);
        $message = "<div class='alert alert-success'>Class added successfully.</div>";
    }

    if (!$stmt->execute()) {
        $message = "<div class='alert alert-danger'>Error saving class.</div>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $class_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM classes WHERE class_id = $class_id");
    $message = "<div class='alert alert-success'>Class deleted successfully.</div>";
}

// Fetch all classes
$classes_result = $conn->query("SELECT * FROM classes ORDER BY schedule_datetime ASC");
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Manage Classes</h1>
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#classModal">Add New Class</button>
</div>

<?php echo $message; ?>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Schedule</th>
                    <th>Capacity</th>
                    <th>Booked</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($class = $classes_result->fetch_assoc()):
                    // Get booking count for each class
                    $booked_count = $conn->query("SELECT COUNT(*) as count FROM class_bookings WHERE class_id = {$class['class_id']}")->fetch_assoc()['count'];
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($class['name']); ?></td>
                    <td><?php echo date("D, M d, Y - h:i A", strtotime($class['schedule_datetime'])); ?></td>
                    <td><?php echo $class['max_capacity']; ?></td>
                    <td><?php echo $booked_count; ?></td>
                    <td>
                        <a href="manage-classes.php?delete=<?php echo $class['class_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Class Modal -->
<div class="modal fade" id="classModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add/Edit Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="manage-classes.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="class_id" id="class_id">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Schedule Date & Time</label>
                        <input type="datetime-local" class="form-control" name="schedule_datetime" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Max Capacity</label>
                        <input type="number" class="form-control" name="max_capacity" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary-custom">Save Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>