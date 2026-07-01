<?php
require_once('../includes/auth_admin.php');
require_once('../config/database.php');
require_once('../includes/lib/fpdf.php');

/**
 * Menghitung usia berdasarkan tanggal tanam.
 * @param string|null $tanggal_tanam Tanggal dalam format YYYY-MM-DD
 * @return string Usia yang diformat (cth: "2 tahun, 3 bulan") atau "-"
 */
function hitungUsia($tanggal_tanam) {
    if (empty($tanggal_tanam) || $tanggal_tanam == '0000-00-00') {
        return '-';
    }
    try {
        $tgl_tanam = new DateTime($tanggal_tanam);
        $tgl_sekarang = new DateTime('now');
        if ($tgl_tanam > $tgl_sekarang) {
            return 'Belum ditanam';
        }
        $perbedaan = $tgl_sekarang->diff($tgl_tanam);
        $tahun = $perbedaan->y;
        $bulan = $perbedaan->m;
        $hari = $perbedaan->d;
        $hasil = [];
        if ($tahun > 0) $hasil[] = "$tahun tahun";
        if ($bulan > 0) $hasil[] = "$bulan bulan";
        if ($tahun == 0 && $bulan == 0) {
            if ($hari == 0) return 'Baru ditanam';
            return "$hari hari";
        }
        return implode(', ', $hasil);
    } catch (Exception $e) {
        return '-';
    }
}

// --- Filter dari parameter GET ---
$filter_petugas_id = isset($_GET['id_petugas']) ? intval($_GET['id_petugas']) : '';
$filter_tanggal_mulai = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : '';
$filter_tanggal_selesai = isset($_GET['tanggal_selesai']) ? $_GET['tanggal_selesai'] : '';
$format = isset($_GET['format']) ? $_GET['format'] : 'csv';

// --- Ambil nama petugas jika difilter ---
$nama_petugas_filter = "Semua Petugas";
if (!empty($filter_petugas_id)) {
    $sql_nama_petugas = "SELECT nama FROM users WHERE id = ?";
    $stmt_nama = mysqli_prepare($koneksi, $sql_nama_petugas);
    mysqli_stmt_bind_param($stmt_nama, "i", $filter_petugas_id);
    mysqli_stmt_execute($stmt_nama);
    $result_nama = mysqli_stmt_get_result($stmt_nama);
    if ($row_nama = mysqli_fetch_assoc($result_nama)) {
        $nama_petugas_filter = $row_nama['nama'];
    }
}

// --- Query utama ---
$sql = "SELECT r.tanggal_lapor, r.jenis_tindakan, r.catatan, r.status_prediksi, 
               r.suhu_saat_lapor, r.kelembaban_tanah_saat_lapor, r.gas_saat_lapor,
               t.nama_umum, t.tanggal_tanam, u.nama AS nama_petugas
        FROM reports r
        JOIN trees t ON r.id_pohon = t.id
        JOIN users u ON r.id_petugas = u.id
        WHERE 1=1";
$params = [];
$types = "";

if (!empty($filter_petugas_id)) { $sql .= " AND r.id_petugas = ?"; $params[] = $filter_petugas_id; $types .= "i"; }
if (!empty($filter_tanggal_mulai)) { $sql .= " AND DATE(r.tanggal_lapor) >= ?"; $params[] = $filter_tanggal_mulai; $types .= "s"; }
if (!empty($filter_tanggal_selesai)) { $sql .= " AND DATE(r.tanggal_lapor) <= ?"; $params[] = $filter_tanggal_selesai; $types .= "s"; }

$sql .= " ORDER BY r.tanggal_lapor DESC";

$stmt = mysqli_prepare($koneksi, $sql);
if (!empty($types)) { mysqli_stmt_bind_param($stmt, $types, ...$params); }
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$data = mysqli_fetch_all($result, MYSQLI_ASSOC);
$nama_file = "laporan_petugas_" . date('Y-m-d');

// ================== FORMAT CSV ==================
if ($format == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $nama_file . '.csv"');
    $output = fopen('php://output', 'w');

    fputcsv($output, ['Tanggal Lapor', 'Nama Petugas', 'Nama Pohon', 'Usia Pohon', 'Jenis Tindakan', 'Hasil Analisis AI', 'Catatan', 'Suhu (°C)', 'Kelembaban Tanah', 'Gas']);

    foreach ($data as $row) {
        $usia = hitungUsia($row['tanggal_tanam']);
        $csv_row = [
            date('d M Y, H:i', strtotime($row['tanggal_lapor'])),
            $row['nama_petugas'],
            $row['nama_umum'],
            $usia,
            $row['jenis_tindakan'],
            $row['status_prediksi'] ?? 'N/A',
            $row['catatan'],
            $row['suhu_saat_lapor'] !== null ? number_format($row['suhu_saat_lapor'], 1) : 'N/A',
            $row['kelembaban_tanah_saat_lapor'] ?? 'N/A',
            $row['gas_saat_lapor'] ?? 'N/A'
        ];
        fputcsv($output, $csv_row);
    }

    fclose($output);
    exit();
}

