<?php
session_start();
require_once '../../config/database.php';
require_once '../../TCPDF/tcpdf.php';

// Keamanan: Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    die("AKSES DITOLAK: Silakan login terlebih dahulu.");
}
if (!isset($_GET['internship_id'])) {
    die("ERROR: ID PKL tidak ditentukan.");
}

$internship_id = $_GET['internship_id'];
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Ambil semua data yang diperlukan untuk laporan dalam satu query
$query = "
    SELECT
        s.name AS student_name, s.nisn, s.class, s.expertise_program, s.expertise_concentration,
        l.name AS location_name, l.address AS location_address, l.instructor_name,
        i.start_date, i.end_date, i.teacher_id,
        t.name AS teacher_name, t.nip AS teacher_nip,
        r.*,
        (SELECT setting_value FROM settings WHERE setting_key = 'school_name') AS school_name,
        (SELECT setting_value FROM settings WHERE setting_key = 'academic_year') AS academic_year,
        (SELECT setting_value FROM settings WHERE setting_key = 'report_date_place') AS report_date_place
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
    die("ERROR: Data laporan tidak ditemukan.");
}

// Keamanan: Jika pengguna adalah guru, pastikan dia adalah pembimbing yang ditugaskan
if ($role === 'teacher') {
    $stmt_teacher_check = $conn->prepare("SELECT id FROM teachers WHERE user_id = ?");
    $stmt_teacher_check->bind_param("i", $user_id);
    $stmt_teacher_check->execute();
    $teacher_check_result = $stmt_teacher_check->get_result();
    if($teacher_check_result->num_rows > 0) {
        $teacher_id = $teacher_check_result->fetch_assoc()['id'];
        if($data['teacher_id'] != $teacher_id) {
             die("AKSES DITOLAK: Anda tidak berwenang melihat laporan ini.");
        }
    } else {
         die("AKSES DITOLAK: Profil guru tidak valid.");
    }
}

// ============== PEMBUATAN PDF ===================

