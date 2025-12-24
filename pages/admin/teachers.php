<?php
$page_title = 'Kelola Guru';
require_once '../../includes/header.php';
require_once '../../config/database.php';

// Keamanan: Pastikan header.php dipanggil SEBELUM ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

// Logika untuk mengambil dan mengelola data guru
$query = "SELECT t.id, t.name, t.nip, u.username
          FROM teachers t
          JOIN users u ON t.user_id = u.id
          ORDER BY t.name ASC";
$result = $conn->query($query);
$teachers = $result->fetch_all(MYSQLI_ASSOC);

$edit_teacher = null;
if (isset($_GET['edit_id'])) {
    $stmt = $conn->prepare("SELECT t.id, t.name, t.nip, u.username
                            FROM teachers t
                            JOIN users u ON t.user_id = u.id
                            WHERE t.id = ?");
    $stmt->bind_param("i", $_GET['edit_id']);
    $stmt->execute();
    $edit_teacher = $stmt->get_result()->fetch_assoc();
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

<div class="card mb-4 shadow-sm">
    <div class="card-header">
        <h4><?php echo $edit_teacher ? 'Ubah Data Guru' : 'Tambah Guru Baru'; ?></h4>
    </div>
    <div class="card-body">
        <form action="../../actions/admin/teachers.php" method="post">
            <input type="hidden" name="action" value="<?php echo $edit_teacher ? 'update' : 'create'; ?>">
            <?php if ($edit_teacher): ?>
                <input type="hidden" name="id" value="<?php echo $edit_teacher['id']; ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($edit_teacher['name'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nip" class="form-label">NIP</label>
                    <input type="text" class="form-control" id="nip" name="nip" value="<?php echo htmlspecialchars($edit_teacher['nip'] ?? ''); ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Nama Pengguna (Username)</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($edit_teacher['username'] ?? ''); ?>" required <?php echo $edit_teacher ? 'disabled' : ''; ?>>
                     <?php if ($edit_teacher): ?>
                        <small class="form-text text-muted">Username tidak dapat diubah.</small>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Kata Sandi (Password)</label>
                    <input type="password" class="form-control" id="password" name="password" <?php echo $edit_teacher ? '' : 'required'; ?>>
                    <?php if ($edit_teacher): ?>
                        <small class="form-text text-muted">Biarkan kosong untuk mempertahankan kata sandi saat ini.</small>
                    <?php endif; ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $edit_teacher ? 'Perbarui Guru' : 'Tambah Guru'; ?></button>
            <?php if ($edit_teacher): ?>
                <a href="teachers.php" class="btn btn-secondary">Batal Ubah</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h4>Data Guru</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Username</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teachers as $teacher): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($teacher['name']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['nip']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['username']); ?></td>
                            <td>
                                <a href="teachers.php?edit_id=<?php echo $teacher['id']; ?>" class="btn btn-sm btn-warning">Ubah</a>
                                <form action="../../actions/admin/teachers.php" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus guru ini? Ini juga akan menghapus akun login mereka.');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $teacher['id']; ?>">
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
