<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Menentukan path yang benar ke file config
$config_path = realpath(__DIR__ . '/../config/app.php');
if (!$config_path) {
    // Fallback jika file dipanggil dari direktori yang lebih dalam
    $config_path = realpath(__DIR__ . '/../../config/app.php');
}
require_once $config_path;

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - Aplikasi Laporan PKL' : 'Aplikasi Laporan PKL'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="sidebar">
            <h4 class="text-white text-center py-3">Aplikasi PKL</h4>
            <ul class="nav flex-column">
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'admin_dashboard.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'students.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>pages/admin/students.php"><i class="bi bi-people"></i> Kelola Siswa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'teachers.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>pages/admin/teachers.php"><i class="bi bi-person-video3"></i> Kelola Guru</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'locations.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>pages/admin/locations.php"><i class="bi bi-geo-alt"></i> Kelola Lokasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'mapping.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>pages/admin/mapping.php"><i class="bi bi-diagram-3"></i> Pemetaan Siswa</a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>pages/admin/settings.php"><i class="bi bi-gear"></i> Pengaturan</a>
                    </li>
                <?php elseif ($_SESSION['role'] === 'teacher'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'teacher_dashboard.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>teacher_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'students.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>pages/teacher/students.php"><i class="bi bi-people"></i> Siswa Bimbingan</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="main-content">
            <header class="topbar">
                <div class="d-flex justify-content-end align-items-center w-100">
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="d-none d-sm-inline mx-1"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <i class="bi bi-person-circle fs-4"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end text-small shadow">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>
            <main class="container-fluid p-4">
                <!-- Content starts here -->
    <?php else: ?>
        <div class="login-background">
             <main class="container">
                <!-- Login content starts here -->
    <?php endif; ?>
