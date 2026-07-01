<?php

// --- Konfigurasi Koneksi Database ---
$db_host = "localhost";    // Server database, biasanya "localhost"
$db_user = "root";         // Username default XAMPP
$db_pass = "";             // Password default XAMPP (kosong)
$db_name = "db_krj_monitoring"; // Nama database yang baru Anda buat

// --- Membuat Koneksi ---
$koneksi = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// --- Cek Koneksi ---
// Jika koneksi gagal, hentikan script dan tampilkan pesan error.
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Jika file ini dipanggil, variabel $koneksi akan siap digunakan.
?>