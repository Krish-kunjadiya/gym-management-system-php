<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Ensure member is logged in
if ($_SESSION['role'] !== 'member') {
    header('Location: index.php');
    exit();
}

$member_id_obj = $conn->query("SELECT member_id FROM members WHERE user_id = {$_SESSION['user_id']}")->fetch_assoc();
$member_id = $member_id_obj['member_id'];

// Fetch upcoming classes
$today = date("Y-m-d H:i:s");
$sql = "SELECT c.*, (SELECT COUNT(*) FROM class_bookings WHERE class_id = c.class_id) as booked,
        (SELECT COUNT(*) FROM class_bookings WHERE class_id = c.class_id AND member_id = {$member_id}) as is_booked
        FROM classes c
        WHERE c.schedule_datetime >= '{$today}'
        ORDER BY c.schedule_datetime ASC";
$classes_result = $conn->query($sql);

?>
<h1>Upcoming Class Schedule</h1>

<?php if(isset($_GET['status'])): ?>
    <?php if($_GET['status'] == 'success'): ?>
        <div class="alert alert-success">You have successfully booked the class!</div>
    <?php elseif($_GET['status'] == 'full'): ?>
        <div class="alert alert-danger">Sorry, this class is already full.</div>
    <?php elseif($_GET['status'] == 'booked'): ?>
        <div class="alert alert-warning">You are already booked for this class.</div>
     <?php elseif($_GET['status'] == 'error'): ?>
        <div class="alert alert-danger">An error occurred. Please try again.</div>
    <?php endif; ?>
<?php endif; ?>


<div class="row">
    <?php if($classes_result->num_rows > 0): ?>
        <?php while ($class = $classes_result->fetch_assoc()):
            $available_slots = $class['max_capacity'] - $class['booked'];
        ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo htmlspecialchars($class['name']); ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo date("l, F j, Y - g:i A", strtotime($class['schedule_datetime'])); ?></h6>
                    <p class="card-text flex-grow-1"><?php echo htmlspecialchars($class['description']); ?></p>
                    <div class="mt-auto">
                        <p class="mb-2"><strong>Slots Available:</strong> <?php echo $available_slots; ?> / <?php echo $class['max_capacity']; ?></p>
                        <?php if($class['is_booked']): ?>
                             <button class="btn btn-success w-100" disabled>Already Booked</button>
                        <?php elseif($available_slots > 0): ?>
                            <a href="book-class.php?class_id=<?php echo $class['class_id']; ?>" class="btn btn-primary-custom w-100">Book Now</a>
                        <?php else: ?>
                            <button class="btn btn-danger w-100" disabled>Class Full</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col">
            <p>No upcoming classes scheduled at the moment. Please check back later.</p>
        </div>
    <?php endif; ?>
</div>


<?php require_once 'includes/footer.php'; ?>