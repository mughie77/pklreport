<?php
session_start();
require_once '../../../config/database.php';

// Security checks
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../../../login.php?error=Access denied');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['internship_id'])) {
    header('Location: ../../pages/teacher/students.php?error=Invalid request');
    exit();
}

$internship_id = $_POST['internship_id'];
$user_id = $_SESSION['user_id'];

// Get teacher_id from user_id to verify ownership
$stmt_teacher = $conn->prepare("SELECT id FROM teachers WHERE user_id = ?");
$stmt_teacher->bind_param("i", $user_id);
$stmt_teacher->execute();
$teacher_id = $stmt_teacher->get_result()->fetch_assoc()['id'];

// Verify this teacher is assigned to this internship before proceeding
$stmt_verify = $conn->prepare("SELECT id FROM internships WHERE id = ? AND teacher_id = ?");
$stmt_verify->bind_param("ii", $internship_id, $teacher_id);
$stmt_verify->execute();
if ($stmt_verify->get_result()->num_rows === 0) {
    header('Location: ../../pages/teacher/students.php?error=Authorization failed');
    exit();
}

// Prepare data from POST
$objective_1_score = $_POST['objective_1_score'] ?: null;
$objective_1_desc = $_POST['objective_1_desc'] ?: null;
$objective_2_score = $_POST['objective_2_score'] ?: null;
$objective_2_desc = $_POST['objective_2_desc'] ?: null;
$objective_3_score = $_POST['objective_3_score'] ?: null;
$objective_3_desc = $_POST['objective_3_desc'] ?: null;
$objective_4_score = $_POST['objective_4_score'] ?: null;
$objective_4_desc = $_POST['objective_4_desc'] ?: null;
$notes = $_POST['notes'] ?: null;
$absence_sick = $_POST['absence_sick'] ?? 0;
$absence_permit = $_POST['absence_permit'] ?? 0;
$absence_unexcused = $_POST['absence_unexcused'] ?? 0;


// Use INSERT ... ON DUPLICATE KEY UPDATE to handle both create and update
$query = "INSERT INTO reports (internship_id, objective_1_score, objective_1_desc, objective_2_score, objective_2_desc, objective_3_score, objective_3_desc, objective_4_score, objective_4_desc, notes, absence_sick, absence_permit, absence_unexcused)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
          ON DUPLICATE KEY UPDATE
          objective_1_score = VALUES(objective_1_score),
          objective_1_desc = VALUES(objective_1_desc),
          objective_2_score = VALUES(objective_2_score),
          objective_2_desc = VALUES(objective_2_desc),
          objective_3_score = VALUES(objective_3_score),
          objective_3_desc = VALUES(objective_3_desc),
          objective_4_score = VALUES(objective_4_score),
          objective_4_desc = VALUES(objective_4_desc),
          notes = VALUES(notes),
          absence_sick = VALUES(absence_sick),
          absence_permit = VALUES(absence_permit),
          absence_unexcused = VALUES(absence_unexcused)";

$stmt = $conn->prepare($query);
$stmt->bind_param("isississisiii",
    $internship_id,
    $objective_1_score, $objective_1_desc,
    $objective_2_score, $objective_2_desc,
    $objective_3_score, $objective_3_desc,
    $objective_4_score, $objective_4_desc,
    $notes,
    $absence_sick, $absence_permit, $absence_unexcused
);

if ($stmt->execute()) {
    header("Location: ../../pages/teacher/grades.php?internship_id=$internship_id&success=Report saved successfully");
} else {
    error_log($stmt->error);
    header("Location: ../../pages/teacher/grades.php?internship_id=$internship_id&error=An unexpected error occurred.");
}
exit();
?>
