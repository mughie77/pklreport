<?php
$page_title = 'Kelola Laporan Siswa';
require_once '../../includes/header.php';
require_once '../../config/database.php';

// Keamanan & Inisialisasi: Pastikan header.php dipanggil SEBELUM ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}
if (!isset($_GET['internship_id'])) {
    header('Location: students.php?error=Tidak ada siswa yang dipilih');
    exit();
}

$internship_id = $_GET['internship_id'];
$user_id = $_SESSION['user_id'];

// Ambil ID guru
$stmt_teacher = $conn->prepare("SELECT id FROM teachers WHERE user_id = ?");
$stmt_teacher->bind_param("i", $user_id);
$stmt_teacher->execute();
$teacher_id_result = $stmt_teacher->get_result();
if ($teacher_id_result->num_rows === 0) {
    header('Location: students.php?error=Profil guru tidak ditemukan');
    exit();
}
$teacher_id = $teacher_id_result->fetch_assoc()['id'];

// Verifikasi otorisasi guru
$query_verify = "SELECT i.id, s.name as student_name, s.nisn, s.class, l.name as location_name
                 FROM internships i
                 JOIN students s ON i.student_id = s.id
                 JOIN locations l ON i.location_id = l.id
                 WHERE i.id = ? AND i.teacher_id = ?";
$stmt_verify = $conn->prepare($query_verify);
$stmt_verify->bind_param("ii", $internship_id, $teacher_id);
$stmt_verify->execute();
$internship_details = $stmt_verify->get_result()->fetch_assoc();

if (!$internship_details) {
    header('Location: students.php?error=Anda tidak berwenang melihat laporan ini');
    exit();
}

// Ambil data laporan yang ada
$stmt_report = $conn->prepare("SELECT * FROM reports WHERE internship_id = ?");
$stmt_report->bind_param("i", $internship_id);
$stmt_report->execute();
$report = $stmt_report->get_result()->fetch_assoc();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo $page_title; ?></h1>
    <a href="students.php" class="btn btn-secondary">Kembali ke Daftar Siswa</a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<div class="card mb-4 shadow-sm">
    <div class="card-header">
        <h4>Detail Siswa</h4>
    </div>
    <div class="card-body">
        <p><strong>Nama:</strong> <?php echo htmlspecialchars($internship_details['student_name']); ?></p>
        <p><strong>NISN:</strong> <?php echo htmlspecialchars($internship_details['nisn']); ?></p>
        <p><strong>Kelas:</strong> <?php echo htmlspecialchars($internship_details['class']); ?></p>
        <p><strong>Lokasi PKL:</strong> <?php echo htmlspecialchars($internship_details['location_name']); ?></p>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h4>Formulir Laporan</h4>
    </div>
    <div class="card-body">
        <form action="../../actions/teacher/grades.php" method="post">
            <input type="hidden" name="internship_id" value="<?php echo $internship_id; ?>">

            <h5>Tujuan Pembelajaran</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Tujuan Pembelajaran</th>
                            <th>Skor (1-100)</th>
                            <th>Deskripsi Pencapaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Memahami alur bisnis dunia kerja tempat PKL dan wawasan wirausaha</td>
                            <td><input type="number" name="objective_1_score" class="form-control" min="0" max="100" value="<?php echo $report['objective_1_score'] ?? ''; ?>"></td>
                            <td><textarea name="objective_1_desc" class="form-control" rows="2"><?php echo htmlspecialchars($report['objective_1_desc'] ?? ''); ?></textarea></td>
                        </tr>
                        <tr>
                            <td>Menerapkan soft skill yang dibutuhkan dalam dunia kerja</td>
                            <td><input type="number" name="objective_2_score" class="form-control" min="0" max="100" value="<?php echo $report['objective_2_score'] ?? ''; ?>"></td>
                            <td><textarea name="objective_2_desc" class="form-control" rows="2"><?php echo htmlspecialchars($report['objective_2_desc'] ?? ''); ?></textarea></td>
                        </tr>
                        <tr>
                            <td>Menerapkan norma, SOP dan K3LH yang ada pada dunia kerja</td>
                            <td><input type="number" name="objective_3_score" class="form-control" min="0" max="100" value="<?php echo $report['objective_3_score'] ?? ''; ?>"></td>
                            <td><textarea name="objective_3_desc" class="form-control" rows="2"><?php echo htmlspecialchars($report['objective_3_desc'] ?? ''); ?></textarea></td>
                        </tr>
                        <tr>
                            <td>Menerapkan kompetensi teknis yang sudah dipelajari di sekolah</td>
                            <td><input type="number" name="objective_4_score" class="form-control" min="0" max="100" value="<?php echo $report['objective_4_score'] ?? ''; ?>"></td>
                            <td><textarea name="objective_4_desc" class="form-control" rows="2"><?php echo htmlspecialchars($report['objective_4_desc'] ?? ''); ?></textarea></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label"><h5>Catatan Tambahan</h5></label>
                <textarea name="notes" id="notes" class="form-control"><?php echo htmlspecialchars($report['notes'] ?? ''); ?></textarea>
            </div>

            <h5>Ketidakhadiran</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="absence_sick" class="form-label">Sakit (hari)</label>
                    <input type="number" name="absence_sick" id="absence_sick" class="form-control" min="0" value="<?php echo $report['absence_sick'] ?? 0; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="absence_permit" class="form-label">Izin (hari)</label>
                    <input type="number" name="absence_permit" id="absence_permit" class="form-control" min="0" value="<?php echo $report['absence_permit'] ?? 0; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="absence_unexcused" class="form-label">Tanpa Keterangan (hari)</label>
                    <input type="number" name="absence_unexcused" id="absence_unexcused" class="form-control" min="0" value="<?php echo $report['absence_unexcused'] ?? 0; ?>">
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Simpan Laporan</button>
                 <a href="../../actions/teacher/generate_pdf.php?internship_id=<?php echo $internship_id; ?>" class="btn btn-success" target="_blank">Cetak PDF</a>
            </div>
        </form>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>
