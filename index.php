<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Redirect based on user role
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin_dashboard.php');
    } else if ($_SESSION['role'] === 'teacher') {
        header('Location: teacher_dashboard.php');
    }
    exit();
} else {
    // If not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}
?>