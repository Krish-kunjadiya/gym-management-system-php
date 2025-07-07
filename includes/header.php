<?php
// This check should be on top of every protected page
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get user role for navigation
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* -- FINAL LIGHT THEME PALETTE -- */
        :root {
            --bg-main: #FFFFFA; /* Off-White */
            --bg-card: #FFFFFF; /* Pure White */
            --primary-dark: #333138; /* Dark Charcoal */
            --primary-accent: #FF312E; /* Bright Red */
            --text-dark: #000103; /* Almost Black */
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-dark);
        }
        .navbar {
            background-color: var(--primary-dark);
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .navbar-brand, .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 500;
        }
        .navbar-brand:hover, .navbar-nav .nav-link:hover {
            color: var(--primary-accent) !important;
        }
        .card {
            background-color: var(--bg-card);
            border: 1px solid #e9e9e9;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            color: var(--text-dark);
        }
        .card-header {
            background-color: var(--primary-dark);
            color: #fff;
            font-weight: 600;
            border-bottom: 1px solid var(--primary-dark);
        }
        .btn-primary-custom {
            background-color: var(--primary-accent);
            border-color: var(--primary-accent);
            color: #fff;
            font-weight: 600;
        }
        .btn-primary-custom:hover {
            background-color: #e02b28; /* Slightly darker red */
            border-color: #e02b28;
        }
        .btn-secondary-custom {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            color: #fff;
        }
        .btn-secondary-custom:hover {
            background-color: #4a4850;
            border-color: #4a4850;
        }
        .badge-custom {
            background-color: var(--primary-accent);
            color: #fff;
        }
        a {
            color: var(--primary-accent);
            text-decoration: none;
        }
        a:hover {
            color: #e02b28;
        }
        .table {
            color: var(--text-dark);
        }
        .table-striped>tbody>tr:nth-of-type(odd)>* {
            --bs-table-accent-bg: #f8f9fa;
        }
        .form-control, .form-select {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
        }
        .modal-content, .accordion-item {
            background-color: var(--bg-card);
        }
        .accordion-button {
            color: var(--text-dark);
        }
        .accordion-button:not(.collapsed) {
            background-color: var(--primary-dark);
            color: #fff;
        }
        /* --- FOOTER FIX --- */
        footer {
            background-color: var(--primary-dark) !important;
            color: #ccc;
        }
        /* --- DEFINITIVE FIX FOR BLUE BUTTON FOCUS/ACTIVE OUTLINE --- */
        .btn-primary-custom:focus,
        .btn-primary-custom:active,
        .btn-secondary-custom:focus,
        .btn-secondary-custom:active,
        .btn-danger:focus,
        .btn-danger:active {
            box-shadow: 0 0 0 0.25rem rgba(255, 49, 46, 0.35) !important;
            /* We add !important to ensure this rule wins over any other specific Bootstrap rule */
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <!-- The rest of your navbar HTML remains the same -->
        <div class="container">
            <a class="navbar-brand" href="#">Gym MS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
    <?php if ($role === 'admin'): ?>
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="manage-members.php">Members</a></li>
        <li class="nav-item"><a class="nav-link" href="manage-staff.php">Staff</a></li>
        <li class="nav-item"><a class="nav-link" href="add-plan.php">Plans</a></li>
        <li class="nav-item"><a class="nav-link" href="manage-classes.php">Classes</a></li>
        <li class="nav-item"><a class="nav-link" href="manage-announcements.php">Announcements</a></li> <!-- ADDED FOR ADMIN -->
        <li class="nav-item"><a class="nav-link" href="payments.php">Payments</a></li>
        <li class="nav-item"><a class="nav-link" href="attendance.php">Attendance</a></li>
        <li class="nav-item"><a class="nav-link" href="reports.php">Reports</a></li>
    <?php elseif ($role === 'staff'): ?>
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="manage-members.php">Members</a></li>
        <li class="nav-item"><a class="nav-link" href="manage-classes.php">Classes</a></li>
        <li class="nav-item"><a class="nav-link" href="manage-announcements.php">Announcements</a></li> <!-- ADDED FOR STAFF -->
        <li class="nav-item"><a class="nav-link" href="attendance.php">Attendance</a></li>
    <?php elseif ($role === 'member'): ?>
        <li class="nav-item"><a class="nav-link" href="member-dashboard.php">My Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="class-schedule.php">Class Schedule</a></li>
        <li class="nav-item"><a class="nav-link" href="edit-profile.php">Edit Profile</a></li>
    <?php endif; ?>
    <li class="nav-item">
        <a class="btn btn-danger btn-sm" href="logout.php">Logout</a>
    </li>
</ul>
            </div>
        </div>
    </nav>

    <div class="container">