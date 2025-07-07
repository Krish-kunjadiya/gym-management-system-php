<?php
require_once 'includes/db.php';

// Ensure member is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header('Location: index.php');
    exit();
}

if (!isset($_GET['class_id']) || !is_numeric($_GET['class_id'])) {
    header('Location: class-schedule.php');
    exit();
}

$class_id = (int)$_GET['class_id'];
$member_id_obj = $conn->query("SELECT member_id FROM members WHERE user_id = {$_SESSION['user_id']}")->fetch_assoc();
$member_id = $member_id_obj['member_id'];

// Use a transaction for safe booking
$conn->begin_transaction();

try {
    // 1. Lock the row and get current details to prevent race conditions
    $sql = "SELECT max_capacity, (SELECT COUNT(*) FROM class_bookings WHERE class_id = ?) as booked 
            FROM classes WHERE class_id = ? FOR UPDATE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $class_id, $class_id);
    $stmt->execute();
    $class = $stmt->get_result()->fetch_assoc();

    if ($class['booked'] >= $class['max_capacity']) {
        throw new Exception("full");
    }

    // 2. Insert the booking. A UNIQUE key on (class_id, member_id) prevents double booking.
    $sql_insert = "INSERT INTO class_bookings (class_id, member_id) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ii", $class_id, $member_id);
    $stmt_insert->execute();

    // 3. If all is good, commit
    $conn->commit();

    require_once 'includes/achievements_engine.php';
    checkAndAwardAchievements($member_id, $conn);
    
    header('Location: class-schedule.php?status=success');

} catch (Exception $e) {
    $conn->rollback();
    if ($e->getMessage() === "full") {
        header('Location: class-schedule.php?status=full');
    } elseif ($conn->errno === 1062) { // 1062 is the error code for a duplicate entry
        header('Location: class-schedule.php?status=booked');
    }
    else {
        header('Location: class-schedule.php?status=error');
    }
}
exit();
?>