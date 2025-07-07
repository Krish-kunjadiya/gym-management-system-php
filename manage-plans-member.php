<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Role-based access control for admin/staff
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff') {
    header('Location: index.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage-members.php');
    exit();
}

$member_id = (int)$_GET['id'];
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_type = $_POST['plan_type']; // 'workout' or 'diet'
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $date_created = date('Y-m-d');

    if ($plan_type === 'workout') {
        $table = 'workouts';
    } else {
        $table = 'diet_plans';
    }

    $sql = "INSERT INTO $table (member_id, title, description, date_created) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $member_id, $title, $description, $date_created);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Plan added successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error adding plan.</div>";
    }
}

// Fetch member details
$member_result = $conn->query("SELECT name FROM members WHERE member_id = $member_id");
$member = $member_result->fetch_assoc();

// Fetch existing plans
$workouts = $conn->query("SELECT * FROM workouts WHERE member_id = $member_id ORDER BY date_created DESC");
$diets = $conn->query("SELECT * FROM diet_plans WHERE member_id = $member_id ORDER BY date_created DESC");
?>

<h1>Manage Plans for <?php echo htmlspecialchars($member['name']); ?></h1>
<a href="manage-members.php" class="btn btn-secondary-custom mb-3">Back to Members</a>

<?php echo $message; ?>

<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Add New Plan</div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Plan Type</label>
                        <select name="plan_type" class="form-select">
                            <option value="workout">Workout Plan</option>
                            <option value="diet">Diet Plan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary-custom">Add Plan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card mb-4">
            <div class="card-header">Assigned Workout Plans</div>
            <ul class="list-group list-group-flush">
                <?php while($plan = $workouts->fetch_assoc()): ?>
                <li class="list-group-item"><strong><?php echo htmlspecialchars($plan['title']); ?>:</strong> <?php echo nl2br(htmlspecialchars($plan['description'])); ?></li>
                <?php endwhile; ?>
                 <?php if($workouts->num_rows === 0) echo "<li class='list-group-item'>No workout plans assigned.</li>"; ?>
            </ul>
        </div>
        <div class="card">
            <div class="card-header">Assigned Diet Plans</div>
            <ul class="list-group list-group-flush">
                 <?php while($plan = $diets->fetch_assoc()): ?>
                <li class="list-group-item"><strong><?php echo htmlspecialchars($plan['title']); ?>:</strong> <?php echo nl2br(htmlspecialchars($plan['description'])); ?></li>
                <?php endwhile; ?>
                <?php if($diets->num_rows === 0) echo "<li class='list-group-item'>No diet plans assigned.</li>"; ?>
            </ul>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>