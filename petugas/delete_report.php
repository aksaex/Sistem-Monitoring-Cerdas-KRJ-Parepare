<?php
session_start();
require_once('../config/database.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_peran'] != 'Petugas') {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['id'])) {
    $report_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // 1. Ambil data foto dan id_pohon sebelum menghapus (untuk redirect dan hapus file)
    $sql_check = "SELECT foto, id_pohon FROM reports WHERE id = ? AND id_petugas = ?";
    $stmt_check = mysqli_prepare($koneksi, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "ii", $report_id, $user_id);
    mysqli_stmt_execute($stmt_check);
    $result = mysqli_stmt_get_result($stmt_check);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $foto = $row['foto'];
        $id_pohon = $row['id_pohon'];

        // 2. Hapus file fisik jika ada
        if (!empty($foto) && file_exists("uploads/" . $foto)) {
            unlink("uploads/" . $foto);
        }

        // 3. Hapus data dari database
        $sql_delete = "DELETE FROM reports WHERE id = ?";
        $stmt_delete = mysqli_prepare($koneksi, $sql_delete);
        mysqli_stmt_bind_param($stmt_delete, "i", $report_id);
        
        if (mysqli_stmt_execute($stmt_delete)) {
            header("Location: tree_detail.php?id=" . $id_pohon . "&success=Laporan dihapus");
        } else {
            header("Location: tree_detail.php?id=" . $id_pohon . "&error=Gagal menghapus database");
        }

    } else {
        // Laporan tidak ditemukan atau bukan milik user ini
        header("Location: index.php?error=Akses ditolak");
    }
} else {
    header("Location: index.php");
}
?>