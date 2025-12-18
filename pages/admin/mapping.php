<?php
session_start();
require_once '../../../config/database.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../../login.php');
    exit();
}

// Fetch unassigned students, teachers, and locations for the dropdowns
$students = $conn->query("SELECT * FROM students WHERE id NOT IN (SELECT student_id FROM internships) ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$teachers = $conn->query("SELECT * FROM teachers ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$locations = $conn->query("SELECT * FROM locations ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

// Fetch existing internship mappings
$query = "SELECT i.id, s.name as student_name, t.name as teacher_name, l.name as location_name, i.start_date, i.end_date
          FROM internships i
          JOIN students s ON i.student_id = s.id
          JOIN teachers t ON i.teacher_id = t.id
          JOIN locations l ON i.location_id = l.id
          ORDER BY s.name ASC";
$mappings = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Map Internships</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Map Students to Internships</h1>
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
                <h4>Assign a New Internship</h4>
            </div>
            <div class="card-body">
                <form action="../../actions/admin/mapping.php" method="post">
                    <input type="hidden" name="action" value="create">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="student_id" class="form-label">Student</label>
                            <select class="form-control" id="student_id" name="student_id" required>
                                <option value="">Select Student</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="teacher_id" class="form-label">Supervising Teacher</label>
                            <select class="form-control" id="teacher_id" name="teacher_id" required>
                                <option value="">Select Teacher</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?php echo $teacher['id']; ?>"><?php echo htmlspecialchars($teacher['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="location_id" class="form-label">PKL Location</label>
                        <select class="form-control" id="location_id" name="location_id" required>
                             <option value="">Select Location</option>
                             <?php foreach ($locations as $location): ?>
                                <option value="<?php echo $location['id']; ?>"><?php echo htmlspecialchars($location['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Assign Internship</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Current Internship Assignments</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Teacher</th>
                            <th>Location</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mappings as $map): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($map['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($map['teacher_name']); ?></td>
                                <td><?php echo htmlspecialchars($map['location_name']); ?></td>
                                <td><?php echo htmlspecialchars($map['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($map['end_date']); ?></td>
                                <td>
                                    <form action="../../actions/admin/mapping.php" method="post" onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $map['id']; ?>">
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