// ================== FORMAT PDF ==================
elseif ($format == 'pdf') {

    class PDF extends FPDF {
        public $filterPetugas;
        public $filterPeriode;

        function Header() {
            $this->Image('../assets/images/krj.png', 10, 8, 25);
            $this->SetFont('Arial','B',16);
            $this->Cell(277,10,'Laporan Aktivitas Petugas',0,1,'C');

            $this->SetFont('Arial','',10);
            $this->Cell(277,7, 'Petugas: ' . $this->filterPetugas, 0, 1, 'C');
            $this->Cell(277,7, 'Periode: ' . $this->filterPeriode, 0, 1, 'C');

            $this->SetFont('Arial','I',8);
            $this->Cell(277,5, 'Diunduh pada: ' . date('d M Y, H:i:s'), 0, 1, 'C');
            $this->Ln(10);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(0,10,'Halaman '.$this->PageNo().'/{nb}',0,0,'C');
        }
    }

    $pdf = new PDF('L','mm','A4');
    $pdf->filterPetugas = $nama_petugas_filter;
    $pdf->filterPeriode = (!empty($filter_tanggal_mulai) ? $filter_tanggal_mulai : 'Semua') . ' s/d ' . (!empty($filter_tanggal_selesai) ? $filter_tanggal_selesai : 'Semua');

    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',8);

    // Header Tabel
    $pdf->Cell(30, 7, 'Tanggal Lapor', 1, 0, 'C');
    $pdf->Cell(30, 7, 'Nama Petugas', 1, 0, 'C');
    $pdf->Cell(35, 7, 'Nama Pohon', 1, 0, 'C');
    $pdf->Cell(20, 7, 'Usia Pohon', 1, 0, 'C');
    $pdf->Cell(30, 7, 'Jenis Tindakan', 1, 0, 'C');
    $pdf->Cell(25, 7, 'Analisis AI', 1, 0, 'C');
    $pdf->Cell(16, 7, 'Suhu', 1, 0, 'C');
    $pdf->Cell(17, 7, 'K. Tanah', 1, 0, 'C');
    $pdf->Cell(15, 7, 'Gas', 1, 0, 'C');
    $pdf->Cell(57, 7, 'Catatan', 1, 1, 'C');

    // Body Tabel
    $pdf->SetFont('Arial','',7);

    foreach ($data as $row) {
        $usia = hitungUsia($row['tanggal_tanam']);
        $pdf->Cell(30, 6, date('d M Y, H:i', strtotime($row['tanggal_lapor'])), 1);
        $pdf->Cell(30, 6, $row['nama_petugas'], 1);
        $pdf->Cell(35, 6, $row['nama_umum'], 1);
        $pdf->Cell(20, 6, $usia, 1);
        $pdf->Cell(30, 6, $row['jenis_tindakan'], 1);
        $pdf->Cell(25, 6, $row['status_prediksi'] ?? 'N/A', 1);
        $pdf->Cell(16, 6, $row['suhu_saat_lapor'] !== null ? number_format($row['suhu_saat_lapor'], 1) . ' C' : 'N/A', 1);
        $pdf->Cell(17, 6, $row['kelembaban_tanah_saat_lapor'] ?? 'N/A', 1);
        $pdf->Cell(15, 6, $row['gas_saat_lapor'] ?? 'N/A', 1);
        $pdf->MultiCell(57, 6, $row['catatan'], 1);
    }

    // Total Laporan & Tanda Tangan
    $pdf->Ln(10);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(0, 7, 'Total Laporan: ' . count($data), 0, 1);

    $pdf->Ln(20);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(220);
    $pdf->Cell(0, 7, 'Parepare, ' . date('d M Y'), 0, 1, 'L');
    $pdf->Cell(220);
    $pdf->Cell(0, 7, 'Penanggung Jawab,', 0, 1, 'L');
    $pdf->Ln(20);
    $pdf->Cell(220);
    $pdf->Cell(0, 7, '___________________', 0, 1, 'L');

    $pdf->Output('D', $nama_file . '.pdf');
    exit();
}
?>
