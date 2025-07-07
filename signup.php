<?php
require_once 'includes/db.php';
// ... (keep the existing PHP logic for handling form submission at the top)
// The rest of the PHP from your signup.php file goes here...
// This is just the HTML and CSS part
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // [YOUR EXISTING SIGNUP PHP LOGIC GOES HERE]
    // ...
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Gym Management System</title>
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
        .signup-card {
            width: 100%;
            max-width: 450px;
            border: none;
            padding: 1rem 2rem;
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
            <h1>Join Us Today</h1>
            <p class="lead">Start your fitness journey and unlock your potential.</p>
        </div>
        <div class="form-half">
            <div class="signup-card">
                <h2 class="text-center mb-4">Create Account</h2>
                
                <?php echo $message; ?>

                <form action="signup.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contact" name="contact" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="d-grid mt-2">
                        <button type="submit" class="btn btn-primary">Sign Up</button>
                    </div>
                </form>
                <div class="text-center mt-3">
                    <p>Already have an account? <a href="index.php">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>