<?php 
require_once('../includes/auth_admin.php'); 

// Tentukan judul halaman
$page_title = 'Dashboard';

// Panggil kerangka utama (header dan sidebar)
require_once('../includes/header.php');
require_once('../config/database.php');

// --- Logika untuk Mengambil Data Ringkasan (Tidak ada perubahan di sini) ---
// Menghitung total pengguna
$sql_users = "SELECT COUNT(id) AS total_pengguna FROM users";
$result_users = mysqli_query($koneksi, $sql_users);
$total_pengguna = mysqli_fetch_assoc($result_users)['total_pengguna'];

// Menghitung total pohon
$sql_trees = "SELECT COUNT(id) AS total_pohon FROM trees";
$result_trees = mysqli_query($koneksi, $sql_trees);
$total_pohon = mysqli_fetch_assoc($result_trees)['total_pohon'];

// Menghitung total laporan
$sql_reports = "SELECT COUNT(id) AS total_laporan FROM reports";
$result_reports = mysqli_query($koneksi, $sql_reports);
$total_laporan = mysqli_fetch_assoc($result_reports)['total_laporan'];
?>

<div class="space-y-6">
    <h2 class="text-2xl font-semibold text-gray-800">Ringkasan Sistem</h2>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-xl shadow-md flex items-center gap-4">
            <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 21a6 6 0 006-5.197M12 15a4 4 0 110-5.292m0 5.292a4 4 0 010-5.292"></path></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Total Pengguna</p>
                <p class="font-bold text-2xl text-gray-800"><?php echo $total_pengguna; ?></p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md flex items-center gap-4">
            <div class="bg-green-100 text-green-600 p-3 rounded-full">
                <span class="text-2xl">🌳</span>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Total Pohon</p>
                <p class="font-bold text-2xl text-gray-800"><?php echo $total_pohon; ?></p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md flex items-center gap-4">
            <div class="bg-yellow-100 text-yellow-600 p-3 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Total Laporan</p>
                <p class="font-bold text-2xl text-gray-800"><?php echo $total_laporan; ?></p>
            </div>
        </div>
        
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Selamat Datang, <?php echo htmlspecialchars ($_SESSION['user_nama']); ?>!</h2>
        <p class="text-gray-600">Anda login sebagai *ADMIN. Dari panel ini, Anda dapat mengelola data pengguna, data pohon dan mengunduh laporan dalam sistem. Silakan gunakan menu di sebelah kiri untuk memulai.</p>
    </div>
</div>
<?php
// Panggil kerangka penutup
require_once('../includes/footer.php');
?>