<?php
require_once '../../config/database.php';

// Keamanan & Inisialisasi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../../../login.php');
    exit();
}

// Ambil ID guru dari ID pengguna
$user_id = $_SESSION['user_id'];
$stmt_teacher = $conn->prepare("SELECT id FROM teachers WHERE user_id = ?");
$stmt_teacher->bind_param("i", $user_id);
$stmt_teacher->execute();
$teacher_result = $stmt_teacher->get_result();
if ($teacher_result->num_rows === 0) {
    die("Profil guru tidak ditemukan.");
}
$teacher_id = $teacher_result->fetch_assoc()['id'];

// Ambil siswa yang dibimbing oleh guru ini
$query = "SELECT s.name as student_name, s.nisn, s.class, l.name as location_name, i.id as internship_id
          FROM internships i
          JOIN students s ON i.student_id = s.id
          JOIN locations l ON i.location_id = l.id
          WHERE i.teacher_id = ?
          ORDER BY s.name ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$assigned_students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


$page_title = 'Siswa Bimbingan';
require_once '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo $page_title; ?></h1>
</div>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header">
        <h4>Daftar Siswa Bimbingan PKL Anda</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>Lokasi PKL</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($assigned_students)): ?>
                        <tr>
                            <td colspan="5" class="text-center">Anda belum memiliki siswa bimbingan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($assigned_students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['nisn']); ?></td>
                                <td><?php echo htmlspecialchars($student['class']); ?></td>
                                <td><?php echo htmlspecialchars($student['location_name']); ?></td>
                                <td>
                                    <a href="grades.php?internship_id=<?php echo $student['internship_id']; ?>" class="btn btn-sm btn-primary">Kelola Laporan</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>
