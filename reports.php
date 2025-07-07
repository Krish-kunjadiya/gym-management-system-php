<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Role-based access control
if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

// --- Report Queries ---
// 1. Monthly revenue
$monthly_revenue_sql = "SELECT DATE_FORMAT(date, '%Y-%m') as month, SUM(amount) as total_revenue 
                        FROM payments 
                        GROUP BY month 
                        ORDER BY month DESC";
$monthly_revenue_result = $conn->query($monthly_revenue_sql);

// 2. New member registrations per month
$new_members_sql = "SELECT DATE_FORMAT(join_date, '%Y-%m') as month, COUNT(member_id) as new_members 
                    FROM members 
                    GROUP BY month 
                    ORDER BY month DESC";
$new_members_result = $conn->query($new_members_sql);

?>
<h1>Reports</h1>

<!-- Chart.js Integration Placeholder -->
<div class="alert alert-info">
    <strong>For Developers:</strong> To create graphs, you can integrate a library like <a href="https://www.chartjs.org/" target="_blank">Chart.js</a>. 
    You would use PHP to format the data below into a JSON format that Chart.js can understand.
</div>

<div class="row">
    <!-- Monthly Revenue Report -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Monthly Revenue</div>
            <div class="card-body">
                <table class="table">
                    <thead><tr><th>Month</th><th>Total Revenue</th></tr></thead>
                    <tbody>
                        <?php while($row = $monthly_revenue_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date("F Y", strtotime($row['month'] . "-01")); ?></td>
                            <td>&#8377;<?php echo number_format($row['total_revenue'], 2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- New Members Report -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">New Member Registrations</div>
            <div class="card-body">
                <table class="table">
                    <thead><tr><th>Month</th><th>New Members</th></tr></thead>
                    <tbody>
                        <?php while($row = $new_members_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date("F Y", strtotime($row['month'] . "-01")); ?></td>
                            <td><?php echo $row['new_members']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>