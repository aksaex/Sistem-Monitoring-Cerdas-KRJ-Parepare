<?php require_once('../includes/auth_petugas.php'); ?>
<?php
$page_title = 'Laporan';
include('../includes/header.php');
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

// Ambil ID petugas login
$id_petugas_login = $_SESSION['user_id'];

// --- LOGIKA FILTER ---
$filter_tindakan = isset($_GET['jenis_tindakan']) ? $_GET['jenis_tindakan'] : '';
$filter_tanggal_mulai = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : '';
$filter_tanggal_selesai = isset($_GET['tanggal_selesai']) ? $_GET['tanggal_selesai'] : '';

// --- PERUBAHAN SQL: Menambahkan kolom t.tanggal_tanam ---
$sql = "SELECT r.tanggal_lapor, r.jenis_tindakan, r.catatan, r.status_prediksi, t.nama_umum, t.tanggal_tanam
        FROM reports r
        JOIN trees t ON r.id_pohon = t.id
        WHERE r.id_petugas = ?";
$params = [$id_petugas_login];
$types = "i";

if (!empty($filter_tindakan)) {
    $sql .= " AND r.jenis_tindakan = ?";
    $params[] = $filter_tindakan;
    $types .= "s";
}
if (!empty($filter_tanggal_mulai)) {
    $sql .= " AND DATE(r.tanggal_lapor) >= ?";
    $params[] = $filter_tanggal_mulai;
    $types .= "s";
}
if (!empty($filter_tanggal_selesai)) {
    $sql .= " AND DATE(r.tanggal_lapor) <= ?";
    $params[] = $filter_tanggal_selesai;
    $types .= "s";
}

$sql .= " ORDER BY r.tanggal_lapor DESC";

$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 tracking-tight">Laporan Saya</h2>

    <div class="bg-green-50 p-6 rounded-xl border border-green-100 mb-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Filter Laporan</h3>
        <form action="laporan.php" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label for="jenis_tindakan" class="block text-sm font-medium text-gray-700 mb-1">Jenis Tindakan</label>
                    <select id="jenis_tindakan" name="jenis_tindakan" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                        <option value="">-- Semua Tindakan --</option>
                        <option value="Penyiraman" <?php if($filter_tindakan == 'Penyiraman') echo 'selected'; ?>>Penyiraman</option>
                        <option value="Pemupukan" <?php if($filter_tindakan == 'Pemupukan') echo 'selected'; ?>>Pemupukan</option>
                        <option value="Pemangkasan" <?php if($filter_tindakan == 'Pemangkasan') echo 'selected'; ?>>Pemangkasan</option>
                        <option value="Pengendalian Hama" <?php if($filter_tindakan == 'Pengendalian Hama') echo 'selected'; ?>>Pengendalian Hama</option>
                        <option value="Pemeriksaan Rutin" <?php if($filter_tindakan == 'Pemeriksaan Rutin') echo 'selected'; ?>>Pemeriksaan Rutin</option>
                    </select>
                </div>
                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="<?php echo htmlspecialchars($filter_tanggal_mulai); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                </div>
                <div>
                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="<?php echo htmlspecialchars($filter_tanggal_selesai); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition w-full">
                        Filter
                    </button>
                    <?php if (!empty($filter_tindakan) || !empty($filter_tanggal_mulai) || !empty($filter_tanggal_selesai)) : ?>
                        <a href="laporan.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium text-sm transition w-full text-center">
                            Reset
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="bg-green-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 font-semibold uppercase tracking-wider text-gray-600">Tanggal</th>
                    <th class="px-6 py-3 font-semibold uppercase tracking-wider text-gray-600">Nama Pohon</th>
                    <th class="px-6 py-3 font-semibold uppercase tracking-wider text-gray-600">Usia Pohon</th>
                    <th class="px-6 py-3 font-semibold uppercase tracking-wider text-gray-600">Jenis Tindakan</th>
                    <th class="px-6 py-3 font-semibold uppercase tracking-wider text-gray-600">Hasil Analisis AI</th>
                    <th class="px-6 py-3 font-semibold uppercase tracking-wider text-gray-600">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        
                        // Panggil fungsi hitungUsia
                        $usia = hitungUsia($row['tanggal_tanam']);

                        echo "<tr class='hover:bg-green-50 transition duration-150'>";
                        echo "<td class='px-6 py-4 whitespace-nowrap font-medium text-gray-900'>" . date('d M Y H:i', strtotime($row['tanggal_lapor'])) . "</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap text-gray-700'>" . htmlspecialchars($row['nama_umum']) . "</td>";
                        
                        // Kolom baru
                        echo "<td class='px-6 py-4 whitespace-nowrap text-gray-700'>$usia</td>";
                        
                        echo "<td class='px-6 py-4 whitespace-nowrap text-gray-600'>" . htmlspecialchars($row['jenis_tindakan']) . "</td>";
                        
                        echo "<td class='px-6 py-4 whitespace-nowrap'>";
                        if (!empty($row['status_prediksi'])) {
                            $status = strtolower($row['status_prediksi']);
                            $badge_class = 'bg-gray-200 text-gray-800';
                            if ($status == 'sehat') $badge_class = 'bg-green-100 text-green-800';
                            elseif ($status == 'butuh_perhatian') $badge_class = 'bg-yellow-100 text-yellow-800';
                            elseif ($status == 'kritis') $badge_class = 'bg-red-100 text-red-800';
                            echo "<span class='px-3 py-1 text-xs font-semibold rounded-full $badge_class'>" . htmlspecialchars(ucfirst($row['status_prediksi'])) . "</span>";
                        } else {
                            echo '<span class="text-gray-400 text-xs">N/A</span>';
                        }
                        echo "</td>";
                        
                        echo "<td class='px-6 py-4 whitespace-nowrap text-gray-500 italic'>" . htmlspecialchars($row['catatan']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    // Mengubah colspan menjadi 6
                    echo "<tr><td colspan='6' class='text-center text-gray-500 py-8 italic'>Tidak ada laporan yang cocok dengan filter Anda.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('../includes/footer.php'); ?>