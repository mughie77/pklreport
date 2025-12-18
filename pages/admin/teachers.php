<?php
session_start();
require_once '../../../config/database.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../../login.php');
    exit();
}

// Fetch all teachers with their user info
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Teachers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Manage Teachers</h1>
            <a href="../../../admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">
                <h4><?php echo $edit_teacher ? 'Edit Teacher' : 'Add New Teacher'; ?></h4>
            </div>
            <div class="card-body">
                <form action="../../actions/admin/teachers.php" method="post">
                    <input type="hidden" name="action" value="<?php echo $edit_teacher ? 'update' : 'create'; ?>">
                    <?php if ($edit_teacher): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_teacher['id']; ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $edit_teacher['name'] ?? ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="nip" name="nip" value="<?php echo $edit_teacher['nip'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo $edit_teacher['username'] ?? ''; ?>" required <?php echo $edit_teacher ? 'disabled' : ''; ?>>
                             <?php if ($edit_teacher): ?>
                                <small class="form-text text-muted">Username cannot be changed.</small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" <?php echo $edit_teacher ? '' : 'required'; ?>>
                            <?php if ($edit_teacher): ?>
                                <small class="form-text text-muted">Leave blank to keep the current password.</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_teacher ? 'Update Teacher' : 'Add Teacher'; ?></button>
                    <?php if ($edit_teacher): ?>
                        <a href="teachers.php" class="btn btn-secondary">Cancel Edit</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Existing Teachers</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>NIP</th>
                            <th>Username</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $teacher): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($teacher['name']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['nip']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['username']); ?></td>
                                <td>
                                    <a href="teachers.php?edit_id=<?php echo $teacher['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="../../actions/admin/teachers.php" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this teacher? This will also delete their login account.');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $teacher['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
