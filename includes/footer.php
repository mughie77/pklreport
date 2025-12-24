<?php if (isset($_SESSION['user_id'])): ?>
                <!-- Konten untuk pengguna yang sudah login berakhir di sini -->
            </main>

            <footer class="footer mt-auto py-3 bg-light border-top">
                <div class="container-fluid text-center">
                    <span class="text-muted">&copy; <?php echo date('Y'); ?> SMK Negeri 2 Bondowoso. Semua Hak Cipta Dilindungi.</span>
                </div>
            </footer>
        </div> <!-- end .main-content -->
<?php else: ?>
                <!-- Konten untuk pengguna yang belum login (halaman login) berakhir di sini -->
            </main>
        </div> <!-- end .login-background -->
<?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
