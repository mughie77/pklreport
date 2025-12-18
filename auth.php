<?php
session_start();
require_once 'config/database.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the database query
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirect to the appropriate dashboard
            header('Location: index.php');
            exit();
        }
    }

    // If authentication fails, redirect back to the login page
    header('Location: login.php?error=Invalid username or password');
    exit();
} else {
    // If the page is accessed directly, redirect to the login page
    header('Location: login.php');
    exit();
}
?>