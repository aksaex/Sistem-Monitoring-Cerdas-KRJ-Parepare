<?php
session_start();
require_once('../config/database.php');

// Keamanan: Pastikan hanya Admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['user_peran'] != 'Admin') {
    $_SESSION['message'] = "Pengguna baru berhasil ditambahkan!";
    $_SESSION['message_type'] = "success"; // Tipe pesan: 'success' atau 'error'
header("Location: manage_users.php");
    header("Location: ../index.php");
    exit();
}

// Cek apakah data dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $peran = mysqli_real_escape_string($koneksi, $_POST['peran']);

    // Validasi data tidak boleh kosong
    if (empty($nama) || empty($username) || empty($password) || empty($peran)) {
        header("Location: manage_users.php?error=Semua kolom harus diisi!");
        exit();
    }
    
    // =================================================================
    // == LANGKAH BARU: CEK APAKAH USERNAME SUDAH ADA DI DATABASE ==
    // =================================================================
    
    // 1. Siapkan query untuk mengecek username
    $sql_check = "SELECT id FROM users WHERE username = ?";
    $stmt_check = mysqli_prepare($koneksi, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $username);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    // 2. Cek hasilnya
    if (mysqli_num_rows($result_check) > 0) {
        // Jika username sudah ada (hasilnya lebih dari 0), kembali dengan pesan error yang jelas
        header("Location: manage_users.php?error=Username '" . urlencode($username) . "' sudah ada. Silakan gunakan username lain.");
        exit(); // Hentikan eksekusi script di sini
    }
    
    // =================================================================
    // == JIKA USERNAME AMAN, LANJUTKAN PROSES PENYIMPANAN ==
    // =================================================================

    // Siapkan query SQL untuk menyimpan pengguna baru
    $sql_insert = "INSERT INTO users (nama, username, password, peran) VALUES (?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($koneksi, $sql_insert);
    // "ssss" berarti keempat variabel adalah string
    mysqli_stmt_bind_param($stmt_insert, "ssss", $nama, $username, $password, $peran);

    // Eksekusi query
    if (mysqli_stmt_execute($stmt_insert)) {
        // Jika berhasil, kembali ke halaman manajemen pengguna dengan pesan sukses
        header("Location: manage_users.php?success=Pengguna baru berhasil ditambahkan!");
        exit();
    } else {
        // Jika gagal karena alasan lain, kembali dengan pesan error database
        header("Location: manage_users.php?error=Gagal menyimpan data ke database.");
        exit();
    }

} else {
    // Jika file diakses langsung, redirect
    header("Location: index.php");
    exit();
}
?>