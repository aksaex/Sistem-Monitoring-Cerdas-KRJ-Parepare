<?php
session_start(); // Wajib untuk mengakses session

// 1. Hapus semua variabel session
$_SESSION = array();

// 2. Hancurkan session
session_destroy();

// 3. Arahkan kembali ke halaman login utama dengan pesan sukses
header("Location: index.php?success=Anda telah berhasil logout.");
exit();
?>