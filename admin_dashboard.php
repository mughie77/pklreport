<?php
session_start();
// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Welcome, Admin!</h2>
        <p>From this dashboard, you can manage the application's core features.</p>
        <div class="list-group">
            <a href="pages/admin/students.php" class="list-group-item list-group-item-action">Manage Students</a>
            <a href="pages/admin/teachers.php" class="list-group-item list-group-item-action">Manage Teachers</a>
            <a href="pages/admin/locations.php" class="list-group-item list-group-item-action">Manage PKL Locations</a>
            <a href="pages/admin/mapping.php" class="list-group-item list-group-item-action">Map Students to Internships</a>
            <a href="pages/admin/settings.php" class="list-group-item list-group-item-action">Application Settings</a>
        </div>
    </div>
</body>
</html>