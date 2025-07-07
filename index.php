<?php
require_once 'includes/db.php';

// If user is already logged in, redirect to their dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'member') {
        header('Location: member-dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gym Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
        <style>
        :root {
            --primary-accent: #FF312E;
            --primary-dark: #333138;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .main-container {
            display: flex;
            min-height: 100vh;
        }
        .image-half {
            flex: 0 0 40%; /* MODIFIED: Set to 40% width */
            background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), url('assets/images/login-bg.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #fff;
            padding: 2rem;
            text-align: center;
        }
        .image-half h1 {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-accent); /* MODIFIED: Changed to accent red */
        }
        .form-half {
            flex: 0 0 60%; /* MODIFIED: Set to 60% width */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border: none;
            padding: 2rem;
        }
        .btn-primary {
            background-color: var(--primary-accent);
            border-color: var(--primary-accent);
            font-weight: 600;
        }
        .btn-primary:hover, .btn-primary:active {
            background-color: #e02b28;
            border-color: #e02b28;
        }
        .btn-primary:focus,
        .btn-primary:active {
            box-shadow: 0 0 0 0.25rem rgba(255, 49, 46, 0.35) !important;
        }
        a {
            color: var(--primary-accent);
        }
        @media (max-width: 992px) { /* Changed breakpoint for better responsiveness */
            .image-half {
                display: none; /* Hide image on smaller screens */
            }
            .form-half {
                flex: 1; /* Let form take full width on smaller screens */
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="image-half">
            <h1>Welcome Back</h1>
            <p class="lead">Your journey to fitness continues here.</p>
        </div>
        <div class="form-half">
            <div class="login-card">
                <h2 class="text-center mb-4">Gym Login</h2>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">Invalid email or password.</div>
                <?php endif; ?>
                <?php if (isset($_GET['signup']) && $_GET['signup'] === 'success'): ?>
                    <div class="alert alert-success">Registration successful! Please log in.</div>
                <?php endif; ?>

                <form action="includes/auth.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
                <div class="text-center mt-3">
                    <p>Don't have an account? <a href="signup.php">Sign Up Now</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>