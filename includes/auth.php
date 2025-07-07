<?php
require_once 'db.php'; // This will also start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header('Location: ../index.php?error=emptyfields');
        exit();
    }

    $sql = "SELECT user_id, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, start session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin' || $user['role'] === 'staff') {
                header('Location: ../dashboard.php');
            } else {
                header('Location: ../member-dashboard.php');
            }
            exit();
        }
    }

    // If we reach here, login failed
    header('Location: ../index.php?error=1');
    exit();
    
} else {
    // Not a POST request
    header('Location: ../index.php');
    exit();
}
?>