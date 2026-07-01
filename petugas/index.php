<?php 
require_once('../includes/auth_petugas.php'); 

// Tentukan judul halaman
$page_title = 'Dashboard';

// Panggil kerangka utama
require_once('../includes/header.php');
require_once('../config/database.php');

// ==========================================================
// BAGIAN 1: KODE PENGAMBILAN DATA IOT (TETAP SAMA)
// ==========================================================

$sql_iot = "SELECT suhu, kelembaban_udara, kelembaban_tanah, gas FROM data_sensor ORDER BY id DESC LIMIT 1";
$result_iot = mysqli_query($koneksi, $sql_iot);

$suhu = "N/A";
$kelembaban_udara = "N/A";
$kelembaban_tanah = "N/A";
$gas_nilai = "N/A";
$status_gas = 'Aman';
$class_gas_bg = 'bg-green-100';
$class_gas_text = 'text-green-800';
$status_suhu = 'Optimal';
$class_suhu_bg = 'bg-blue-100';
$class_suhu_text = 'text-blue-800';
$status_tanah = 'Baik';
$class_tanah_bg = 'bg-yellow-100';
$class_tanah_text = 'text-yellow-800';

if ($result_iot && mysqli_num_rows($result_iot) > 0) {
    $latest_data = mysqli_fetch_assoc($result_iot);
    $suhu = number_format($latest_data['suhu'], 1);
    $kelembaban_udara = round($latest_data['kelembaban_udara']);
    $gas_nilai = $latest_data['gas'];
    $kelembaban_tanah_mentah = $latest_data['kelembaban_tanah'];
    $kelembaban_tanah = round((1 - ($kelembaban_tanah_mentah / 4095.0)) * 100);

    if ($gas_nilai > 1500) {
        $status_gas = 'Bahaya Asap';
        $class_gas_bg = 'bg-red-100';
        $class_gas_text = 'text-red-800';
    }
    if ($suhu > 30.0 && $suhu != "N/A") {
        $status_suhu = 'Panas';
        $class_suhu_bg = 'bg-red-100';
        $class_suhu_text = 'text-red-800';
    }
    if ($kelembaban_tanah != "N/A") {
        if ($kelembaban_tanah < 20) {
            $status_tanah = 'Kering Kritis';
            $class_tanah_bg = 'bg-red-100';
            $class_tanah_text = 'text-red-800';
        } elseif ($kelembaban_tanah > 80) {
            $status_tanah = 'Terlalu Basah';
            $class_tanah_bg = 'bg-blue-100';
            $class_tanah_text = 'text-blue-800';
        }
    }
}
// ==========================================================
// AKHIR DARI KODE IOT
// ==========================================================

// ==========================================================
// START: KODE BARU UNTUK SISTEM CERDAS
// ==========================================================
$sql_prediksi = "SELECT r.id_pohon, r.foto, r.status_prediksi, r.tanggal_lapor, t.nama_umum
                 FROM reports r
                 JOIN trees t ON r.id_pohon = t.id
                 WHERE r.status_prediksi IS NOT NULL AND r.foto IS NOT NULL
                 ORDER BY r.tanggal_lapor DESC
                 LIMIT 5"; // Kita ambil 5 laporan terbaru

$result_prediksi = mysqli_query($koneksi, $sql_prediksi);
// ==========================================================
// END: KODE BARU UNTUK SISTEM CERDAS
// ==========================================================
?>

