<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Role-based access control
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff') {
    header('Location: member-dashboard.php');
    exit();
}

// Fetch all members with their plan details
$sql = "SELECT m.member_id, m.name, m.contact, m.join_date, u.email, p.name as plan_name
        FROM members m
        JOIN users u ON m.user_id = u.user_id
        LEFT JOIN plans p ON m.plan_id = p.plan_id
        ORDER BY m.name ASC";
$result = $conn->query($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Manage Members</h1>
    <a href="register.php" class="btn btn-primary-custom">Add New Member</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Plan</th>
                        <th>Join Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['contact']); ?></td>
                                <td><span
                                        class="badge badge-custom"><?php echo htmlspecialchars($row['plan_name'] ?? 'N/A'); ?></span>
                                </td>
                                <td><?php echo date("M d, Y", strtotime($row['join_date'])); ?></td>
                                <td>
                                    <a href="edit-member.php?id=<?php echo $row['member_id']; ?>"
                                        class="btn btn-sm btn-info disabled">Edit</a>
                                    <a href="manage-plans-member.php?id=<?php echo $row['member_id']; ?>"
                                        class="btn btn-sm btn-secondary-custom">Plans</a> <!-- ADD THIS LINE -->
                                    <a href="delete-member.php?id=<?php echo $row['member_id']; ?>"
                                        class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No members found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php require_once 'includes/footer.php'; ?>