<?php
// This function will be the central engine for checking all achievements.
function checkAndAwardAchievements($member_id, $conn) {
    // Get all achievements the member has NOT yet earned.
    $sql = "SELECT * FROM achievements WHERE id NOT IN (SELECT achievement_id FROM member_achievements WHERE member_id = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $unearned_achievements = $stmt->get_result();

    if ($unearned_achievements->num_rows === 0) {
        return; // Member has all achievements, no need to check.
    }

    // --- Check each category ---

    // 1. Consistency (Attendance)
    $attendance_count_res = $conn->query("SELECT COUNT(*) as count FROM attendance WHERE member_id = $member_id");
    $attendance_count = $attendance_count_res->fetch_assoc()['count'];

    // 2. Progress (Logging)
    $progress_log_count_res = $conn->query("SELECT COUNT(*) as count FROM progress_logs WHERE member_id = $member_id");
    $progress_log_count = $progress_log_count_res->fetch_assoc()['count'];

    // 3. Community (Classes) - For now, we count bookings as attended for simplicity.
    $class_booking_count_res = $conn->query("SELECT COUNT(*) as count FROM class_bookings WHERE member_id = $member_id");
    $class_booking_count = $class_booking_count_res->fetch_assoc()['count'];


    // Now, loop through the unearned achievements and see if the member qualifies.
    while ($achievement = $unearned_achievements->fetch_assoc()) {
        $qualified = false;
        switch ($achievement['category']) {
            case 'consistency':
                if ($attendance_count >= $achievement['requirement']) $qualified = true;
                break;
            case 'progress':
                if ($progress_log_count >= $achievement['requirement']) $qualified = true;
                break;
            case 'community':
                if ($class_booking_count >= $achievement['requirement']) $qualified = true;
                break;
            // 'loyalty' would require a more complex check based on join_date, can be added later.
        }

        if ($qualified) {
            // Award the achievement!
            $insert_sql = "INSERT INTO member_achievements (member_id, achievement_id) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ii", $member_id, $achievement['id']);
            $insert_stmt->execute();
        }
    }
}
?>