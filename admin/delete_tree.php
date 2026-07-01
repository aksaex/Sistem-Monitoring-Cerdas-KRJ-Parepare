<?php
session_start();
require_once('../config/database.php');

// Keamanan: Pastikan hanya Admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['user_peran'] != 'Admin') {
    header("Location: ../index.php");
    exit();
}

// Ambil ID pohon dari URL dan pastikan itu adalah angka
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $tree_id_to_delete = $_GET['id'];

    // Siapkan dan eksekusi query DELETE menggunakan prepared statement
    $sql = "DELETE FROM trees WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    
    // Bind parameter untuk keamanan
    mysqli_stmt_bind_param($stmt, "i", $tree_id_to_delete);

    if (mysqli_stmt_execute($stmt)) {
        // Jika berhasil, kembali ke halaman manajemen dengan pesan sukses
        header("Location: manage_trees.php?success=Data pohon berhasil dihapus.");
        exit();
    } else {
        // Jika gagal, kembali dengan pesan error
        header("Location: manage_trees.php?error=Gagal menghapus data pohon.");
        exit();
    }

} else {
    // Jika tidak ada ID yang valid di URL, kembali ke halaman manajemen
    header("Location: manage_trees.php");
    exit();
}
?>