<?php
session_start();
require_once('../config/database.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_peran'] != 'Petugas') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $report_id = intval($_POST['report_id']);
    $id_pohon = intval($_POST['id_pohon']);
    $jenis_tindakan = $_POST['jenis_tindakan'];
    $catatan = $_POST['catatan'];
    
    $foto_lama = $_POST['foto_lama'];
    $prediksi_lama = $_POST['prediksi_lama'];
    
    // Pastikan laporan ini milik user yg login
    $cek_sql = "SELECT id FROM reports WHERE id = ? AND id_petugas = ?";
    $stmt_cek = mysqli_prepare($koneksi, $cek_sql);
    mysqli_stmt_bind_param($stmt_cek, "ii", $report_id, $_SESSION['user_id']);
    mysqli_stmt_execute($stmt_cek);
    
    // === PERBAIKAN UTAMA DI SINI (BARIS 24) ===
    $result_cek = mysqli_stmt_get_result($stmt_cek);
    if (mysqli_num_rows($result_cek) == 0) {
        die("Akses ditolak atau laporan tidak ditemukan.");
    }
    // ==========================================

    $nama_file_foto = $foto_lama;
    $status_prediksi = $prediksi_lama;

    // --- LOGIKA JIKA ADA FOTO BARU ---
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "uploads/";
        $file_extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $nama_file_baru = "report_" . $id_pohon . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $nama_file_baru;

        $allowed_types = ['jpg', 'jpeg', 'png'];
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                
                // 1. Hapus foto lama jika ada
                if (!empty($foto_lama) && file_exists($target_dir . $foto_lama)) {
                    unlink($target_dir . $foto_lama);
                }

                // 2. Update nama file
                $nama_file_foto = $nama_file_baru;

                // 3. Jalankan Python Prediksi (Sesuai konfigurasi komputer Anda)
                $pythonPath = '"C:\Program Files\Python313\python.exe"'; 
                $scriptName = 'prediksi.py';
                $imagePath = escapeshellarg($target_file);
                $projectDir = __DIR__;
                
                $command = "cd /d " . escapeshellarg($projectDir) . " && " . $pythonPath . " " . $scriptName . " " . $imagePath . " 2>&1";
                $output = shell_exec($command);
                
                $output_lines = explode("\n", trim($output));
                $jsonOutput = end($output_lines);
                $result = json_decode($jsonOutput, true);

                if ($result && !isset($result['error'])) {
                    $status_prediksi = $result['prediksi'];
                } else {
                    $status_prediksi = null; // Gagal prediksi
                }
            }
        }
    }

    // --- UPDATE DATABASE ---
    $sql = "UPDATE reports SET jenis_tindakan = ?, catatan = ?, foto = ?, status_prediksi = ? WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $jenis_tindakan, $catatan, $nama_file_foto, $status_prediksi, $report_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: tree_detail.php?id=" . $id_pohon . "&success=Laporan berhasil diperbarui");
    } else {
        header("Location: edit_report.php?id=" . $report_id . "&error=Gagal update");
    }
    exit();
}
?>