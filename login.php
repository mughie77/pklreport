<?php
// login.php

// Mulai sesi jika belum ada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Jika pengguna sudah login, arahkan ke index.php yang akan menangani rute
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$page_title = 'Login';
require_once 'includes/header.php';
?>

<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-5">
        <div class="card shadow-lg">
            <div class="card-header">
                <h3 class="text-center">Login Sistem Laporan PKL</h3>
            </div>
            <div class="card-body p-4">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>
                <form action="auth.php" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nama Pengguna</label>
                        <input type="text" class="form-control" id="username" name="username" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
