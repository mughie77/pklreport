<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Also check if the role is set to prevent warnings
    if (isset($_SESSION['role'])) {
        // Redirect based on user role
        if ($_SESSION['role'] === 'admin') {
            header('Location: admin_dashboard.php');
            exit();
        } else if ($_SESSION['role'] === 'teacher') {
            header('Location: teacher_dashboard.php');
            exit();
        }
    }
    // If role is not set or invalid, the session is corrupt. Log out to clear it.
    header('Location: logout.php');
    exit();

} else {
    // If not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}
?>