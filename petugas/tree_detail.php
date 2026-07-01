<?php
// Panggil file koneksi database terlebih dahulu
require_once('../config/database.php');
require_once('../includes/auth_petugas.php'); // Pastikan auth dipanggil

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

// Ambil ID pohon dari URL (?id=...)
$tree_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($tree_id <= 0) {
    header("Location: index.php");
    exit();
}

// Query untuk mengambil detail pohon spesifik (DIAMANKAN DGN PREPARED STATEMENT)
$sql_tree = "SELECT * FROM trees WHERE id = ?";
$stmt_tree = mysqli_prepare($koneksi, $sql_tree);
mysqli_stmt_bind_param($stmt_tree, "i", $tree_id);
mysqli_stmt_execute($stmt_tree);
$result_tree = mysqli_stmt_get_result($stmt_tree);

if (mysqli_num_rows($result_tree) == 0) {
    die("Pohon tidak ditemukan.");
}

$pohon = mysqli_fetch_assoc($result_tree);

// === LOGIKA BARU UNTUK HITUNG USIA ===
// Panggil fungsi hitungUsia
$usia = hitungUsia($pohon['tanggal_tanam']); 
// Format tanggal tanam
$tgl_tanam_formatted = $pohon['tanggal_tanam'] ? date('d M Y', strtotime($pohon['tanggal_tanam'])) : '-';
// =====================================

// Set judul halaman
$page_title = 'Detail: ' . htmlspecialchars($pohon['nama_umum']);
include('../includes/header.php');

// Query untuk laporan (DIAMANKAN DGN PREPARED STATEMENT)
$sql_reports = "SELECT reports.*, users.nama AS nama_petugas 
                  FROM reports 
                  JOIN users ON reports.id_petugas = users.id 
                  WHERE reports.id_pohon = ? 
                  ORDER BY tanggal_lapor DESC";
$stmt_reports = mysqli_prepare($koneksi, $sql_reports);
mysqli_stmt_bind_param($stmt_reports, "i", $tree_id);
mysqli_stmt_execute($stmt_reports);
$result_reports = mysqli_stmt_get_result($stmt_reports);
?>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-xl shadow-md">
        <div class="flex items-start gap-5">
            <div class="text-6xl flex-shrink-0"><?php echo htmlspecialchars($pohon['emoji']); ?></div>
            <div class="flex-grow">
                <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($pohon['nama_umum']); ?></h1>
                <p class="text-lg text-gray-500 italic"><?php echo htmlspecialchars($pohon['nama_ilmiah']); ?></p>
                
                <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-700">
                    <div>
                        <span class="font-medium text-gray-900">ID Pohon:</span>
                        <span><?php echo htmlspecialchars($pohon['id_pohon_unik']); ?></span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-900">Tanggal Tanam:</span>
                        <span><?php echo $tgl_tanam_formatted; ?></span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-900">Perkiraan Usia:</span>
                        <span class="font-semibold text-green-700"><?php echo $usia; ?></span>
                    </div>
                </div>
                <div class="mt-4 border-t pt-4">
                    <h3 class="font-semibold text-gray-700 mb-2">Deskripsi</h3>
                    <p class="text-gray-600 prose"><?php echo nl2br(htmlspecialchars($pohon['deskripsi'])); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Riwayat Laporan Perawatan</h2>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($_GET['success']); ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($_GET['error']); ?></span>
            </div>
        <?php endif; ?>

        <div class="space-y-4">
            <?php
            if ($result_reports && mysqli_num_rows($result_reports) > 0) {
                while($laporan = mysqli_fetch_assoc($result_reports)) {
            ?>
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 mb-4">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-green-700 bg-green-100 px-3 py-1 rounded-full w-max">
                                <?php echo htmlspecialchars($laporan['jenis_tindakan']); ?>
                            </span>
                            <span class="text-xs text-gray-500 mt-1">
                                <?php echo date('d M Y, H:i', strtotime($laporan['tanggal_lapor'])); ?>
                            </span>
                        </div>

                        <?php if ($_SESSION['user_id'] == $laporan['id_petugas']): ?>
                        <div class="flex gap-2">
                            <a href="edit_report.php?id=<?php echo $laporan['id']; ?>" 
                               class="text-xs font-medium text-blue-600 hover:text-blue-800 border border-blue-200 bg-white px-3 py-1 rounded-md hover:bg-blue-50 transition-colors">
                               Edit
                            </a>
                            <a href="delete_report.php?id=<?php echo $laporan['id']; ?>" 
                               onclick="return confirm('Yakin ingin menghapus laporan ini? Foto juga akan dihapus permanen.');"
                               class="text-xs font-medium text-red-600 hover:text-red-800 border border-red-200 bg-white px-3 py-1 rounded-md hover:bg-red-50 transition-colors">
                               Hapus
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-2 pt-2 border-t border-gray-200">
                        <p class="text-gray-700 mb-3"><?php echo nl2br(htmlspecialchars($laporan['catatan'])); ?></p>
                        
                        <?php if (!empty($laporan['foto'])): ?>
                            <div class="flex items-start gap-4">
                                <a href="<?php echo 'uploads/' . $laporan['foto']; ?>" target="_blank">
                                    <img src="<?php echo 'uploads/' . $laporan['foto']; ?>" alt="Foto Laporan" class="w-24 h-24 object-cover rounded-md border border-gray-300">
                                </a>
                                
                                <?php if (!empty($laporan['status_prediksi'])): 
                                    $status = strtolower($laporan['status_prediksi']);
                                    $badge_class = 'bg-gray-200 text-gray-800';
                                    if ($status == 'sehat') $badge_class = 'bg-green-100 text-green-800';
                                    elseif ($status == 'butuh_perhatian' || $status == 'perlu_tindakan') $badge_class = 'bg-yellow-100 text-yellow-800';
                                    elseif ($status == 'kritis') $badge_class = 'bg-red-100 text-red-800';
                                ?>
                                <div>
                                    <h4 class="text-xs font-medium text-gray-500 mb-1">Analisis AI</h4>
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full <?php echo $badge_class; ?>">
                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $laporan['status_prediksi']))); ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="text-right text-xs text-gray-500 mt-3">
                        Laporan oleh: <strong><?php echo htmlspecialchars($laporan['nama_petugas']); ?></strong>
                    </div>
                </div>
            <?php
                }
            } else {
                echo "<p class='text-center text-gray-500 py-4'>Belum ada riwayat laporan untuk pohon ini.</p>";
            }
            ?>
        </div>
    </div>

    <div class="mt-6 ml-1">
        <a href="report_form.php?tree_id=<?php echo $tree_id; ?>" 
           class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-medium 
                 px-8 py-2.5 rounded-lg shadow-md hover:shadow-lg hover:translate-y[-2px] transition-all duration-300 ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" 
                 fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" 
                 class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>Buat Laporan Baru</span>
        </a>
    </div>
</div>

<?php
include('../includes/footer.php');
?>