<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Role-based access control
if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

$message = '';
// Handle adding a new payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payment'])) {
    $member_id = (int)$_POST['member_id'];
    $amount = (float)$_POST['amount'];
    $date = $_POST['date'];
    $method = $conn->real_escape_string($_POST['method']);

    $sql = "INSERT INTO payments (member_id, amount, date, method) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idss", $member_id, $amount, $date, $method);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Payment recorded successfully.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error recording payment.</div>";
    }
}

// Fetch members for dropdown
$members_result = $conn->query("SELECT member_id, name FROM members ORDER BY name");
// Fetch all payments
$payments_sql = "SELECT p.payment_id, p.amount, p.date, p.method, m.name as member_name 
                 FROM payments p 
                 JOIN members m ON p.member_id = m.member_id 
                 ORDER BY p.date DESC";
$payments_result = $conn->query($payments_sql);
?>

<h1>Payment Management</h1>
<?php echo $message; ?>

<div class="row">
    <!-- Add Payment Form -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Record New Payment</div>
            <div class="card-body">
                <form action="payments.php" method="POST">
                    <div class="mb-3">
                        <label for="member_id" class="form-label">Member</label>
                        <select name="member_id" class="form-select" required>
                            <option value="">-- Choose Member --</option>
                            <?php while($m = $members_result->fetch_assoc()): ?>
                            <option value="<?php echo $m['member_id']; ?>"><?php echo htmlspecialchars($m['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (&#8377;)</label>
                        <input type="number" step="0.01" class="form-control" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Payment Date</label>
                        <input type="date" class="form-control" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="method" class="form-label">Payment Method</label>
                        <select name="method" class="form-select">
                            <option>Cash</option>
                            <option>Credit Card</option>
                            <option>Bank Transfer</option>
                        </select>
                    </div>
                    <button type="submit" name="add_payment" class="btn btn-primary-custom">Add Payment</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Payment History Table -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Payment History</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead><tr><th>Member</th><th>Amount</th><th>Date</th><th>Method</th></tr></thead>
                    <tbody>
                        <?php while($p = $payments_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['member_name']); ?></td>
                            <td>&#8377;<?php echo number_format($p['amount'], 2); ?></td>
                            <td><?php echo date("M d, Y", strtotime($p['date'])); ?></td>
                            <td><?php echo htmlspecialchars($p['method']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>