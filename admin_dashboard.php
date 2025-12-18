<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Arahkan jika tidak login atau bukan admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';

// Ambil statistik
$student_count = $conn->query("SELECT COUNT(*) AS count FROM students")->fetch_assoc()['count'];
$teacher_count = $conn->query("SELECT COUNT(*) AS count FROM teachers")->fetch_assoc()['count'];
$location_count = $conn->query("SELECT COUNT(*) AS count FROM locations")->fetch_assoc()['count'];
$internship_count = $conn->query("SELECT COUNT(*) AS count FROM internships")->fetch_assoc()['count'];


$page_title = 'Dashboard Admin';
require_once 'includes/header.php';
?>

<div class="p-4 bg-light rounded-3 mb-4">
    <div class="container-fluid py-3">
        <h1 class="display-5 fw-bold">Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p class="col-md-8 fs-4">Dari dashboard ini, Anda dapat mengelola fitur inti aplikasi Laporan PKL.</p>
    </div>
</div>

<div class="row text-center">
    <div class="col-md-3 mb-4">
        <div class="card h-100 shadow dashboard-card">
            <div class="card-body">
                <h5 class="card-title">Total Siswa</h5>
                <p class="card-text fs-1"><?php echo $student_count; ?></p>
                <a href="pages/admin/students.php" class="btn btn-primary">Kelola</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card h-100 shadow dashboard-card">
            <div class="card-body">
                <h5 class="card-title">Total Guru</h5>
                <p class="card-text fs-1"><?php echo $teacher_count; ?></p>
                <a href="pages/admin/teachers.php" class="btn btn-primary">Kelola</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card h-100 shadow dashboard-card">
            <div class="card-body">
                <h5 class="card-title">Total Lokasi</h5>
                <p class="card-text fs-1"><?php echo $location_count; ?></p>
                <a href="pages/admin/locations.php" class="btn btn-primary">Kelola</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card h-100 shadow dashboard-card">
            <div class="card-body">
                <h5 class="card-title">Siswa Terpetakan</h5>
                <p class="card-text fs-1"><?php echo $internship_count; ?></p>
                <a href="pages/admin/mapping.php" class="btn btn-primary">Kelola</a>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
