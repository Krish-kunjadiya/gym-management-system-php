<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Role-based access control
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff') {
    // If not admin or staff, redirect to member dashboard or login
    header('Location: member-dashboard.php');
    exit();
}

// --- Fetch data for summary cards ---

// Total Members
$totalMembersResult = $conn->query("SELECT COUNT(*) as count FROM members");
$totalMembers = $totalMembersResult->fetch_assoc()['count'];

// Total Revenue (This Month)
$currentMonth = date('m');
$currentYear = date('Y');
$revenueResult = $conn->query("SELECT SUM(amount) as total FROM payments WHERE MONTH(date) = $currentMonth AND YEAR(date) = $currentYear");
$totalRevenue = $revenueResult->fetch_assoc()['total'] ?? 0;

// Attendance Today
$today = date('Y-m-d');
$attendanceResult = $conn->query("SELECT COUNT(*) as count FROM attendance WHERE date = '$today'");
$todayAttendance = $attendanceResult->fetch_assoc()['count'];

// Active Plans
$activePlansResult = $conn->query("SELECT COUNT(*) as count FROM plans");
$activePlans = $activePlansResult->fetch_assoc()['count'];

?>

<h1 class="mb-4">Dashboard</h1>

<!-- Summary Cards -->
<!-- Summary Cards -->
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-white" style="background-color: #333138;">
            <div class="card-body text-center">
                <h5 class="card-title">Total Members</h5>
                <p class="card-text fs-2 fw-bold" style="color: #FF312E;"><?php echo $totalMembers; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white" style="background-color: #333138;">
            <div class="card-body text-center">
                <h5 class="card-title">Revenue (This Month)</h5>
                <p class="card-text fs-2 fw-bold" style="color: #FF312E;">₹<?php echo number_format($totalRevenue, 0); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white" style="background-color: #333138;">
            <div class="card-body text-center">
                <h5 class="card-title">Attendance Today</h5>
                <p class="card-text fs-2 fw-bold" style="color: #FF312E;"><?php echo $todayAttendance; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white" style="background-color: #333138;">
            <div class="card-body text-center">
                <h5 class="card-title">Active Plans</h5>
                <p class="card-text fs-2 fw-bold" style="color: #FF312E;"><?php echo $activePlans; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Placeholder for more content like charts or recent activity -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Recent Activity</h5>
        <p>Future content area for recent member registrations, payments, etc.</p>
    </div>
</div>


<?php require_once 'includes/footer.php'; ?>