<?php
// End-to-end test script for the PKL Report application
// To run: `php run_tests.php` from the project root.

require_once 'config/database.php';

// A simple assertion function for testing
function assert_test($condition, $message) {
    if (!$condition) {
        echo "[FAIL] $message\n";
        // In a real scenario, you might want to exit or log failures
    } else {
        echo "[PASS] $message\n";
    }
}

echo "Starting PKL Report Application Tests...\n\n";

// ===================================
// Test 1: Admin - Student Management
// ===================================
echo "--- Testing Admin: Student Management ---\n";

// Create
$conn->query("INSERT INTO students (name, nisn, class) VALUES ('Test Student', '12345', 'XII-A')");
$student_id = $conn->insert_id;
assert_test($student_id > 0, "Create student");

// Read
$student = $conn->query("SELECT * FROM students WHERE id = $student_id")->fetch_assoc();
assert_test($student && $student['name'] === 'Test Student', "Read student");

// Update
$conn->query("UPDATE students SET name = 'Test Student Updated' WHERE id = $student_id");
$updated_student = $conn->query("SELECT * FROM students WHERE id = $student_id")->fetch_assoc();
assert_test($updated_student && $updated_student['name'] === 'Test Student Updated', "Update student");

// Delete
$conn->query("DELETE FROM students WHERE id = $student_id");
$deleted_student = $conn->query("SELECT * FROM students WHERE id = $student_id")->fetch_assoc();
assert_test($deleted_student === null, "Delete student");

// ===================================
// Test 2: Admin - Teacher Management
// ===================================
echo "\n--- Testing Admin: Teacher Management ---\n";

// Create (User and Teacher)
$hashed_password = password_hash('password', PASSWORD_DEFAULT);
$conn->query("INSERT INTO users (username, password, role) VALUES ('testteacher', '$hashed_password', 'teacher')");
$user_id = $conn->insert_id;
$conn->query("INSERT INTO teachers (user_id, name, nip) VALUES ($user_id, 'Test Teacher', '112233')");
$teacher_id = $conn->insert_id;
assert_test($teacher_id > 0, "Create teacher and user");

// Read
$teacher = $conn->query("SELECT * FROM teachers WHERE id = $teacher_id")->fetch_assoc();
assert_test($teacher && $teacher['name'] === 'Test Teacher', "Read teacher");

// Update
$conn->query("UPDATE teachers SET name = 'Test Teacher Updated' WHERE id = $teacher_id");
$updated_teacher = $conn->query("SELECT * FROM teachers WHERE id = $teacher_id")->fetch_assoc();
assert_test($updated_teacher && $updated_teacher['name'] === 'Test Teacher Updated', "Update teacher");

// Delete (User deletion cascades to teacher)
$conn->query("DELETE FROM users WHERE id = $user_id");
$deleted_teacher = $conn->query("SELECT * FROM teachers WHERE id = $teacher_id")->fetch_assoc();
$deleted_user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
assert_test($deleted_teacher === null && $deleted_user === null, "Delete teacher and user");


// ===================================
// Test 3: Admin - Location Management
// ===================================
echo "\n--- Testing Admin: Location Management ---\n";

// Create
$conn->query("INSERT INTO locations (name, address) VALUES ('Test Location', '123 Test St')");
$location_id = $conn->insert_id;
assert_test($location_id > 0, "Create location");

// Read
$location = $conn->query("SELECT * FROM locations WHERE id = $location_id")->fetch_assoc();
assert_test($location && $location['name'] === 'Test Location', "Read location");

// Update
$conn->query("UPDATE locations SET name = 'Test Location Updated' WHERE id = $location_id");
$updated_location = $conn->query("SELECT * FROM locations WHERE id = $location_id")->fetch_assoc();
assert_test($updated_location && $updated_location['name'] === 'Test Location Updated', "Update location");

// Delete
$conn->query("DELETE FROM locations WHERE id = $location_id");
$deleted_location = $conn->query("SELECT * FROM locations WHERE id = $location_id")->fetch_assoc();
assert_test($deleted_location === null, "Delete location");

