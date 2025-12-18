<?php
session_start();
require_once '../../../config/database.php';

// Security check: ensure user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../../login.php?error=Access denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation
    if (empty($_POST['school_name']) || empty($_POST['academic_year'])) {
        header('Location: ../../pages/admin/settings.php?error=All fields are required');
        exit();
    }

    $school_name = $_POST['school_name'];
    $academic_year = $_POST['academic_year'];

    $settings_to_update = [
        'school_name' => $school_name,
        'academic_year' => $academic_year,
    ];

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");

        foreach ($settings_to_update as $key => $value) {
            $stmt->bind_param("ss", $key, $value);
            $stmt->execute();
        }

        $conn->commit();
        header('Location: ../../pages/admin/settings.php?success=Settings updated successfully');
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        // Log the detailed error message instead of displaying it to the user
        error_log($exception->getMessage());
        header('Location: ../../pages/admin/settings.php?error=An unexpected error occurred. Please try again.');
    }

    exit();
} else {
    // Redirect if not a POST request
    header('Location: ../../pages/admin/settings.php');
    exit();
}
?>
