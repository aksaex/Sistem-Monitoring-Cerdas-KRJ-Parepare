<?php
require_once('../includes/auth_admin.php');
$page_title = 'Edit Pohon';
require_once('../includes/header.php');
require_once('../config/database.php');

// 1. Ambil ID pohon dari URL
$tree_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($tree_id <= 0) {
    echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4' role='alert'><p>Error: ID pohon tidak valid.</p></div>";
    require_once('../includes/footer.php');
    exit();
}

// 2. Ambil data pohon saat ini dari database
$sql = "SELECT * FROM trees WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $tree_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pohon = mysqli_fetch_assoc($result);

// Jika pohon tidak ditemukan
if (!$pohon) {
    echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4' role='alert'><p>Error: Data pohon tidak ditemukan.</p></div>";
    require_once('../includes/footer.php');
    exit();
}
?>

<div class="bg-white p-6 sm:p-8 rounded-xl shadow-md">
    <h2 class="text-2xl font-semibold text-gray-800 mb-2">Formulir Edit</h2>
    <p class="text-gray-600 mb-6">Anda mengedit data: <strong class="font-medium"><?php echo htmlspecialchars($pohon['nama_umum']); ?></strong></p>

    <form action="process_edit_tree.php" method="POST" class="space-y-5">
        <input type="hidden" name="id" value="<?php echo $pohon['id']; ?>">

        <div>
            <label for="id_pohon_unik" class="block text-sm font-medium text-gray-700 mb-1">ID Unik Pohon</label>
            <input type="text" id="id_pohon_unik" name="id_pohon_unik" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" value="<?php echo htmlspecialchars($pohon['id_pohon_unik']); ?>" required>
        </div>

        <div>
            <label for="nama_umum" class="block text-sm font-medium text-gray-700 mb-1">Nama Umum</label>
            <input type="text" id="nama_umum" name="nama_umum" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" value="<?php echo htmlspecialchars($pohon['nama_umum']); ?>" required>
        </div>

        <div>
            <label for="nama_ilmiah" class="block text-sm font-medium text-gray-700 mb-1">Nama Ilmiah (Opsional)</label>
            <input type="text" id="nama_ilmiah" name="nama_ilmiah" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" value="<?php echo htmlspecialchars($pohon['nama_ilmiah']); ?>">
        </div>

        <div>
            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat (Opsional)</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"><?php echo htmlspecialchars($pohon['deskripsi']); ?></textarea>
        </div>

        <div>
            <label for="tanggal_tanam" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Tanam (Opsional)</Jlabel>
            <input 
                type="date" 
                id="tanggal_tanam" 
                name="tanggal_tanam" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                value="<?php echo htmlspecialchars($pohon['tanggal_tanam']); ?>">
                </div>
        <div>
            <label for="emoji" class="block text-sm font-medium text-gray-700 mb-1">Emoji (Opsional)</label>
            <input type="text" id="emoji" name="emoji" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" value="<?php echo htmlspecialchars($pohon['emoji']); ?>">
        </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 mt-6">
                <a href="manage_trees.php" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                    Simpan Perubahan
                </button>
        </div>
    </form>
</div>

<?php require_once('../includes/footer.php'); ?>