<?php
require_once '../../config/database.php';

// Logika untuk mengambil dan mengelola data siswa
$result = $conn->query("SELECT * FROM students ORDER BY name ASC");
$students = $result->fetch_all(MYSQLI_ASSOC);

$edit_student = null;
if (isset($_GET['edit_id'])) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit_id']);
    $stmt->execute();
    $edit_student = $stmt->get_result()->fetch_assoc();
}

$page_title = 'Kelola Siswa';
require_once '../../includes/header.php';
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

<div class="card mb-4 shadow-sm">
    <div class="card-header">
        <h4><?php echo $edit_student ? 'Ubah Data Siswa' : 'Tambah Siswa Baru'; ?></h4>
    </div>
    <div class="card-body">
        <form action="../../actions/admin/students.php" method="post">
            <input type="hidden" name="action" value="<?php echo $edit_student ? 'update' : 'create'; ?>">
            <?php if ($edit_student): ?>
                <input type="hidden" name="id" value="<?php echo $edit_student['id']; ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($edit_student['name'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nisn" class="form-label">NISN</label>
                    <input type="text" class="form-control" id="nisn" name="nisn" value="<?php echo htmlspecialchars($edit_student['nisn'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="class" class="form-label">Kelas</label>
                    <input type="text" class="form-control" id="class" name="class" value="<?php echo htmlspecialchars($edit_student['class'] ?? ''); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="expertise_program" class="form-label">Program Keahlian</label>
                    <input type="text" class="form-control" id="expertise_program" name="expertise_program" value="<?php echo htmlspecialchars($edit_student['expertise_program'] ?? ''); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="expertise_concentration" class="form-label">Konsentrasi Keahlian</label>
                    <input type="text" class="form-control" id="expertise_concentration" name="expertise_concentration" value="<?php echo htmlspecialchars($edit_student['expertise_concentration'] ?? ''); ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $edit_student ? 'Perbarui Siswa' : 'Tambah Siswa'; ?></button>
            <?php if ($edit_student): ?>
                <a href="students.php" class="btn btn-secondary">Batal Ubah</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h4>Data Siswa</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>Program Keahlian</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo htmlspecialchars($student['nisn']); ?></td>
                            <td><?php echo htmlspecialchars($student['class']); ?></td>
                            <td><?php echo htmlspecialchars($student['expertise_program']); ?></td>
                            <td>
                                <a href="students.php?edit_id=<?php echo $student['id']; ?>" class="btn btn-sm btn-warning">Ubah</a>
                                <form action="../../actions/admin/students.php" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus siswa ini?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>
