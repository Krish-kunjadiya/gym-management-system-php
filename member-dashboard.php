<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Role-based access control
if ($_SESSION['role'] !== 'member') {
    header('Location: dashboard.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Get member_id
$member_id_obj = $conn->query("SELECT member_id FROM members WHERE user_id = $user_id")->fetch_assoc();
$member_id = $member_id_obj['member_id'];


// Handle Progress Log Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_progress'])) {
    $log_date = $_POST['log_date'];
    $weight = (float)$_POST['weight'];
    
    $sql_log = "INSERT INTO progress_logs (member_id, log_date, weight) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE weight = VALUES(weight)";
    $stmt_log = $conn->prepare($sql_log);
    $stmt_log->bind_param("isd", $member_id, $log_date, $weight);
    if($stmt_log->execute()){
         $message = "<div class='alert alert-success'>Progress logged successfully!</div>";
         require_once 'includes/achievements_engine.php';
         checkAndAwardAchievements($member_id, $conn);
    }
}

// --- Fetch ALL Member Data ---
// 1. Get member details and plan info
$sql_member = "SELECT m.*, p.name as plan_name, p.duration FROM members m LEFT JOIN plans p ON m.plan_id = p.plan_id WHERE m.user_id = ?";
$stmt_member = $conn->prepare($sql_member);
$stmt_member->bind_param("i", $user_id);
$stmt_member->execute();
$member = $stmt_member->get_result()->fetch_assoc();

// 2. Payment History
$sql_payments = "SELECT date, amount, method FROM payments WHERE member_id = ? ORDER BY date DESC";
$stmt_pay = $conn->prepare($sql_payments);
$stmt_pay->bind_param("i", $member_id);
$stmt_pay->execute();
$payment_history = $stmt_pay->get_result();

// 3. Workout & Diet Plans
$workouts = $conn->query("SELECT * FROM workouts WHERE member_id = $member_id ORDER BY date_created DESC");
$diets = $conn->query("SELECT * FROM diet_plans WHERE member_id = $member_id ORDER BY date_created DESC");

// 4. Progress Log Data for Chart
$progress_data_result = $conn->query("SELECT log_date, weight FROM progress_logs WHERE member_id = $member_id ORDER BY log_date ASC");
$progress_labels = [];
$progress_weights = [];
while($row = $progress_data_result->fetch_assoc()){
    $progress_labels[] = date("M d", strtotime($row['log_date']));
    $progress_weights[] = $row['weight'];
}

// 5. Fetch latest 3 announcements
$announcements_result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3");

// 6. Fetch earned achievements
$sql_badges = "SELECT a.name, a.description, a.icon_path
               FROM member_achievements ma
               JOIN achievements a ON ma.achievement_id = a.id
               WHERE ma.member_id = ?
               ORDER BY a.category, a.tier";
$stmt_badges = $conn->prepare($sql_badges);
$stmt_badges->bind_param("i", $member_id);
$stmt_badges->execute();
$badges_result = $stmt_badges->get_result();
?>

<!-- Add Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<h1>Welcome, <?php echo htmlspecialchars($member['name']); ?>!</h1>

<!-- Announcements Section -->
<?php if($announcements_result->num_rows > 0): ?>
<div class="mb-4">
    <h4 class="mb-3">Latest From the Gym</h4>
    <?php while($ann = $announcements_result->fetch_assoc()): ?>
        <div class="alert" role="alert" style="background-color: #fff; border-left: 5px solid var(--primary-accent);">
            <h5 class="alert-heading"><?php echo htmlspecialchars($ann['title']); ?></h5>
            <p><?php echo htmlspecialchars($ann['content']); ?></p>
            <hr>
            <p class="mb-0 fst-italic"><small>Posted on <?php echo date("F j, Y", strtotime($ann['created_at'])); ?></small></p>
        </div>
    <?php endwhile; ?>
</div>
<?php endif; ?>

<?php echo $message; ?>

<div class="row mt-4">
    <!-- ======================= LEFT COLUMN ======================= -->
    <div class="col-lg-8">
        
        <!-- Progress Chart -->
        <div class="card mb-4">
            <div class="card-header">My Weight Progress (kg)</div>
            <div class="card-body">
                <canvas id="progressChart"></canvas>
            </div>
        </div>

        <!-- Workout and Diet Plans Accordion -->
        <div class="accordion mb-4" id="plansAccordion">
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseWorkouts">My Workout Plans</button>
            </h2>
            <div id="collapseWorkouts" class="accordion-collapse collapse show" data-bs-parent="#plansAccordion">
              <div class="accordion-body">
                <?php if($workouts->num_rows > 0): while($plan = $workouts->fetch_assoc()): ?>
                    <h5><?php echo htmlspecialchars($plan['title']); ?></h5><p><?php echo nl2br(htmlspecialchars($plan['description'])); ?></p><hr>
                <?php endwhile; else: ?><p>No workout plans assigned yet.</p><?php endif; ?>
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDiets">My Diet Plans</button>
            </h2>
            <div id="collapseDiets" class="accordion-collapse collapse" data-bs-parent="#plansAccordion">
              <div class="accordion-body">
                 <?php if($diets->num_rows > 0): while($plan = $diets->fetch_assoc()): ?>
                    <h5><?php echo htmlspecialchars($plan['title']); ?></h5><p><?php echo nl2br(htmlspecialchars($plan['description'])); ?></p><hr>
                <?php endwhile; else: ?><p>No diet plans assigned yet.</p><?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- My Achievements -->
        <div class="card mt-4">
            <div class="card-header">My Achievements</div>
            <div class="card-body">
                <div class="row">
                    <?php if($badges_result->num_rows > 0): ?>
                        <?php while($badge = $badges_result->fetch_assoc()): ?>
                            <div class="col-3 col-md-2 text-center mb-3" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo htmlspecialchars($badge['name']) . ': ' . htmlspecialchars($badge['description']); ?>">
                                <img src="<?php echo htmlspecialchars($badge['icon_path']); ?>" alt="<?php echo htmlspecialchars($badge['name']); ?>" class="img-fluid">
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center">Your first badge is waiting. Keep up the great work!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div> <!-- ======================= END LEFT COLUMN ======================= -->

    <!-- ======================= RIGHT COLUMN ======================= -->
    <div class="col-lg-4">
        
        <!-- Profile & Membership -->
        <div class="card mb-4">
            <div class="card-header">My Membership</div>
            <div class="card-body">
                 <?php if ($member['plan_id']): ?>
                    <p><strong>Plan:</strong> <span class="badge badge-custom"><?php echo htmlspecialchars($member['plan_name']); ?></span></p>
                <?php else: ?>
                    <div class="alert alert-warning"><strong>No Active Membership!</strong> Please visit the front desk.</div>
                <?php endif; ?>
                <a href="edit-profile.php" class="btn btn-secondary-custom w-100">Edit Profile & Password</a>
            </div>
        </div>

        <!-- Log Progress Form -->
        <div class="card mb-4">
            <div class="card-header">Log My Progress</div>
            <div class="card-body">
                <form action="member-dashboard.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="log_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                     <div class="mb-3">
                        <label class="form-label">Weight (kg)</label>
                        <input type="number" step="0.1" name="weight" class="form-control" required>
                    </div>
                    <button type="submit" name="log_progress" class="btn btn-primary-custom w-100">Log It!</button>
                </form>
            </div>
        </div>

        <!-- Payment History -->
        <div class="card">
             <div class="card-header">Payment History</div>
             <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                 <ul class="list-group list-group-flush">
                     <?php if($payment_history->num_rows > 0): ?>
                        <?php while($pay = $payment_history->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?php echo date("d M, Y", strtotime($pay['date'])); ?></span>
                                <strong>₹<?php echo number_format($pay['amount']); ?></strong>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="list-group-item">No payment history.</li>
                    <?php endif; ?>
                 </ul>
             </div>
        </div>

    </div> <!-- ======================= END RIGHT COLUMN ======================= -->
</div> <!-- END ROW -->

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('progressChart').getContext('2d');
    const progressChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($progress_labels); ?>,
            datasets: [{
                label: 'Weight (kg)',
                data: <?php echo json_encode($progress_weights); ?>,
                backgroundColor: 'rgba(255, 49, 46, 0.1)',
                borderColor: '#FF312E',
                borderWidth: 3,
                tension: 0.2,
                pointBackgroundColor: '#333138',
                pointBorderColor: '#FF312E'
            }]
        },
        options: {
            scales: {
                y: { ticks: { color: '#555' }, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                x: { ticks: { color: '#555' }, grid: { display: false } }
            },
            plugins: { legend: { labels: { color: '#333' } } }
        }
    });

    // Initialize Bootstrap Tooltips for badges
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>

<?php require_once 'includes/footer.php'; ?>