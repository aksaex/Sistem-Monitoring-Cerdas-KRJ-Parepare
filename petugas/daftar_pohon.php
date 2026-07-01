<?php require_once('../includes/auth_petugas.php'); ?>
<?php
$page_title = 'Pohon';
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
        if ($tahun > 0) {
            $hasil[] = "$tahun tahun";
        }
        if ($bulan > 0) {
            $hasil[] = "$bulan bulan";
        }
        
        if ($tahun == 0 && $bulan == 0) {
            if ($hari == 0) {
                return 'Baru ditanam';
            }
            return "$hari hari";
        }

        return implode(', ', $hasil);

    } catch (Exception $e) {
        return '-';
    }
}

// --- LOGIKA PENCARIAN ---
$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($koneksi, $_GET['keyword']) : '';

// Kita tambahkan 'tanggal_tanam' ke SELECT
$sql = "SELECT id, id_pohon_unik, nama_umum, nama_ilmiah, tanggal_tanam FROM trees";
if (!empty($keyword)) {
    // Pastikan baris 'if' ini TIDAK dikomentari
    $sql .= " WHERE nama_umum LIKE '%$keyword%' OR id_pohon_unik LIKE '%$keyword%'";
}
$sql .= " ORDER BY id_pohon_unik ASC";
$result = mysqli_query($koneksi, $sql);
?>

<div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 tracking-tight">Daftar Pohon</h2>

    <form action="daftar_pohon.php" method="GET" class="mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <input 
                type="text" 
                name="keyword" 
                class="w-full sm:flex-1 rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-150" 
                placeholder="Cari berdasarkan Nama atau ID Pohon..." 
                value="<?php echo htmlspecialchars($keyword); ?>"
            >
            <button
                type="submit"
                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-medium shadow transition duration-150 flex items-center justify-center"
            >
                🔎 <span class="ml-2">Cari</span>
            </button>
            </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="bg-green-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 font-semibold uppercase tracking-wider text-gray-600">ID Pohon</th>
                    <th class="px-6 py-3 font-semibold uppercase tracking-wider text-gray-600">Nama Umum</th>
                    <th class="px-6 py-3 font-semibold uppercase tracking-wider text-gray-600">Nama Ilmiah</th>
                    <th class="px-6 py-3 font-semibold uppercase tracking-wider text-gray-600">Usia</th>
                    <th class="px-6 py-3 font-semibold uppercase tracking-wider text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        
                        // Panggil fungsi hitungUsia
                        $usia = hitungUsia($row['tanggal_tanam']);

                        echo "<tr class='hover:bg-green-50 transition duration-150'>"; // Pastikan baris ini AKTIF
                        echo "<td class='px-6 py-4 whitespace-nowrap font-medium text-gray-900'>" . htmlspecialchars($row['id_pohon_unik']) . "</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap text-gray-700'>" . htmlspecialchars($row['nama_umum']) . "</td>";
                        echo "<td class='px-6 py-4 whitespace-nowrap text-gray-500 italic'>" . htmlspecialchars($row['nama_ilmiah']) . "</td>";
                        
                        // Kolom baru
                        echo "<td class='px-6 py-4 whitespace-nowrap text-gray-700'>$usia</td>";
                        
                        echo "<td class='px-6 py-4 whitespace-nowrap'>"; // Pastikan baris ini AKTIF
                        echo "<a href='tree_detail.php?id=" . $row['id'] . "' class='text-green-600 hover:text-green-800 font-medium transition duration-150'>Detail & Riwayat</a>";
                        echo "</td>";
                        echo "</tr>"; // Pastikan baris ini AKTIF
                    }
                } else {
                    // Colspan diubah menjadi 5
                    echo "<tr><td colspan='5' class='text-center text-gray-500 py-8 italic'>Tidak ada data pohon yang cocok dengan pencarian Anda.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('../includes/footer.php'); ?>