class PKL_PDF extends TCPDF {
     public function Header() {
        // Tidak ada header
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Halaman '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}


$pdf = new PKL_PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

// Informasi dokumen
$pdf->SetCreator('Sistem Laporan PKL');
$pdf->SetAuthor($data['teacher_name']);
$pdf->SetTitle('Laporan PKL - ' . $data['student_name']);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(TRUE, 15);

$pdf->AddPage();

// --- Header Laporan ---
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 8, 'LAPORAN HASIL PRAKTIK KERJA LAPANGAN (PKL)', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, strtoupper($data['school_name'] ?? 'NAMA SEKOLAH'), 0, 1, 'C');
$pdf->Cell(0, 8, 'TAHUN PELAJARAN ' . ($data['academic_year'] ?? 'YYYY/YYYY'), 0, 1, 'C');
$pdf->Ln(5);

// --- Informasi Siswa ---
$pdf->SetFont('helvetica', '', 10);
$lebar_kolom_info = 45;
$pdf->Cell($lebar_kolom_info, 6, 'Nama Peserta Didik', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->MultiCell(0, 6, $data['student_name'], 0, 'L', 0, 1);
$pdf->Cell($lebar_kolom_info, 6, 'NISN', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->MultiCell(0, 6, $data['nisn'], 0, 'L', 0, 1);
$pdf->Cell($lebar_kolom_info, 6, 'Kelas', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->MultiCell(0, 6, $data['class'], 0, 'L', 0, 1);
$pdf->Cell($lebar_kolom_info, 6, 'Program Keahlian', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->MultiCell(0, 6, $data['expertise_program'], 0, 'L', 0, 1);
$pdf->Cell($lebar_kolom_info, 6, 'Konsentrasi Keahlian', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->MultiCell(0, 6, $data['expertise_concentration'], 0, 'L', 0, 1);
$pdf->Cell($lebar_kolom_info, 6, 'Tempat PKL', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->MultiCell(0, 6, $data['location_name'], 0, 'L', 0, 1);
$pdf->Cell($lebar_kolom_info, 6, 'Alamat PKL', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->MultiCell(0, 6, $data['location_address'], 0, 'L', 0, 1);
$pdf->Cell($lebar_kolom_info, 6, 'Tanggal PKL', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->MultiCell(0, 6, date('d/m/Y', strtotime($data['start_date'])) . ' - ' . date('d/m/Y', strtotime($data['end_date'])), 0, 'L', 0, 1);
$pdf->Cell($lebar_kolom_info, 6, 'Nama Instruktur', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->MultiCell(0, 6, $data['instructor_name'], 0, 'L', 0, 1);
$pdf->Cell($lebar_kolom_info, 6, 'Nama Pembimbing', 0, 0); $pdf->Cell(5, 6, ':', 0, 0); $pdf->MultiCell(0, 6, $data['teacher_name'], 0, 'L', 0, 1);
$pdf->Ln(5);

// --- Tabel Laporan ---
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(95, 8, 'TUJUAN PEMBELAJARAN', 1, 0, 'C', 1);
$pdf->Cell(20, 8, 'SKOR', 1, 0, 'C', 1);
$pdf->Cell(65, 8, 'DESKRIPSI PENCAPAIAN', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', '', 9);
$pdf->SetFillColor(255, 255, 255);

$objectives = [
    ['text' => 'Memahami alur bisnis dunia kerja tempat PKL dan wawasan wirausaha', 'score' => $data['objective_1_score'], 'desc' => $data['objective_1_desc']],
    ['text' => 'Menerapkan soft skill yang dibutuhkan dalam dunia kerja (tempat PKL)', 'score' => $data['objective_2_score'], 'desc' => $data['objective_2_desc']],
    ['text' => 'Menerapkan norma, SOP dan K3LH yang ada pada dunia kerja (tempat PKL)', 'score' => $data['objective_3_score'], 'desc' => $data['objective_3_desc']],
    ['text' => 'Menerapkan kompetensi teknis yang sudah dipelajari di sekolah dan/atau baru dipelajari pada dunia kerja (tempat PKL)', 'score' => $data['objective_4_score'], 'desc' => $data['objective_4_desc']],
];

foreach ($objectives as $obj) {
    $h_text = $pdf->getStringHeight(95, $obj['text'], false, true, '', 1);
    $h_desc = $pdf->getStringHeight(65, $obj['desc'], false, true, '', 1);
    $h = max($h_text, $h_desc);

    $pdf->MultiCell(95, $h, $obj['text'], 1, 'L', 1, 0, '', '', true, 0, false, true, $h, 'M');
    $pdf->MultiCell(20, $h, $obj['score'] ?? '-', 1, 'C', 1, 0, '', '', true, 0, false, true, $h, 'M');
    $pdf->MultiCell(65, $h, $obj['desc'] ?? '-', 1, 'L', 1, 1, '', '', true, 0, false, true, $h, 'M');
}

// --- Catatan ---
$pdf->Ln(2);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 8, 'Catatan :', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(0, 12, $data['notes'] ?? '-', 1, 'L', 1, 1);

// --- Ketidakhadiran ---
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(60, 8, 'Ketidakhadiran', 1, 1, 'L', 1);
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(30, 6, 'Sakit', 1, 0, 'L', 1); $pdf->Cell(30, 6, ': ' . ($data['absence_sick'] ?? 0) . ' hari', 1, 1, 'L', 1);
$pdf->Cell(30, 6, 'Izin', 1, 0, 'L', 1); $pdf->Cell(30, 6, ': ' . ($data['absence_permit'] ?? 0) . ' hari', 1, 1, 'L', 1);
$pdf->Cell(30, 6, 'Tanpa Keterangan', 1, 0, 'L', 1); $pdf->Cell(30, 6, ': ' . ($data['absence_unexcused'] ?? 0) . ' hari', 1, 1, 'L', 1);

// --- Tanda Tangan ---
$pdf->Ln(15);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, $data['report_date_place'] ?? 'Tempat, Tanggal', 0, 1, 'R');
$pdf->Cell(90, 6, 'Guru Pembimbing,', 0, 0, 'C');
$pdf->Cell(90, 6, 'Pembimbing Dunia Kerja,', 0, 1, 'C');
$pdf->Ln(20);
$pdf->SetFont('helvetica', 'BU', 10);
$pdf->Cell(90, 6, $data['teacher_name'], 0, 0, 'C');
$pdf->Cell(90, 6, $data['instructor_name'], 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(90, 6, 'NIP. ' . $data['teacher_nip'], 0, 0, 'C');


// Menutup dan menampilkan dokumen PDF
$pdf->Output('laporan_pkl_' . str_replace(' ', '_', $data['student_name']) . '.pdf', 'I');
?>
