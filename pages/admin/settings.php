<?php
session_start();
require_once '../../config/database.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../../login.php');
    exit();
}

// Fetch current settings
$settings = [];
$result = $conn->query("SELECT * FROM settings");
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Application Settings</h1>
            <a href="../../../admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h4>Edit Settings</h4>
            </div>
            <div class="card-body">
                <form action="../../actions/admin/settings.php" method="post">
                    <div class="mb-3">
                        <label for="school_name" class="form-label">School Name</label>
                        <input type="text" class="form-control" id="school_name" name="school_name" value="<?php echo htmlspecialchars($settings['school_name'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="academic_year" class="form-label">Academic Year</label>
                        <input type="text" class="form-control" id="academic_year" name="academic_year" value="<?php echo htmlspecialchars($settings['academic_year'] ?? ''); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
