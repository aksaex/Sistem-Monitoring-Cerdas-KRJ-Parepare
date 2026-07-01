<?php
// Selalu mulai session di awal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek 1: Apakah ada yang login?
// Cek 2: Apakah perannya adalah 'Admin'?
if (!isset($_SESSION['user_id']) || $_SESSION['user_peran'] !== 'Admin') {
    // Jika tidak memenuhi syarat, hancurkan session dan redirect ke login
    session_unset();
    session_destroy();
    header("Location: ../index.php?error=Akses ditolak. Silakan login sebagai Admin.");
    exit();
}
?>