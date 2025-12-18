<?php
session_start();
require_once '../../../config/database.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../../login.php');
    exit();
}

// Fetch all students for display
$result = $conn->query("SELECT * FROM students ORDER BY name ASC");
$students = $result->fetch_all(MYSQLI_ASSOC);

$edit_student = null;
if (isset($_GET['edit_id'])) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit_id']);
    $stmt->execute();
    $edit_student = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Manage Students</h1>
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
                <h4><?php echo $edit_student ? 'Edit Student' : 'Add New Student'; ?></h4>
            </div>
            <div class="card-body">
                <form action="../../actions/admin/students.php" method="post">
                    <input type="hidden" name="action" value="<?php echo $edit_student ? 'update' : 'create'; ?>">
                    <?php if ($edit_student): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_student['id']; ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $edit_student['name'] ?? ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nisn" class="form-label">NISN</label>
                            <input type="text" class="form-control" id="nisn" name="nisn" value="<?php echo $edit_student['nisn'] ?? ''; ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="class" class="form-label">Class</label>
                            <input type="text" class="form-control" id="class" name="class" value="<?php echo $edit_student['class'] ?? ''; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="expertise_program" class="form-label">Expertise Program</label>
                            <input type="text" class="form-control" id="expertise_program" name="expertise_program" value="<?php echo $edit_student['expertise_program'] ?? ''; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="expertise_concentration" class="form-label">Expertise Concentration</label>
                            <input type="text" class="form-control" id="expertise_concentration" name="expertise_concentration" value="<?php echo $edit_student['expertise_concentration'] ?? ''; ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_student ? 'Update Student' : 'Add Student'; ?></button>
                    <?php if ($edit_student): ?>
                        <a href="students.php" class="btn btn-secondary">Cancel Edit</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Existing Students</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>NISN</th>
                            <th>Class</th>
                            <th>Expertise Program</th>
                            <th>Actions</th>
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
                                    <a href="students.php?edit_id=<?php echo $student['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="../../actions/admin/students.php" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
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
