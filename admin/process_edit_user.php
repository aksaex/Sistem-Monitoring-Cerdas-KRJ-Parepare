<?php
session_start();
require_once('../config/database.php');

// Keamanan: Pastikan hanya Admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['user_peran'] != 'Admin') {
    header("Location: ../index.php");
    exit();
}

// Cek apakah data dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil semua data dari form
    $id = intval($_POST['id']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $peran = mysqli_real_escape_string($koneksi, $_POST['peran']);
    $password_baru = $_POST['password']; // Tidak perlu escape karena akan di-handle secara berbeda

    // Validasi dasar
    if (empty($id) || empty($nama) || empty($username) || empty($peran)) {
        header("Location: manage_users.php?error=Data tidak lengkap.");
        exit();
    }
    
    // Siapkan query UPDATE dasar
    $sql = "UPDATE users SET nama = ?, username = ?, peran = ?";
    $params = [$nama, $username, $peran];
    $types = "sss";

    // Cek apakah Admin ingin mengubah password
    if (!empty($password_baru)) {
        // Jika ya, tambahkan bagian password ke query
        $sql .= ", password = ?";
        $params[] = $password_baru; // Nanti ini harus di-hash
        $types .= "s";
    }

    // Tambahkan kondisi WHERE di akhir query
    $sql .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    // Eksekusi query menggunakan prepared statement untuk keamanan
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);

    if (mysqli_stmt_execute($stmt)) {
        // Jika berhasil, simpan pesan sukses ke session
        $_SESSION['message'] = "Data pengguna berhasil diperbarui!";
        $_SESSION['message_type'] = "success";
    } else {
        // Jika gagal, simpan pesan error ke session
        $_SESSION['message'] = "Gagal memperbarui data.";
        $_SESSION['message_type'] = "error";
    }
    // SELALU kembali ke halaman manajemen pengguna setelah proses
    header("Location: manage_users.php");
    exit();


} else {
    // Jika file diakses langsung, redirect
    header("Location: index.php");
    exit();
}
?>