<div class="space-y-6">

    <?php if ($status_tanah == 'Kering Kritis' || $status_gas == 'Bahaya Asap'): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-md" role="alert">
        <div class="flex items-center">
            <div class="py-1">
                <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zM9 15v-2h2v2H9zm2-4H9V5h2v6z"/></svg>
            </div>
            <div>
                <p class="font-bold">Peringatan Kondisi Kritis!</p>
                <ul class="list-disc list-inside mt-1 text-sm">
                    <?php if ($status_tanah == 'Kering Kritis'): ?>
                        <li>Kelembaban tanah terdeteksi <strong>SANGAT KERING</strong>. Segera lakukan penyiraman.</li>
                    <?php endif; ?>
                    <?php if ($status_gas == 'Bahaya Asap'): ?>
                        <li>Sensor gas mendeteksi <strong>LEVEL ASAP BERBAHAYA</strong>. Segera lakukan pemeriksaan di lokasi.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Data Lingkungan IOT</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                <p class="text-gray-500 text-sm">Status Gas (MQ-2)</p>
                <p class="font-bold text-3xl text-gray-800 mt-1"><?php echo $gas_nilai; ?></p>
                <div class="mt-3"><span class="px-3 py-1 text-xs font-semibold rounded-full <?php echo $class_gas_bg . ' ' . $class_gas_text; ?>"><?php echo $status_gas; ?></span></div>
            </div>
            <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                <p class="text-gray-500 text-sm">Suhu Udara</p>
                <p class="font-bold text-3xl text-gray-800 mt-1"><?php echo $suhu; ?>°C</p>
                <div class="mt-3"><span class="px-3 py-1 text-xs font-semibold rounded-full <?php echo $class_suhu_bg . ' ' . $class_suhu_text; ?>"><?php echo $status_suhu; ?></span></div>
            </div>
            <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                <p class="text-gray-500 text-sm">Kelembapan Tanah</p>
                <p class="font-bold text-3xl text-gray-800 mt-1"><?php echo $kelembaban_tanah; ?>%</p>
                <div class="mt-3"><span class="px-3 py-1 text-xs font-semibold rounded-full <?php echo $class_tanah_bg . ' ' . $class_tanah_text; ?>"><?php echo $status_tanah; ?></span></div>
            </div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Hasil Analisis AI</h2>
        
        <?php if ($result_prediksi && mysqli_num_rows($result_prediksi) > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5">
                <?php while($row = mysqli_fetch_assoc($result_prediksi)): ?>
                    <?php
                        // Logika untuk warna badge status
                        $status = strtolower($row['status_prediksi']);
                        $badge_class = 'bg-gray-200 text-gray-800';
                        if ($status == 'sehat') {
                            $badge_class = 'bg-green-100 text-green-800';
                        } elseif ($status == 'butuh_perhatian') {
                            $badge_class = 'bg-yellow-100 text-yellow-800';
                        } elseif ($status == 'kritis') {
                            $badge_class = 'bg-red-100 text-red-800';
                        }
                    ?>
                    <a href="tree_detail.php?id=<?php echo $row['id_pohon']; ?>" class="block border border-gray-200 rounded-lg hover:shadow-lg transition-shadow duration-200 bg-gray-50">
                        <img src="uploads/<?php echo htmlspecialchars($row['foto']); ?>" alt="Foto Pohon" class="w-full h-40 object-cover rounded-t-lg">
                        <div class="p-4">
                            <p class="font-semibold text-gray-800 truncate"><?php echo htmlspecialchars($row['nama_umum']); ?></p>
                            <p class="text-sm text-gray-500 mb-2"><?php echo date('d M Y, H:i', strtotime($row['tanggal_lapor'])); ?></p>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full <?php echo $badge_class; ?>">
                                <?php echo htmlspecialchars(ucfirst($row['status_prediksi'])); ?>
                            </span>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500 py-4">Belum ada laporan dengan hasil analisis. Unggah laporan baru dengan foto untuk melihat hasilnya di sini.</p>
        <?php endif; ?>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Akses ke Daftar Pohon</h2>
        <p class="text-gray-600 mb-4">Untuk melihat semua pohon yang terdaftar dalam sistem dan membuat laporan.</p>
        <a href="daftar_pohon.php" class="btn-primary-action">Lihat Semua Pohon</a>
    </div>
</div>
<?php
// Panggil kerangka penutup
require_once('../includes/footer.php');
?>