<?php
session_start();
require_once 'config/database.php';
require_once 'TCPDF/tcpdf.php';

// Security: Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("ACCESS DENIED: Please log in.");
}
if (!isset($_GET['internship_id'])) {
    die("ERROR: Internship ID not specified.");
}

$internship_id = $_GET['internship_id'];
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch all data required for the report in one query
$query = "
    SELECT
        s.name AS student_name, s.nisn, s.class, s.expertise_program, s.expertise_concentration,
        l.name AS location_name, l.instructor_name,
        i.start_date, i.end_date, i.teacher_id,
        t.name AS teacher_name, t.nip AS teacher_nip,
        r.*,
        (SELECT setting_value FROM settings WHERE setting_key = 'school_name') AS school_name,
        (SELECT setting_value FROM settings WHERE setting_key = 'academic_year') AS academic_year
    FROM internships i
    JOIN students s ON i.student_id = s.id
    JOIN teachers t ON i.teacher_id = t.id
    JOIN locations l ON i.location_id = l.id
    LEFT JOIN reports r ON i.id = r.internship_id
    WHERE i.id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $internship_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("ERROR: Report data not found.");
}

// Security: If the user is a teacher, ensure they are the one assigned to this internship
if ($role === 'teacher') {
    $stmt_teacher_check = $conn->prepare("SELECT id FROM teachers WHERE user_id = ? AND id = ?");
    $stmt_teacher_check->bind_param("ii", $user_id, $data['teacher_id']);
    $stmt_teacher_check->execute();
    if ($stmt_teacher_check->get_result()->num_rows === 0) {
        die("ACCESS DENIED: You are not authorized to view this report.");
    }
}

// ============== PDF GENERATION ===================

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PKL Report System');
$pdf->SetTitle('Report PKL - ' . $data['student_name']);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(TRUE, 15);

// Add a page
$pdf->AddPage();

// --- Header ---
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, strtoupper($data['school_name']), 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, 'TAHUN PELAJARAN ' . $data['academic_year'], 0, 1, 'C');
$pdf->Ln(10);

// --- Student Info ---
$pdf->SetFont('helvetica', '', 10);
$info_width = 45;
$pdf->Cell($info_width, 6, 'Nama Peserta Didik', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->Cell(0, 6, $data['student_name'], 0, 1);
$pdf->Cell($info_width, 6, 'NISN', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->Cell(0, 6, $data['nisn'], 0, 1);
$pdf->Cell($info_width, 6, 'Kelas', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->Cell(0, 6, $data['class'], 0, 1);
$pdf->Cell($info_width, 6, 'Program Keahlian', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->Cell(0, 6, $data['expertise_program'], 0, 1);
$pdf->Cell($info_width, 6, 'Konsentrasi Keahlian', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->Cell(0, 6, $data['expertise_concentration'], 0, 1);
$pdf->Cell($info_width, 6, 'Tempat PKL', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->Cell(0, 6, $data['location_name'], 0, 1);
$pdf->Cell($info_width, 6, 'Tanggal PKL', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->Cell(0, 6, date('d M Y', strtotime($data['start_date'])) . ' Selesai: ' . date('d M Y', strtotime($data['end_date'])), 0, 1);
$pdf->Cell($info_width, 6, 'Nama Instruktur', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->Cell(0, 6, $data['instructor_name'], 0, 1);
$pdf->Cell($info_width, 6, 'Nama Pembimbing', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->Cell(0, 6, $data['teacher_name'], 0, 1);
$pdf->Ln(5);

// --- Report Table ---
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(95, 8, 'TUJUAN PEMBELAJARAN', 1, 0, 'C', 1);
$pdf->Cell(20, 8, 'SKOR', 1, 0, 'C', 1);
$pdf->Cell(65, 8, 'DESKRIPSI', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', '', 9);

$objectives = [
    ['text' => 'Memahami alur bisnis dunia kerja tempat PKL dan wawasan wirausaha', 'score' => $data['objective_1_score'], 'desc' => $data['objective_1_desc']],
    ['text' => 'Menerapkan soft skill yang dibutuhkan dalam dunia kerja (tempat PKL)', 'score' => $data['objective_2_score'], 'desc' => $data['objective_2_desc']],
    ['text' => 'Menerapkan norma, SOP dan K3LH yang ada pada dunia kerja (tempat PKL)', 'score' => $data['objective_3_score'], 'desc' => $data['objective_3_desc']],
    ['text' => 'Menerapkan kompetensi teknis yang sudah dipelajari di sekolah dan/atau baru dipelajari pada dunia kerja (tempat PKL)', 'score' => $data['objective_4_score'], 'desc' => $data['objective_4_desc']],
];

foreach ($objectives as $obj) {
    $h = $pdf->getStringHeight(65, $obj['desc']);
    $pdf->MultiCell(95, $h, $obj['text'], 1, 'L', 0, 0);
    $pdf->MultiCell(20, $h, $obj['score'], 1, 'C', 0, 0);
    $pdf->MultiCell(65, $h, $obj['desc'], 1, 'L', 0, 1);
}

// --- Notes ---
$pdf->Ln(2);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 8, 'Catatan :', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(0, 12, $data['notes'] ?? '-', 1, 'L', 0, 1);

// --- Absence ---
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(60, 8, 'Ketidakhadiran', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(30, 6, 'Sakit', 1, 0, 'L'); $pdf->Cell(30, 6, ': ' . ($data['absence_sick'] ?? 0) . ' hari', 1, 1, 'L');
$pdf->Cell(30, 6, 'Ijin', 1, 0, 'L'); $pdf->Cell(30, 6, ': ' . ($data['absence_permit'] ?? 0) . ' hari', 1, 1, 'L');
$pdf->Cell(30, 6, 'Tanpa Keterangan', 1, 0, 'L'); $pdf->Cell(30, 6, ': ' . ($data['absence_unexcused'] ?? 0) . ' hari', 1, 1, 'L');

// --- Signatures ---
$pdf->Ln(15);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(90, 6, 'Guru Pembimbing', 0, 0, 'C');
$pdf->Cell(90, 6, 'Pembimbing Dunia Kerja', 0, 1, 'C');
$pdf->Ln(20);
$pdf->SetFont('helvetica', 'U', 10);
$pdf->Cell(90, 6, $data['teacher_name'], 0, 0, 'C');
$pdf->Cell(90, 6, $data['instructor_name'], 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(90, 6, 'NIP. ' . $data['teacher_nip'], 0, 0, 'C');
$pdf->Cell(90, 6, 'NIP. -', 0, 1, 'C');

// Close and output PDF document
$pdf->Output('report_pkl_' . str_replace(' ', '_', $data['student_name']) . '.pdf', 'I');
?>
