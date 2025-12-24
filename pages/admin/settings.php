<?php
$page_title = 'Pengaturan Aplikasi';
require_once '../../includes/header.php';
require_once '../../config/database.php';

// Keamanan: Pastikan header.php dipanggil SEBELUM ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

// Logika untuk mengambil pengaturan
$settings = [];
$result = $conn->query("SELECT * FROM settings");
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo $page_title; ?></h1>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header">
        <h4>Ubah Pengaturan</h4>
    </div>
    <div class="card-body">
        <form action="../../actions/admin/settings.php" method="post">
            <div class="mb-3">
                <label for="school_name" class="form-label">Nama Sekolah</label>
                <input type="text" class="form-control" id="school_name" name="school_name" value="<?php echo htmlspecialchars($settings['school_name'] ?? 'SMK Negeri 2 Bondowoso'); ?>" required>
            </div>
            <div class="mb-3">
                <label for="academic_year" class="form-label">Tahun Pelajaran</label>
                <input type="text" class="form-control" id="academic_year" name="academic_year" value="<?php echo htmlspecialchars($settings['academic_year'] ?? ''); ?>" placeholder="Contoh: 2023/2024" required>
            </div>
             <div class="mb-3">
                <label for="report_date_place" class="form-label">Tempat dan Tanggal Laporan</label>
                <input type="text" class="form-control" id="report_date_place" name="report_date_place" value="<?php echo htmlspecialchars($settings['report_date_place'] ?? ''); ?>" placeholder="Contoh: Bondowoso, 31 Desember 2023" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
        </form>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>
