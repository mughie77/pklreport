<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Arahkan jika tidak login atau bukan guru
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';

// Ambil ID guru dari ID pengguna yang login
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id FROM teachers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$teacher_id = $stmt->get_result()->fetch_assoc()['id'];

// Ambil jumlah siswa bimbingan
$student_count = 0;
if ($teacher_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM internships WHERE teacher_id = ?");
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $student_count = $stmt->get_result()->fetch_assoc()['count'];
}


$page_title = 'Dashboard Guru';
require_once 'includes/header.php';
?>

<div class="p-4 bg-light rounded-3 mb-4">
    <div class="container-fluid py-3">
        <h1 class="display-5 fw-bold">Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p class="col-md-8 fs-4">Di sini Anda dapat mengelola siswa bimbingan dan laporan PKL mereka.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100 shadow dashboard-card">
            <div class="card-body text-center">
                <h5 class="card-title">Jumlah Siswa Bimbingan</h5>
                <p class="card-text fs-1"><?php echo $student_count; ?></p>
                <a href="pages/teacher/students.php" class="btn btn-primary">Lihat & Kelola Laporan</a>
            </div>
        </div>
    </div>
</div>


<?php
require_once 'includes/footer.php';
?>
