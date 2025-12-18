<?php
session_start();
require_once '../../../config/database.php';

// Security check: ensure user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to login page or show an error
    header('Location: ../../../login.php?error=Access denied');
    exit();
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'create':
        handle_create($conn);
        break;
    case 'update':
        handle_update($conn);
        break;
    case 'delete':
        handle_delete($conn);
        break;
    default:
        // Redirect if no valid action is provided
        header('Location: ../../pages/admin/students.php?error=Invalid action');
        exit();
}

function handle_create($conn) {
    // Basic validation
    if (empty($_POST['name']) || empty($_POST['nisn']) || empty($_POST['class'])) {
        header('Location: ../../pages/admin/students.php?error=All fields are required');
        exit();
    }

    $name = $_POST['name'];
    $nisn = $_POST['nisn'];
    $class = $_POST['class'];
    $expertise_program = $_POST['expertise_program'];
    $expertise_concentration = $_POST['expertise_concentration'];

    $stmt = $conn->prepare("INSERT INTO students (name, nisn, class, expertise_program, expertise_concentration) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $nisn, $class, $expertise_program, $expertise_concentration);

    if ($stmt->execute()) {
        header('Location: ../../pages/admin/students.php?success=Student added successfully');
    } else {
        error_log($stmt->error);
        header('Location: ../../pages/admin/students.php?error=An unexpected error occurred.');
    }
    exit();
}

function handle_update($conn) {
    if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['nisn']) || empty($_POST['class'])) {
        header('Location: ../../pages/admin/students.php?error=All fields are required for update');
        exit();
    }

    $id = $_POST['id'];
    $name = $_POST['name'];
    $nisn = $_POST['nisn'];
    $class = $_POST['class'];
    $expertise_program = $_POST['expertise_program'];
    $expertise_concentration = $_POST['expertise_concentration'];

    $stmt = $conn->prepare("UPDATE students SET name = ?, nisn = ?, class = ?, expertise_program = ?, expertise_concentration = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $name, $nisn, $class, $expertise_program, $expertise_concentration, $id);

    if ($stmt->execute()) {
        header('Location: ../../pages/admin/students.php?success=Student updated successfully');
    } else {
        error_log($stmt->error);
        header('Location: ../../pages/admin/students.php?error=An unexpected error occurred.');
    }
    exit();
}

function handle_delete($conn) {
    if (empty($_POST['id'])) {
        header('Location: ../../pages/admin/students.php?error=Student ID is missing');
        exit();
    }

    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header('Location: ../../pages/admin/students.php?success=Student deleted successfully');
    } else {
        error_log($stmt->error);
        header('Location: ../../pages/admin/students.php?error=An unexpected error occurred.');
    }
    exit();
}
?>
