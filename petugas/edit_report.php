<?php
require_once('../config/database.php');
require_once('../includes/auth_petugas.php');

$report_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Cek apakah laporan ada dan milik user yang login
$sql = "SELECT * FROM reports WHERE id = ? AND id_petugas = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "ii", $report_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$laporan = mysqli_fetch_assoc($result);

if (!$laporan) {
    echo "<script>alert('Laporan tidak ditemukan atau Anda tidak memiliki akses.'); window.history.back();</script>";
    exit();
}

// Ambil nama pohon untuk judul
$sql_tree = "SELECT nama_umum FROM trees WHERE id = " . $laporan['id_pohon'];
$res_tree = mysqli_query($koneksi, $sql_tree);
$pohon = mysqli_fetch_assoc($res_tree);

$page_title = 'Edit Laporan';
include('../includes/header.php');
?>

<div class="bg-white p-6 rounded-xl shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Edit Laporan</h2>
        <span class="text-sm text-gray-500">Pohon: <?php echo htmlspecialchars($pohon['nama_umum']); ?></span>
    </div>

    <form action="process_edit_report.php" method="POST" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="report_id" value="<?php echo $report_id; ?>">
        <input type="hidden" name="id_pohon" value="<?php echo $laporan['id_pohon']; ?>">
        <input type="hidden" name="foto_lama" value="<?php echo $laporan['foto']; ?>">
        <input type="hidden" name="prediksi_lama" value="<?php echo $laporan['status_prediksi']; ?>">

        <div>
            <label for="jenis_tindakan" class="block text-sm font-medium text-gray-700">Jenis Tindakan</label>
            <select id="jenis_tindakan" name="jenis_tindakan" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" required>
                <?php
                $opsi = ['Pemeriksaan', 'Penyiraman', 'Pemupukan', 'Pemangkasan', 'Pengendalian Hama'];
                foreach ($opsi as $op) {
                    $selected = ($laporan['jenis_tindakan'] == $op) ? 'selected' : '';
                    echo "<option value='$op' $selected>$op</option>";
                }
                ?>
            </select>
        </div>

        <div>
            <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan Observasi</label>
            <textarea id="catatan" name="catatan" rows="5" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"><?php echo htmlspecialchars($laporan['catatan']); ?></textarea>
        </div>
        
        <div class="border-t pt-4 mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Saat Ini</label>
            <?php if ($laporan['foto']): ?>
                <img src="uploads/<?php echo $laporan['foto']; ?>" class="h-32 w-auto rounded-lg border mb-4 object-cover">
            <?php else: ?>
                <p class="text-sm text-gray-400 italic mb-4">Tidak ada foto.</p>
            <?php endif; ?>

            <label class="block text-sm font-medium text-gray-700">Ganti Foto (Opsional)</label>
            <p class="text-xs text-red-500 mb-2">*Jika foto diganti, AI akan memproses ulang prediksi kesehatan.</p>
            
            <input type="file" id="foto_input_hidden" name="foto" accept="image/*" class="hidden">

            <div class="mt-2 flex gap-4">
                <button type="button" id="btn_buka_kamera" class="inline-flex items-center justify-center flex-1 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2H4zm10 3a3 3 0 10-6 0 3 3 0 006 0z" clip-rule="evenodd" />
                    </svg>
                    Buka Kamera
                </button>
                
                <button type="button" id="btn_pilih_galeri" class="inline-flex items-center justify-center flex-1 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                    </svg>
                    Pilih Galeri
                </button>
            </div>

            <p id="file_name_display" class="mt-2 text-xs text-gray-500">Belum ada file baru dipilih.</p>
            </div>

        <div class="flex items-center justify-end gap-4 pt-4">
            <a href="tree_detail.php?id=<?php echo $laporan['id_pohon']; ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">Batal</a>
            <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
const hiddenInput = document.getElementById('foto_input_hidden');
const btnKamera = document.getElementById('btn_buka_kamera');
const btnGaleri = document.getElementById('btn_pilih_galeri');
const fileNameDisplay = document.getElementById('file_name_display');

// 1. Saat tombol "Buka Kamera" diklik
btnKamera.addEventListener('click', () => {
    hiddenInput.setAttribute('capture', 'environment'); 
    hiddenInput.click(); 
});

// 2. Saat tombol "Pilih Galeri" diklik
btnGaleri.addEventListener('click', () => {
    hiddenInput.removeAttribute('capture');
    hiddenInput.click(); 
});

// 3. Tampilkan nama file
hiddenInput.addEventListener('change', (e) => {
    if (hiddenInput.files.length > 0) {
        fileNameDisplay.textContent = 'File baru dipilih: ' + hiddenInput.files[0].name;
        fileNameDisplay.className = 'mt-2 text-xs text-green-600 font-semibold';
    } else {
        fileNameDisplay.textContent = 'Belum ada file baru dipilih.';
        fileNameDisplay.className = 'mt-2 text-xs text-gray-500';
    }
});
</script>

<?php include('../includes/footer.php'); ?>