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
    case 'update':
        handle_update($conn);
        break;
    case 'delete':
        handle_delete($conn);
        break;
    default:
        header('Location: ../../pages/admin/teachers.php?error=Invalid action');
        exit();
}

function handle_create($conn) {
    if (empty($_POST['name']) || empty($_POST['username']) || empty($_POST['password'])) {
        header('Location: ../../pages/admin/teachers.php?error=Name, username, and password are required');
        exit();
    }

    $name = $_POST['name'];
    $nip = $_POST['nip'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'teacher';

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        header('Location: ../../pages/admin/teachers.php?error=Username already exists');
        exit();
    }

    $conn->begin_transaction();

    try {
        // Create the user account
        $stmt_user = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt_user->bind_param("sss", $username, $password, $role);
        $stmt_user->execute();

        $user_id = $conn->insert_id;

        // Create the teacher profile
        $stmt_teacher = $conn->prepare("INSERT INTO teachers (user_id, name, nip) VALUES (?, ?, ?)");
        $stmt_teacher->bind_param("iss", $user_id, $name, $nip);
        $stmt_teacher->execute();

        $conn->commit();
        header('Location: ../../pages/admin/teachers.php?success=Teacher added successfully');
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        error_log($exception->getMessage());
        header('Location: ../../pages/admin/teachers.php?error=An unexpected error occurred.');
    }
    exit();
}

function handle_update($conn) {
    if (empty($_POST['id']) || empty($_POST['name'])) {
        header('Location: ../../pages/admin/teachers.php?error=Teacher ID and name are required for update');
        exit();
    }

    $id = $_POST['id'];
    $name = $_POST['name'];
    $nip = $_POST['nip'];
    $password = $_POST['password'];

    $conn->begin_transaction();

    try {
        // Update teacher's name and NIP
        $stmt_teacher = $conn->prepare("UPDATE teachers SET name = ?, nip = ? WHERE id = ?");
        $stmt_teacher->bind_param("ssi", $name, $nip, $id);
        $stmt_teacher->execute();

        // If a new password is provided, update it
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // Get user_id from teacher id
            $stmt_get_user = $conn->prepare("SELECT user_id FROM teachers WHERE id = ?");
            $stmt_get_user->bind_param("i", $id);
            $stmt_get_user->execute();
            $user_id = $stmt_get_user->get_result()->fetch_assoc()['user_id'];

            $stmt_user_pass = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt_user_pass->bind_param("si", $hashed_password, $user_id);
            $stmt_user_pass->execute();
        }

        $conn->commit();
        header('Location: ../../pages/admin/teachers.php?success=Teacher updated successfully');
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        error_log($exception->getMessage());
        header('Location: ../../pages/admin/teachers.php?error=An unexpected error occurred.');
    }
    exit();
}

function handle_delete($conn) {
    if (empty($_POST['id'])) {
        header('Location: ../../pages/admin/teachers.php?error=Teacher ID is missing');
        exit();
    }

    $id = $_POST['id'];

    $conn->begin_transaction();
    try {
        // Get user_id before deleting the teacher to delete the user as well
        $stmt_get_user = $conn->prepare("SELECT user_id FROM teachers WHERE id = ?");
        $stmt_get_user->bind_param("i", $id);
        $stmt_get_user->execute();
        $result = $stmt_get_user->get_result();

        if ($result->num_rows > 0) {
            $user_id = $result->fetch_assoc()['user_id'];

            // Delete the user record, which will cascade to the teachers table
            $stmt_delete_user = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt_delete_user->bind_param("i", $user_id);
            $stmt_delete_user->execute();
        } else {
             throw new Exception("Teacher not found.");
        }

        $conn->commit();
        header('Location: ../../pages/admin/teachers.php?success=Teacher deleted successfully');
    } catch (Exception $e) {
        $conn->rollback();
        error_log($e->getMessage());
        header('Location: ../../pages/admin/teachers.php?error=An unexpected error occurred.');
    }
    exit();
}
?>
