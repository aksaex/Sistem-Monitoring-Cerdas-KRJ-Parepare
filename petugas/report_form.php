<?php
// Panggil file koneksi database
require_once('../config/database.php');
require_once('../includes/auth_petugas.php'); // Pastikan auth dipanggil

// Ambil ID pohon dari URL
$tree_id = isset($_GET['tree_id']) ? intval($_GET['tree_id']) : 0;

if ($tree_id <= 0) {
    header("Location: index.php");
    exit();
}

// Ambil nama pohon untuk ditampilkan di judul
$sql_tree = "SELECT nama_umum FROM trees WHERE id = $tree_id";
$result_tree = mysqli_query($koneksi, $sql_tree);
$pohon = mysqli_fetch_assoc($result_tree);
$nama_pohon = $pohon ? $pohon['nama_umum'] : 'Tidak Dikenal';

// Set judul halaman
$page_title = 'Catatan: ' . htmlspecialchars($nama_pohon);
include('../includes/header.php');
?>

<div class="bg-white p-6 rounded-xl shadow-md">
    <h2 class="text-2xl font-semibold text-gray-800 mb-2">Form Pencatatan Tindakan</h2>
    <p class="mb-6 text-gray-600">Anda membuat laporan untuk pohon: <strong class="font-medium"><?php echo htmlspecialchars($nama_pohon); ?></strong></p>

    <?php if(isset($_GET['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($_GET['error']); ?></span>
        </div>
    <?php endif; ?>

    <form action="save_report.php" method="POST" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="id_pohon" value="<?php echo $tree_id; ?>">

        <div>
            <label for="jenis_tindakan" class="block text-sm font-medium text-gray-700">Jenis Tindakan</label>
            <select id="jenis_tindakan" name="jenis_tindakan" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" required>
                <option value="">-- Pilih Jenis Tindakan --</option>
                <!-- PERBAIKAN: value disesuaikan dengan teks -->
                <option value="Pemeriksaan">Pemeriksaan</option>
                <option value="Penyiraman">Penyiraman</option>
                <option value="Pemupukan">Pemupukan</option>
                <option value="Pemangkasan">Pemangkasan</option>
                <option value="Pengendalian Hama">Pengendalian Hama</option>
            </select>
        </div>

        <div>
            <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan Observasi</label>
            <textarea id="catatan" name="catatan" rows="5" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="Tuliskan hasil observasi Anda di sini..."></textarea>
        </div>
        
        <!-- ====================================================== -->
        <!-- START: BLOK INPUT FILE YANG DIMODIFIKASI (BARU)       -->
        <!-- ====================================================== -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Unggah Foto (prediksi kecerdasan buatan)</label>
            
            <!-- 1. Input file asli. Dibuat tersembunyi (hidden). -->
            <!-- PENTING: name="foto" tetap ada di sini. -->
            <input type="file" id="foto_input_hidden" name="foto" accept="image/*" class="hidden">

            <!-- 2. Buat dua tombol "palsu" yang akan dilihat pengguna. -->
            <div class="mt-2 flex gap-4">
                <!-- Tombol Buka Kamera -->
                <button type="button" id="btn_buka_kamera" class="inline-flex items-center justify-center flex-1 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <!-- Ikon Kamera -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2H4zm10 3a3 3 0 10-6 0 3 3 0 006 0z" clip-rule="evenodd" />
                    </svg>
                    Buka Kamera
                </button>
                
                <!-- Tombol Pilih Galeri -->
                <button type="button" id="btn_pilih_galeri" class="inline-flex items-center justify-center flex-1 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <!-- Ikon Galeri -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                    </svg>
                    Pilih Galeri
                </button>
            </div>

            <!-- 3. (Opsional) Tempat untuk menampilkan nama file yang dipilih. -->
            <p id="file_name_display" class="mt-2 text-xs text-gray-500">Belum ada file dipilih. Maksimal 2MB.</p>
        </div>
        <!-- ====================================================== -->
        <!-- END: BLOK INPUT FILE YANG DIMODIFIKASI                 -->
        <!-- ====================================================== -->

        <div class="flex items-center justify-end gap-4 pt-4">
            <a href="tree_detail.php?id=<?php echo $tree_id; ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">Batal</a>
            <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Simpan
            </button>
        </div>
    </form>
</div>

<!-- ====================================================== -->
<!-- START: JAVASCRIPT UNTUK MENGONTROL TOMBOL FOTO         -->
<!-- ====================================================== -->
<script>
// Ambil semua elemen yang kita butuhkan
const hiddenInput = document.getElementById('foto_input_hidden');
const btnKamera = document.getElementById('btn_buka_kamera');
const btnGaleri = document.getElementById('btn_pilih_galeri');
const fileNameDisplay = document.getElementById('file_name_display');

// 1. Saat tombol "Buka Kamera" diklik
btnKamera.addEventListener('click', () => {
    // Tambahkan atribut 'capture' untuk membuka kamera
    hiddenInput.setAttribute('capture', 'environment'); 
    hiddenInput.click(); // Klik input file yang tersembunyi
});

// 2. Saat tombol "Pilih Galeri" diklik
btnGaleri.addEventListener('click', () => {
    // Hapus atribut 'capture' agar membuka galeri file
    hiddenInput.removeAttribute('capture');
    hiddenInput.click(); // Klik input file yang tersembunyi
});

// 3. Tampilkan nama file saat pengguna selesai memilih
hiddenInput.addEventListener('change', (e) => {
    if (hiddenInput.files.length > 0) {
        fileNameDisplay.textContent = 'File dipilih: ' + hiddenInput.files[0].name;
    } else {
        fileNameDisplay.textContent = 'Belum ada file dipilih. Maksimal 2MB.';
    }
});
</script>
<!-- ====================================================== -->
<!-- END: JAVASCRIPT                                        -->
<!-- ====================================================== -->

<?php
// Panggil footer
include('../includes/footer.php');
?>