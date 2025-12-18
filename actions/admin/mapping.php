<?php
session_start();
require_once '../../../config/database.php';

// Security check: ensure user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../../login.php?error=Access denied');
    exit();
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'create':
        handle_create($conn);
        break;
    case 'delete':
        handle_delete($conn);
        break;
    default:
        header('Location: ../../pages/admin/mapping.php?error=Invalid action');
        exit();
}

function handle_create($conn) {
    // Basic validation
    if (empty($_POST['student_id']) || empty($_POST['teacher_id']) || empty($_POST['location_id']) || empty($_POST['start_date']) || empty($_POST['end_date'])) {
        header('Location: ../../pages/admin/mapping.php?error=All fields are required');
        exit();
    }

    $student_id = $_POST['student_id'];
    $teacher_id = $_POST['teacher_id'];
    $location_id = $_POST['location_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Check if the student is already assigned
    $stmt_check = $conn->prepare("SELECT id FROM internships WHERE student_id = ?");
    $stmt_check->bind_param("i", $student_id);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        header('Location: ../../pages/admin/mapping.php?error=This student is already assigned to an internship.');
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO internships (student_id, teacher_id, location_id, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $student_id, $teacher_id, $location_id, $start_date, $end_date);

    if ($stmt->execute()) {
        header('Location: ../../pages/admin/mapping.php?success=Internship assigned successfully');
    } else {
        error_log($stmt->error);
        header('Location: ../../pages/admin/mapping.php?error=An unexpected error occurred.');
    }
    exit();
}

function handle_delete($conn) {
    if (empty($_POST['id'])) {
        header('Location: ../../pages/admin/mapping.php?error=Mapping ID is missing');
        exit();
    }

    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM internships WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header('Location: ../../pages/admin/mapping.php?success=Internship assignment deleted successfully');
    } else {
        error_log($stmt->error);
        header('Location: ../../pages/admin/mapping.php?error=An unexpected error occurred.');
    }
    exit();
}
?>
