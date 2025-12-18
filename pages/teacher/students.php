<?php
session_start();
require_once '../../config/database.php';

// Redirect if not logged in or not a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../../../login.php');
    exit();
}

// Get the teacher's ID from their user ID
$user_id = $_SESSION['user_id'];
$stmt_teacher = $conn->prepare("SELECT id FROM teachers WHERE user_id = ?");
$stmt_teacher->bind_param("i", $user_id);
$stmt_teacher->execute();
$teacher_id = $stmt_teacher->get_result()->fetch_assoc()['id'];

if (!$teacher_id) {
    // Handle case where teacher profile doesn't exist for the user
    die("Teacher profile not found.");
}

// Fetch students assigned to this teacher
$query = "SELECT s.name as student_name, s.nisn, s.class, l.name as location_name, i.id as internship_id
          FROM internships i
          JOIN students s ON i.student_id = s.id
          JOIN locations l ON i.location_id = l.id
          WHERE i.teacher_id = ?
          ORDER BY s.name ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$assigned_students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assigned Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Assigned Students</h1>
            <a href="../../../teacher_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Your Students for PKL</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>NISN</th>
                            <th>Class</th>
                            <th>PKL Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($assigned_students)): ?>
                            <tr>
                                <td colspan="5" class="text-center">You have no students assigned yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($assigned_students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['nisn']); ?></td>
                                    <td><?php echo htmlspecialchars($student['class']); ?></td>
                                    <td><?php echo htmlspecialchars($student['location_name']); ?></td>
                                    <td>
                                        <a href="grades.php?internship_id=<?php echo $student['internship_id']; ?>" class="btn btn-sm btn-primary">Manage Report</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
