<?php
session_start();
require_once '../../config/database.php';

// Keamanan: pastikan pengguna adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../../login.php?error=Access denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi dasar
    if (empty($_POST['school_name']) || empty($_POST['academic_year']) || empty($_POST['report_date_place'])) {
        header('Location: ../../pages/admin/settings.php?error=Semua bidang wajib diisi');
        exit();
    }

    $school_name = $_POST['school_name'];
    $academic_year = $_POST['academic_year'];
    $report_date_place = $_POST['report_date_place'];

    $settings_to_update = [
        'school_name' => $school_name,
        'academic_year' => $academic_year,
        'report_date_place' => $report_date_place,
    ];

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");

        foreach ($settings_to_update as $key => $value) {
            $stmt->bind_param("ss", $key, $value);
            $stmt->execute();
        }

        $conn->commit();
        header('Location: ../../pages/admin/settings.php?success=Pengaturan berhasil diperbarui');
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        // Catat pesan error detail alih-alih menampilkannya kepada pengguna
        error_log($exception->getMessage());
        header('Location: ../../pages/admin/settings.php?error=Terjadi kesalahan tak terduga. Silakan coba lagi.');
    }

    exit();
} else {
    // Arahkan jika bukan permintaan POST
    header('Location: ../../pages/admin/settings.php');
    exit();
}
?>
