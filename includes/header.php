<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Define a base URL to make links work from any directory
$base_url = '/';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - Aplikasi Laporan PKL' : 'Aplikasi Laporan PKL'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Aplikasi Laporan PKL</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $base_url; ?>admin_dashboard.php">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $base_url; ?>pages/admin/students.php">Kelola Siswa</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $base_url; ?>pages/admin/teachers.php">Kelola Guru</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $base_url; ?>pages/admin/locations.php">Kelola Lokasi</a>
                            </li>
                             <li class="nav-item">
                                <a class="nav-link" href="<?php echo $base_url; ?>pages/admin/mapping.php">Pemetaan Siswa</a>
                            </li>
                        <?php elseif ($_SESSION['role'] === 'teacher'): ?>
                             <li class="nav-item">
                                <a class="nav-link" href="<?php echo $base_url; ?>teacher_dashboard.php">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $base_url; ?>pages/teacher/students.php">Siswa Bimbingan</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                     <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo $base_url; ?>logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-4">
        <!-- Content starts here -->