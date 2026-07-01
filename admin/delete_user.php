<?php
session_start();
require_once('../config/database.php');

// 1. Keamanan: Pastikan hanya Admin yang bisa mengakses fitur ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_peran'] != 'Admin') {
    // Jika bukan Admin, tendang ke halaman login
    header("Location: ../index.php");
    exit();
}

// 2. Ambil ID pengguna dari URL
// Pastikan kita mendapatkan ID dan itu adalah angka
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id_to_delete = $_GET['id'];

    // 3. Keamanan Tambahan: Admin tidak bisa menghapus akunnya sendiri
    if ($user_id_to_delete == $_SESSION['user_id']) {
        header("Location: manage_users.php?error=Anda tidak dapat menghapus akun Anda sendiri!");
        exit();
    }

    // 4. Siapkan dan eksekusi query DELETE
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    
    // Bind parameter untuk keamanan (mencegah SQL Injection)
    mysqli_stmt_bind_param($stmt, "i", $user_id_to_delete);

    if (mysqli_stmt_execute($stmt)) {
    // Jika berhasil, simpan pesan sukses ke session
    $_SESSION['message'] = "Pengguna berhasil dihapus.";
    $_SESSION['message_type'] = "success";
} else {
    // Jika gagal, simpan pesan error ke session
    $_SESSION['message'] = "Gagal menghapus pengguna.";
    $_SESSION['message_type'] = "error";
}
// Arahkan kembali ke halaman manajemen pengguna
header("Location: manage_users.php");
exit();

} else {
    // Jika tidak ada ID yang valid di URL, kembali ke halaman manajemen
    header("Location: manage_users.php");
    exit();
}
?>