<?php
session_start();
require_once('../config/database.php');

// Keamanan: Pastikan hanya Admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['user_peran'] != 'Admin') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil semua data dari form
    $id = intval($_POST['id']);
    $id_pohon_unik = mysqli_real_escape_string($koneksi, $_POST['id_pohon_unik']);
    $nama_umum = mysqli_real_escape_string($koneksi, $_POST['nama_umum']);
    $nama_ilmiah = mysqli_real_escape_string($koneksi, $_POST['nama_ilmiah']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $emoji = mysqli_real_escape_string($koneksi, $_POST['emoji']);

    // Ambil tanggal tanam. Cek apakah kosong, jika ya, set ke NULL.
    $tanggal_tanam_raw = mysqli_real_escape_string($koneksi, $_POST['tanggal_tanam']);
    $tanggal_tanam = !empty($tanggal_tanam_raw) ? $tanggal_tanam_raw : NULL;

    // Validasi dasar
    if (empty($id) || empty($id_pohon_unik) || empty($nama_umum)) {
        header("Location: manage_trees.php?error=Data tidak lengkap.");
        exit();
    }
    
    // Siapkan query UPDATE
    $sql = "UPDATE trees SET id_pohon_unik = ?, nama_umum = ?, nama_ilmiah = ?, deskripsi = ?, tanggal_tanam = ?, emoji = ? WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    
    // Bind 7 parameter
    mysqli_stmt_bind_param($stmt, "ssssssi", $id_pohon_unik, $nama_umum, $nama_ilmiah, $deskripsi, $tanggal_tanam, $emoji, $id);
    
    // Eksekusi query
    if (mysqli_stmt_execute($stmt)) { // <-- INI ADALAH PERBAIKANNYA
        // Jika berhasil, kembali ke halaman manajemen dengan pesan sukses
        header("Location: manage_trees.php?success=Data pohon berhasil diperbarui!");
        exit();
    } else {
        // Jika gagal, kembali dengan pesan error
        header("Location: manage_trees.php?error=Gagal memperbarui data: " . mysqli_error($koneksi));
        exit();
    }

} else {
    // Jika file diakses langsung, redirect
    header("Location: index.php");
    exit();
}
?>