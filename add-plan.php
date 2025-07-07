<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Role-based access control
if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

$message = '';
// Handle form submission for adding a new plan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_plan'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $duration = (int)$_POST['duration'];
    $amount = (float)$_POST['amount'];

    $sql = "INSERT INTO plans (name, duration, amount) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sid", $name, $duration, $amount);
    
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>New plan added successfully.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error adding plan: " . $conn->error . "</div>";
    }
}

// Fetch all plans to display
$plans_result = $conn->query("SELECT * FROM plans ORDER BY name");

?>

<h1>Manage Membership Plans</h1>
<?php echo $message; ?>

<div class="row">
    <!-- Add Plan Form -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Add New Plan</div>
            <div class="card-body">
                <form action="add-plan.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Plan Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="duration" class="form-label">Duration (in days)</label>
                        <input type="number" class="form-control" id="duration" name="duration" required>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (&#8377;)</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                    </div>
                    <button type="submit" name="add_plan" class="btn btn-primary-custom">Add Plan</button>
                </form>
            </div>
        </div>
    </div>

    <!-- List of Plans -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Existing Plans</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Duration (Days)</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($plans_result->num_rows > 0): ?>
                            <?php while($row = $plans_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo $row['duration']; ?></td>
                                    <td>&#8377;<?php echo number_format($row['amount'], 2); ?></td>
                                    <td>
                                        <!-- Add links for edit/delete here -->
                                        <a href="#" class="btn btn-sm btn-info disabled">Edit</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">No plans found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>