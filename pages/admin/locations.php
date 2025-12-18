<?php
require_once '../../config/database.php';

// Logika untuk mengambil dan mengelola lokasi PKL
$result = $conn->query("SELECT * FROM locations ORDER BY name ASC");
$locations = $result->fetch_all(MYSQLI_ASSOC);

$edit_location = null;
if (isset($_GET['edit_id'])) {
    $stmt = $conn->prepare("SELECT * FROM locations WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit_id']);
    $stmt->execute();
    $edit_location = $stmt->get_result()->fetch_assoc();
}

$page_title = 'Kelola Lokasi PKL';
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
        <h4><?php echo $edit_location ? 'Ubah Lokasi' : 'Tambah Lokasi Baru'; ?></h4>
    </div>
    <div class="card-body">
        <form action="../../actions/admin/locations.php" method="post">
            <input type="hidden" name="action" value="<?php echo $edit_location ? 'update' : 'create'; ?>">
            <?php if ($edit_location): ?>
                <input type="hidden" name="id" value="<?php echo $edit_location['id']; ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="name" class="form-label">Nama Lokasi</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($edit_location['name'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Alamat</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($edit_location['address'] ?? ''); ?></textarea>
            </div>
             <div class="mb-3">
                <label for="instructor_name" class="form-label">Nama Pembimbing Industri</label>
                <input type="text" class="form-control" id="instructor_name" name="instructor_name" value="<?php echo htmlspecialchars($edit_location['instructor_name'] ?? ''); ?>">
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $edit_location ? 'Perbarui Lokasi' : 'Tambah Lokasi'; ?></button>
            <?php if ($edit_location): ?>
                <a href="locations.php" class="btn btn-secondary">Batal Ubah</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h4>Lokasi Tersedia</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nama Lokasi</th>
                        <th>Alamat</th>
                        <th>Pembimbing Industri</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($locations as $location): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($location['name']); ?></td>
                            <td><?php echo htmlspecialchars($location['address']); ?></td>
                            <td><?php echo htmlspecialchars($location['instructor_name']); ?></td>
                            <td>
                                <a href="locations.php?edit_id=<?php echo $location['id']; ?>" class="btn btn-sm btn-warning">Ubah</a>
                                <form action="../../actions/admin/locations.php" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lokasi ini?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $location['id']; ?>">
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
