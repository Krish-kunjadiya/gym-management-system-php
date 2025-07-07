<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Role-based access control
if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

$message = '';
// Handle adding new staff
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $position = $conn->real_escape_string($_POST['position']);
    $password = 'staff123'; // Default password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $conn->begin_transaction();
    try {
        $sql_user = "INSERT INTO users (email, password, role) VALUES (?, ?, 'staff')";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("ss", $email, $hashed_password);
        $stmt_user->execute();
        $user_id = $conn->insert_id;

        $sql_staff = "INSERT INTO staff (user_id, name, position) VALUES (?, ?, ?)";
        $stmt_staff = $conn->prepare($sql_staff);
        $stmt_staff->bind_param("iss", $user_id, $name, $position);
        $stmt_staff->execute();

        $conn->commit();
        $message = "<div class='alert alert-success'>New staff member added successfully! Default password is 'staff123'.</div>";
    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        if ($conn->errno === 1062) {
            $message = "<div class='alert alert-danger'>Error: This email is already in use.</div>";
        } else {
            $message = "<div class='alert alert-danger'>An error occurred: " . $e->getMessage() . "</div>";
        }
    }
}

// Fetch staff list
$sql = "SELECT s.staff_id, s.name, s.position, u.email 
        FROM staff s JOIN users u ON s.user_id = u.user_id ORDER BY s.name";
$staff_result = $conn->query($sql);

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Manage Staff</h1>
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addStaffModal">Add New Staff</button>
</div>

<?php echo $message; ?>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr><th>Name</th><th>Position</th><th>Email</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php while ($row = $staff_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['position']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><a href="#" class="btn btn-sm btn-info disabled">Edit</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="manage-staff.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="position" class="form-label">Position (e.g., Trainer, Front Desk)</label>
                        <input type="text" class="form-control" name="position" required>
                    </div>
                    <button type="submit" class="btn btn-primary-custom">Add Staff</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>