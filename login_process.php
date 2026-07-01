<?php
session_start();
require_once('config/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, nama, peran FROM users WHERE username = ? AND password = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nama'] = $user['nama'];
        $_SESSION['user_peran'] = $user['peran'];

        // Alamat redirect yang sudah benar (tanpa ../)
        if ($user['peran'] == 'Admin') {
            header("Location: admin/index.php");
        } else {
            header("Location: petugas/index.php");
        }
        exit();

    } else {
        header("Location: index.php?error=Username atau password salah!");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>