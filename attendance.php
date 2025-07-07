<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Role-based access control
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff') {
    header('Location: member-dashboard.php');
    exit();
}

$message = '';
$today = date('Y-m-d');
$filter_date = isset($_GET['date']) ? $_GET['date'] : $today;

// Handle check-in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_in'])) {
    $member_id = (int)$_POST['member_id'];

    // Check if already checked in today
    $check_sql = "SELECT attendance_id FROM attendance WHERE member_id = ? AND date = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("is", $member_id, $today);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $message = "<div class='alert alert-warning'>This member has already checked in today.</div>";
    } else {
        $insert_sql = "INSERT INTO attendance (member_id, date, status) VALUES (?, ?, 'present')";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("is", $member_id, $today);
        if ($stmt_insert->execute()) {
            $message = "<div class='alert alert-success'>Member checked in successfully.</div>";

            require_once 'includes/achievements_engine.php';
        checkAndAwardAchievements($member_id, $conn);
        } else {
            $message = "<div class='alert alert-danger'>Error checking in member.</div>";
        }
    }
}

// Fetch all members for the dropdown
$members_result = $conn->query("SELECT member_id, name FROM members ORDER BY name");

// Fetch attendance for the selected date
$attendance_sql = "SELECT a.date, m.name FROM attendance a JOIN members m ON a.member_id = m.member_id WHERE a.date = ? ORDER BY a.attendance_id DESC";
$stmt_att = $conn->prepare($attendance_sql);
$stmt_att->bind_param("s", $filter_date);
$stmt_att->execute();
$attendance_result = $stmt_att->get_result();

?>
<h1>Attendance Tracking</h1>
<?php echo $message; ?>

<div class="row">
    <!-- Mark Attendance -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Mark Today's Attendance</div>
            <div class="card-body">
                <form action="attendance.php" method="POST">
                    <div class="mb-3">
                        <label for="member_id" class="form-label">Select Member</label>
                        <select name="member_id" class="form-select" required>
                            <option value="">-- Choose Member --</option>
                            <?php while($member = $members_result->fetch_assoc()): ?>
                            <option value="<?php echo $member['member_id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" name="check_in" class="btn btn-primary-custom">Check-In</button>
                </form>
            </div>
        </div>
    </div>

    <!-- View Attendance -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <form class="d-flex" action="attendance.php" method="GET">
                    <label for="date" class="col-form-label me-2">View for Date:</label>
                    <input type="date" name="date" class="form-control me-2" value="<?php echo $filter_date; ?>" onchange="this.form.submit()">
                </form>
            </div>
            <div class="card-body">
                <h5>Attendance for <?php echo date("F d, Y", strtotime($filter_date)); ?></h5>
                <table class="table table-striped">
                    <thead><tr><th>Member Name</th><th>Check-in Date</th></tr></thead>
                    <tbody>
                        <?php if($attendance_result->num_rows > 0): ?>
                            <?php while($row = $attendance_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo date("M d, Y", strtotime($row['date'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="2" class="text-center">No attendance records for this date.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>