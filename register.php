<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Role-based access control
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff') {
    header('Location: member-dashboard.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve form data
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $plan_id = (int)$_POST['plan_id'];
    $join_date = $conn->real_escape_string($_POST['join_date']);
    
    // For simplicity, let's generate a default password. 
    // In a real app, you might email this or have the user set it.
    $password = 'member123'; 
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Start transaction
    $conn->begin_transaction();

    try {
        // 1. Insert into users table
        $sql_user = "INSERT INTO users (email, password, role) VALUES (?, ?, 'member')";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("ss", $email, $hashed_password);
        $stmt_user->execute();
        $user_id = $conn->insert_id;

        // 2. Insert into members table
        $sql_member = "INSERT INTO members (user_id, plan_id, name, contact, join_date) VALUES (?, ?, ?, ?, ?)";
        $stmt_member = $conn->prepare($sql_member);
        $stmt_member->bind_param("iisss", $user_id, $plan_id, $name, $contact, $join_date);
        $stmt_member->execute();

        // If both queries succeed, commit the transaction
        $conn->commit();
        $message = "<div class='alert alert-success'>New member registered successfully! Default password is 'member123'.</div>";

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        // Check for duplicate email error
        if ($conn->errno === 1062) {
             $message = "<div class='alert alert-danger'>Error: This email address is already registered.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . $exception->getMessage() . "</div>";
        }
    }
}

// Fetch plans for the dropdown
$plans_result = $conn->query("SELECT plan_id, name FROM plans ORDER BY name");

?>

<h1>Add New Member</h1>
<?php echo $message; ?>

<div class="card">
    <div class="card-body">
        <form action="register.php" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="contact" class="form-label">Contact Number</label>
                    <input type="text" class="form-control" id="contact" name="contact" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="join_date" class="form-label">Joining Date</label>
                    <input type="date" class="form-control" id="join_date" name="join_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="plan_id" class="form-label">Membership Plan</label>
                <select class="form-select" id="plan_id" name="plan_id" required>
                    <option value="">Select a Plan</option>
                    <?php while($plan = $plans_result->fetch_assoc()): ?>
                        <option value="<?php echo $plan['plan_id']; ?>"><?php echo htmlspecialchars($plan['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <p class="form-text">A default password "member123" will be set for the new member.</p>
            <button type="submit" class="btn btn-primary-custom">Register Member</button>
        </form>
    </div>
</div>


<?php require_once 'includes/footer.php'; ?>