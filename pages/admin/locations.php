<?php
session_start();
require_once '../../../config/database.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../../login.php');
    exit();
}

// Fetch all locations
$result = $conn->query("SELECT * FROM locations ORDER BY name ASC");
$locations = $result->fetch_all(MYSQLI_ASSOC);

$edit_location = null;
if (isset($_GET['edit_id'])) {
    $stmt = $conn->prepare("SELECT * FROM locations WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit_id']);
    $stmt->execute();
    $edit_location = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage PKL Locations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Manage PKL Locations</h1>
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
                <h4><?php echo $edit_location ? 'Edit Location' : 'Add New Location'; ?></h4>
            </div>
            <div class="card-body">
                <form action="../../actions/admin/locations.php" method="post">
                    <input type="hidden" name="action" value="<?php echo $edit_location ? 'update' : 'create'; ?>">
                    <?php if ($edit_location): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_location['id']; ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Location Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($edit_location['name'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($edit_location['address'] ?? ''); ?></textarea>
                    </div>
                     <div class="mb-3">
                        <label for="instructor_name" class="form-label">Instructor Name</label>
                        <input type="text" class="form-control" id="instructor_name" name="instructor_name" value="<?php echo htmlspecialchars($edit_location['instructor_name'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_location ? 'Update Location' : 'Add Location'; ?></button>
                    <?php if ($edit_location): ?>
                        <a href="locations.php" class="btn btn-secondary">Cancel Edit</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Existing Locations</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Instructor Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($locations as $location): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($location['name']); ?></td>
                                <td><?php echo htmlspecialchars($location['address']); ?></td>
                                <td><?php echo htmlspecialchars($location['instructor_name']); ?></td>
                                <td>
                                    <a href="locations.php?edit_id=<?php echo $location['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="../../actions/admin/locations.php" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this location?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $location['id']; ?>">
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
