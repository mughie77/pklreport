<?php
session_start();
require_once '../../config/database.php';

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
    case 'update':
        handle_update($conn);
        break;
    case 'delete':
        handle_delete($conn);
        break;
    default:
        header('Location: ../../pages/admin/locations.php?error=Invalid action');
        exit();
}

function handle_create($conn) {
    if (empty($_POST['name'])) {
        header('Location: ../../pages/admin/locations.php?error=Location name is required');
        exit();
    }

    $name = $_POST['name'];
    $address = $_POST['address'];
    $instructor_name = $_POST['instructor_name'];

    $stmt = $conn->prepare("INSERT INTO locations (name, address, instructor_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $address, $instructor_name);

    if ($stmt->execute()) {
        header('Location: ../../pages/admin/locations.php?success=Location added successfully');
    } else {
        error_log($stmt->error);
        header('Location: ../../pages/admin/locations.php?error=An unexpected error occurred.');
    }
    exit();
}

function handle_update($conn) {
    if (empty($_POST['id']) || empty($_POST['name'])) {
        header('Location: ../../pages/admin/locations.php?error=Location ID and name are required for update');
        exit();
    }

    $id = $_POST['id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $instructor_name = $_POST['instructor_name'];

    $stmt = $conn->prepare("UPDATE locations SET name = ?, address = ?, instructor_name = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $address, $instructor_name, $id);

    if ($stmt->execute()) {
        header('Location: ../../pages/admin/locations.php?success=Location updated successfully');
    } else {
        error_log($stmt->error);
        header('Location: ../../pages/admin/locations.php?error=An unexpected error occurred.');
    }
    exit();
}

function handle_delete($conn) {
    if (empty($_POST['id'])) {
        header('Location: ../../pages/admin/locations.php?error=Location ID is missing');
        exit();
    }

    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM locations WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header('Location: ../../pages/admin/locations.php?success=Location deleted successfully');
    } else {
        error_log($stmt->error);
        header('Location: ../../pages/admin/locations.php?error=An unexpected error occurred.');
    }
    exit();
}
?>