// ======================================
// Test 4: Admin - Internship Mapping
// ======================================
echo "\n--- Testing Admin: Internship Mapping ---\n";
// Setup data
$conn->query("INSERT INTO students (name, nisn, class) VALUES ('Mapping Student', '54321', 'XII-B')");
$student_map_id = $conn->insert_id;
$conn->query("INSERT INTO users (username, password, role) VALUES ('mapteacher', '$hashed_password', 'teacher')");
$user_map_id = $conn->insert_id;
$conn->query("INSERT INTO teachers (user_id, name, nip) VALUES ($user_map_id, 'Mapping Teacher', '332211')");
$teacher_map_id = $conn->insert_id;
$conn->query("INSERT INTO locations (name, address) VALUES ('Mapping Location', '456 Map Ave')");
$location_map_id = $conn->insert_id;

// Create Mapping
$conn->query("INSERT INTO internships (student_id, teacher_id, location_id, start_date, end_date) VALUES ($student_map_id, $teacher_map_id, $location_map_id, '2025-01-01', '2025-03-01')");
$internship_id = $conn->insert_id;
assert_test($internship_id > 0, "Create internship mapping");

// Read Mapping
$mapping = $conn->query("SELECT * FROM internships WHERE id = $internship_id")->fetch_assoc();
assert_test($mapping && $mapping['student_id'] == $student_map_id, "Read internship mapping");

// Delete Mapping
$conn->query("DELETE FROM internships WHERE id = $internship_id");
$deleted_mapping = $conn->query("SELECT * FROM internships WHERE id = $internship_id")->fetch_assoc();
assert_test($deleted_mapping === null, "Delete internship mapping");

// Cleanup
$conn->query("DELETE FROM students WHERE id = $student_map_id");
$conn->query("DELETE FROM users WHERE id = $user_map_id");
$conn->query("DELETE FROM locations WHERE id = $location_map_id");

// ======================================
// Test 5: Teacher - Grade Management
// ======================================
echo "\n--- Testing Teacher: Grade Management ---\n";
// Re-create data for this test
$conn->query("INSERT INTO students (name, nisn, class) VALUES ('Grade Student', '67890', 'XII-C')");
$student_grade_id = $conn->insert_id;
$conn->query("INSERT INTO users (username, password, role) VALUES ('gradeteacher', '$hashed_password', 'teacher')");
$user_grade_id = $conn->insert_id;
$conn->query("INSERT INTO teachers (user_id, name, nip) VALUES ($user_grade_id, 'Grade Teacher', '445566')");
$teacher_grade_id = $conn->insert_id;
$conn->query("INSERT INTO locations (name, address) VALUES ('Grade Location', '789 Grade Rd')");
$location_grade_id = $conn->insert_id;
$conn->query("INSERT INTO internships (student_id, teacher_id, location_id, start_date, end_date) VALUES ($student_grade_id, $teacher_grade_id, $location_grade_id, '2025-01-01', '2025-03-01')");
$internship_grade_id = $conn->insert_id;

// Create Report (Upsert)
$conn->query("INSERT INTO reports (internship_id, objective_1_score, notes) VALUES ($internship_grade_id, 85, 'Good start') ON DUPLICATE KEY UPDATE objective_1_score=VALUES(objective_1_score), notes=VALUES(notes)");
$report = $conn->query("SELECT * FROM reports WHERE internship_id = $internship_grade_id")->fetch_assoc();
assert_test($report && $report['objective_1_score'] == 85, "Create (upsert) a report");

// Update Report (Upsert)
$conn->query("INSERT INTO reports (internship_id, objective_1_score, notes) VALUES ($internship_grade_id, 90, 'Excellent progress') ON DUPLICATE KEY UPDATE objective_1_score=VALUES(objective_1_score), notes=VALUES(notes)");
$updated_report = $conn->query("SELECT * FROM reports WHERE internship_id = $internship_grade_id")->fetch_assoc();
assert_test($updated_report && $updated_report['objective_1_score'] == 90 && $updated_report['notes'] === 'Excellent progress', "Update (upsert) a report");

// Cleanup
$conn->query("DELETE FROM internships WHERE id = $internship_grade_id");
$conn->query("DELETE FROM students WHERE id = $student_grade_id");
$conn->query("DELETE FROM users WHERE id = $user_grade_id");
$conn->query("DELETE FROM locations WHERE id = $location_grade_id");

echo "\nAll tests completed.\n";
?>
