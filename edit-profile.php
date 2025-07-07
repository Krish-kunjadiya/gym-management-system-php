<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Ensure member is logged in
if ($_SESSION['role'] !== 'member') {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$message_pwd = '';

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $contact = $conn->real_escape_string($_POST['contact']);
    $sql = "UPDATE members SET contact = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $contact, $user_id);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Profile updated successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error updating profile.</div>";
    }
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $message_pwd = "<div class='alert alert-danger'>New passwords do not match.</div>";
    } else {
        $sql_pass = "SELECT password FROM users WHERE user_id = ?";
        $stmt_pass = $conn->prepare($sql_pass);
        $stmt_pass->bind_param("i", $user_id);
        $stmt_pass->execute();
        $result = $stmt_pass->get_result();
        $user = $result->fetch_assoc();

        if (password_verify($old_password, $user['password'])) {
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql_update_pass = "UPDATE users SET password = ? WHERE user_id = ?";
            $stmt_update_pass = $conn->prepare($sql_update_pass);
            $stmt_update_pass->bind_param("si", $hashed_new_password, $user_id);
            if ($stmt_update_pass->execute()) {
                $message_pwd = "<div class='alert alert-success'>Password changed successfully!</div>";
            } else {
                $message_pwd = "<div class='alert alert-danger'>Error changing password.</div>";
            }
        } else {
            $message_pwd = "<div class='alert alert-danger'>Incorrect old password.</div>";
        }
    }
}

// Fetch current member data to pre-fill the form
$sql_member = "SELECT name, contact FROM members WHERE user_id = ?";
$stmt_member = $conn->prepare($sql_member);
$stmt_member->bind_param("i", $user_id);
$stmt_member->execute();
$member = $stmt_member->get_result()->fetch_assoc();
?>

<h1>Edit My Profile</h1>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">My Details</div>
            <div class="card-body">
                <?php echo $message; ?>
                <form action="edit-profile.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($member['name']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contact" name="contact" value="<?php echo htmlspecialchars($member['contact']); ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary-custom">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Change Password</div>
            <div class="card-body">
                <?php echo $message_pwd; ?>
                <form action="edit-profile.php" method="POST">
                    <div class="mb-3">
                        <label for="old_password" class="form-label">Old Password</label>
                        <input type="password" class="form-control" id="old_password" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary-custom">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>