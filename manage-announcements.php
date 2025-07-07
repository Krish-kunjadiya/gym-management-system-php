<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff') {
    header('Location: index.php'); exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $user_id = $_SESSION['user_id'];
    $sql = "INSERT INTO announcements (title, content, created_by_user_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $content, $user_id);
    $stmt->execute();
}

// Handle delete
if(isset($_GET['delete'])) {
    $id_to_delete = (int)$_GET['delete'];
    $conn->query("DELETE FROM announcements WHERE id = $id_to_delete");
}

$announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Manage Announcements</h1>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Post New Announcement</div>
            <div class="card-body">
                <form action="manage-announcements.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary-custom">Post Notice</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Posted Notices</div>
            <div class="list-group list-group-flush">
                <?php while($row = $announcements->fetch_assoc()): ?>
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?php echo htmlspecialchars($row['title']); ?></h5>
                        <small><?php echo date("d M, Y", strtotime($row['created_at'])); ?></small>
                    </div>
                    <p class="mb-1"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                    <a href="manage-announcements.php?delete=<?php echo $row['id']; ?>" class="text-danger" onclick="return confirm('Are you sure?')"><small>Delete</small></a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>