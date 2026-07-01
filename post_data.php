<?php
// ==========================================================
// post_data.php
// Script ini menerima data POST dari ESP32 dan memasukkannya ke database.
// ==========================================================

// Kredensial Database
$servername = "localhost";
$username = "root";     // Default XAMPP
$password = "";         // Default XAMPP
$dbname = "db_krj_monitoring"; // GANTI dengan nama database Anda

// Ambil data yang dikirim ESP32 melalui HTTP POST
// Nama variabel di sini HARUS sesuai dengan 'postPayload' di kode ESP32.
$suhu = isset($_POST["suhu"]) ? $_POST["suhu"] : ''; 
$kelembaban_udara = isset($_POST["kelembaban_udara"]) ? $_POST["kelembaban_udara"] : '';
$kelembaban_tanah = isset($_POST["kelembaban_tanah"]) ? $_POST["kelembaban_tanah"] : '';
$gas = isset($_POST["gas"]) ? $_POST["gas"] : '';

// Membuat Koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek Koneksi
if ($conn->connect_error) {
    // Beri tahu ESP32 jika koneksi gagal (Error 500)
    http_response_code(500); 
    die("Koneksi database gagal: " . $conn->connect_error);
} 

// SQL untuk menyisipkan data
$sql = "INSERT INTO data_sensor (suhu, kelembaban_udara, kelembaban_tanah, gas)
        VALUES ('$suhu', '$kelembaban_udara', '$kelembaban_tanah', '$gas')";

if ($conn->query($sql) === TRUE) {
    // Beri respons sukses (HTTP Code 200)
    echo "Data berhasil dimasukkan";
} else {
    // Beri tahu ESP32 jika ada error SQL
    http_response_code(500);
    echo "Error SQL: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>