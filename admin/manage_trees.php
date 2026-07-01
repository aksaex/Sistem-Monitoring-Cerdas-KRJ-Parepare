<?php 
require_once('../includes/auth_admin.php');
$page_title = 'Pohon';
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
        
        // Cek jika tanggal tanam ada di masa depan
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
        
        // Jika sangat muda (kurang dari 1 bulan)
        if ($tahun == 0 && $bulan == 0) {
            if ($hari == 0) {
                return 'Baru ditanam'; // Ditanam hari ini
            }
            return "$hari hari";
        }

        return implode(', ', $hasil);

    } catch (Exception $e) {
        // Tangani jika format tanggal salah
        return '-';
    }
}
?>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-xl shadow-md">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold text-gray-800">Daftar Pohon</h2>
            <a href="add_tree.php" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                Tambah
            </a>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Unik</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Umum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Ilmiah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl. Tanam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    // Kita tambahkan 'tanggal_tanam' ke SELECT
                    $sql = "SELECT id, id_pohon_unik, nama_umum, nama_ilmiah, deskripsi, tanggal_tanam FROM trees ORDER BY id ASC";
                    $result = mysqli_query($koneksi, $sql); // Pastikan baris ini AKTIF

                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            // Panggil fungsi hitungUsia
                            $usia = hitungUsia($row['tanggal_tanam']); 
                            
                            // Format tanggal tanam agar lebih rapi (cth: 10 Nov 2025)
                            $tgl_tanam_formatted = $row['tanggal_tanam'] ? date('d M Y', strtotime($row['tanggal_tanam'])) : '-';
                            
                            echo "<tr class='hover:bg-gray-50'>"; // Pastikan baris ini AKTIF
                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>" . htmlspecialchars($row['id_pohon_unik']) . "</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-700'>" . htmlspecialchars($row['nama_umum']) . "</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500 italic'>" . htmlspecialchars($row['nama_ilmiah']) . "</td>";
                            
                            // Kolom baru
                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-700'>$usia</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>$tgl_tanam_formatted</td>";
                            
                            echo "<td class='px-6 py-4 text-sm text-gray-500 max-w-xs truncate' title='" . htmlspecialchars($row['deskripsi']) . "'>" . htmlspecialchars($row['deskripsi']) . "</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-center'>";
                            echo "<a href='edit_tree.php?id=" . $row['id'] . "' class='text-blue-600 hover:text-blue-800 font-medium mr-3'>Edit</a>";
                            echo "<a href='delete_tree.php?id=" . $row['id'] . "' class='text-red-600 hover:text-red-800 font-medium' onclick=\"return confirm('Yakin ingin hapus pohon ini?');\">Hapus</a>";
                            echo "</td>";
                            echo "</tr>"; // Pastikan baris ini AKTIF
                        }
                    } else {
                        // Tadinya 5, sekarang jadi 7 (karena tambah 2 kolom)
                        echo "<tr><td colspan='7' class='text-center text-gray-500 py-6'>Tidak ada data pohon yang terdaftar.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once('../includes/footer.php');
?>