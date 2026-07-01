<?php
session_start();
require_once('../config/database.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_peran'] != 'Petugas') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_pohon = intval($_POST['id_pohon']);
    $jenis_tindakan = $_POST['jenis_tindakan'];
    $catatan = $_POST['catatan'];
    $id_petugas = $_SESSION['user_id'];
    $nama_file_foto = null;
    $status_prediksi = null;

    if (empty($id_pohon) || empty($jenis_tindakan)) {
        header("Location: report_form.php?tree_id=" . $id_pohon . "&error=Data tidak lengkap");
        exit();
    }
    
    // ==========================================================
    // START: KODE BARU UNTUK MENGAMBIL DATA SENSOR TERAKHIR
    // ==========================================================
    $suhu_sekarang = null;
    $kelembaban_tanah_sekarang = null;
    $gas_sekarang = null;

    $sql_iot = "SELECT suhu, kelembaban_tanah, gas FROM data_sensor ORDER BY id DESC LIMIT 1";
    $result_iot = mysqli_query($koneksi, $sql_iot);
    if ($result_iot && mysqli_num_rows($result_iot) > 0) {
        $latest_data = mysqli_fetch_assoc($result_iot);
        $suhu_sekarang = $latest_data['suhu'];
        $kelembaban_tanah_sekarang = $latest_data['kelembaban_tanah'];
        $gas_sekarang = $latest_data['gas'];
    }
    // ==========================================================
    // END: KODE PENGAMBILAN DATA SENSOR
    // ==========================================================
    
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $nama_file_foto = "report_" . $id_pohon . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $nama_file_foto;

        $allowed_types = ['jpg', 'jpeg', 'png'];
        if (!in_array(strtolower($file_extension), $allowed_types)) {
            header("Location: report_form.php?tree_id=" . $id_pohon . "&error=Tipe file tidak valid.");
            exit();
        }

        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
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
                echo "<pre>";
                echo "Perintah yang dijalankan:\n$command\n\n";
                echo "Output mentah dari Python:\n";
                print_r($output);
                echo "\n\nHasil decode JSON:\n";
                print_r($result);
                echo "</pre>";
                exit;
            }

        } else {
            header("Location: report_form.php?tree_id=" . $id_pohon . "&error=Gagal mengunggah foto.");
            exit();
        }
    }

    // --- PERUBAHAN DI SINI: Menambahkan kolom-kolom baru ke query INSERT ---
    $sql = "INSERT INTO reports (id_pohon, id_petugas, jenis_tindakan, catatan, foto, status_prediksi, suhu_saat_lapor, kelembaban_tanah_saat_lapor, gas_saat_lapor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);
    
    // --- PERUBAHAN DI SINI: Menyesuaikan bind_param dengan 9 variabel ---
    // Tipe data: iissssidi (integer, integer, string, string, string, string, double, integer, double/integer)
    mysqli_stmt_bind_param($stmt, "iissssiii", $id_pohon, $id_petugas, $jenis_tindakan, $catatan, $nama_file_foto, $status_prediksi, $suhu_sekarang, $kelembaban_tanah_sekarang, $gas_sekarang);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: tree_detail.php?id=" . $id_pohon . "&success=Laporan berhasil disimpan");
    } else {
        header("Location: report_form.php?tree_id=" . $id_pohon . "&error=Gagal menyimpan laporan.");
    }
    exit();
}

header("Location: index.php");
exit();
?>