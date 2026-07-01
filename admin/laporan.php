<?php 
require_once('../includes/auth_admin.php'); 
$page_title = 'Laporan';
require_once('../includes/header.php');
require_once('../config/database.php');

/**
 * Menghitung usia berdasarkan tanggal tanam.
 * @param string|null $tanggal_tanam Tanggal dalam format YYYY-MM-DD
 * @return string Usia yang diformat (cth: "2 tahun, 3 bulan") atau "-"
 */
function hitungUsia($tanggal_tanam) {
    if (empty($tanggal_tanam) || $tanggal_tanam == '0000-00-00') {
        return '-';
    }
    try {
        $tgl_tanam = new DateTime($tanggal_tanam);
        $tgl_sekarang = new DateTime('now');
        if ($tgl_tanam > $tgl_sekarang) {
            return 'Belum ditanam';
        }
        $perbedaan = $tgl_sekarang->diff($tgl_tanam);
        $tahun = $perbedaan->y;
        $bulan = $perbedaan->m;
        $hari = $perbedaan->d;
        $hasil = [];
        if ($tahun > 0) $hasil[] = "$tahun tahun";
        if ($bulan > 0) $hasil[] = "$bulan bulan";
        if ($tahun == 0 && $bulan == 0) {
            if ($hari == 0) return 'Baru ditanam';
            return "$hari hari";
        }
        return implode(', ', $hasil);
    } catch (Exception $e) {
        return '-';
    }
}

// --- LOGIKA FILTER ---
$filter_petugas = isset($_GET['id_petugas']) ? intval($_GET['id_petugas']) : '';
$filter_tanggal_mulai = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : '';
$filter_tanggal_selesai = isset($_GET['tanggal_selesai']) ? $_GET['tanggal_selesai'] : '';

$query_params = http_build_query([
    'id_petugas' => $filter_petugas,
    'tanggal_mulai' => $filter_tanggal_mulai,
    'tanggal_selesai' => $filter_tanggal_selesai
]);

// --- PERUBAHAN SQL: Menambahkan t.tanggal_tanam ---
$sql = "SELECT r.tanggal_lapor, r.jenis_tindakan, r.catatan, r.status_prediksi, 
               r.suhu_saat_lapor, r.kelembaban_tanah_saat_lapor, r.gas_saat_lapor,
               t.nama_umum, t.tanggal_tanam, u.nama AS nama_petugas
        FROM reports r
        JOIN trees t ON r.id_pohon = t.id
        JOIN users u ON r.id_petugas = u.id
        WHERE 1=1";

$params = [];
$types = "";

if (!empty($filter_petugas)) { $sql .= " AND r.id_petugas = ?"; $params[] = $filter_petugas; $types .= "i"; }
if (!empty($filter_tanggal_mulai)) { $sql .= " AND DATE(r.tanggal_lapor) >= ?"; $params[] = $filter_tanggal_mulai; $types .= "s"; }
if (!empty($filter_tanggal_selesai)) { $sql .= " AND DATE(r.tanggal_lapor) <= ?"; $params[] = $filter_tanggal_selesai; $types .= "s"; }
$sql .= " ORDER BY r.tanggal_lapor DESC";

$stmt = mysqli_prepare($koneksi, $sql);
if (!empty($types)) { mysqli_stmt_bind_param($stmt, $types, ...$params); }
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$sql_petugas = "SELECT id, nama FROM users WHERE peran = 'Petugas' ORDER BY nama ASC";
$result_petugas = mysqli_query($koneksi, $sql_petugas);

?>

<div class="bg-white p-6 sm:p-8 rounded-xl shadow-md">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Laporan Petugas</h2>
    
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
        <form action="laporan.php" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label for="id_petugas" class="block text-sm font-medium text-gray-700 mb-1">Filter Petugas</label>
                <select id="id_petugas" name="id_petugas" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    <option value="">-- Semua Petugas --</option>
                    <?php while($petugas = mysqli_fetch_assoc($result_petugas)): ?>
                        <option value="<?php echo $petugas['id']; ?>" <?php if($filter_petugas == $petugas['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($petugas['nama']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" value="<?php echo htmlspecialchars($filter_tanggal_mulai); ?>">
            </div>
            <div>
                <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" value="<?php echo htmlspecialchars($filter_tanggal_selesai); ?>">
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="w-full inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg shadow-sm hover:bg-blue-700">Filter</button>
                <a href="laporan.php" class="w-full text-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">Reset</a>
            </div>
        </form>
    </div>

    <div class="flex space-x-3 mb-4">
        <a href="unduh_laporan.php?format=csv&<?php echo $query_params; ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg shadow-md hover:bg-green-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Unduh CSV
        </a>
        <a href="unduh_laporan.php?format=pdf&<?php echo $query_params; ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg shadow-md hover:bg-red-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Unduh PDF
        </a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full bg-white divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laporan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail Pohon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tindakan & Analisis</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi IOT</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <?php
                            // === LOGIKA BARU: Panggil fungsi hitungUsia ===
                            $usia = hitungUsia($row['tanggal_tanam']);
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 align-top">
                                <div class="font-semibold text-sm"><?php echo htmlspecialchars($row['nama_petugas']); ?></div>
                                <div class="text-xs text-gray-500"><?php echo date('d M Y, H:i', strtotime($row['tanggal_lapor'])); ?></div>
                            </td>
                            
                            <td class="px-6 py-4 align-top">
                                <div class="text-sm font-semibold"><?php echo htmlspecialchars($row['nama_umum']); ?></div>
                                <div class="text-xs text-gray-500">Usia: <span class="font-medium text-gray-600"><?php echo $usia; ?></span></div>
                            </td>
                            
                            <td class="px-6 py-4 align-top">
                                <div class="font-semibold text-sm"><?php echo htmlspecialchars($row['jenis_tindakan']); ?></div>
                                <div class="text-xs italic text-gray-600 mt-1">"<?php echo htmlspecialchars($row['catatan']); ?>"</div>
                                <div class="mt-2">
                                <?php
                                    if (!empty($row['status_prediksi'])) {
                                        $status = strtolower($row['status_prediksi']);
                                        $badge_class = 'bg-gray-200 text-gray-800';
                                        if ($status == 'sehat') $badge_class = 'bg-green-100 text-green-800';
                                        elseif ($status == 'butuh_perhatian') $badge_class = 'bg-yellow-100 text-yellow-800';
                                        elseif ($status == 'kritis') $badge_class = 'bg-red-100 text-red-800';
                                        echo "<span class='px-2 py-1 text-xs font-semibold rounded-full $badge_class'>" . htmlspecialchars(ucfirst($row['status_prediksi'])) . "</span>";
                                    }
                                ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top text-xs">
                                <?php if($row['suhu_saat_lapor'] !== null): ?>
                                    <div class="flex items-center gap-1"><strong class="font-medium">Suhu:</strong> <?php echo number_format($row['suhu_saat_lapor'], 1); ?> °C</div>
                                    <div class="flex items-center gap-1"><strong class="font-medium">Tanah:</strong> <?php echo $row['kelembaban_tanah_saat_lapor']; ?></div>
                                    <div class="flex items-center gap-1"><strong class="font-medium">Gas:</strong> <?php echo $row['gas_saat_lapor']; ?></div>
                                <?php else: ?>
                                    <span class="text-gray-400">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-6 text-gray-500 italic">Tidak ada data laporan yang cocok dengan filter Anda.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
require_once('../includes/footer.php');
?>