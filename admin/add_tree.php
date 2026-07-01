<?php
require_once('../includes/auth_admin.php');
$page_title = 'Tambah Pohon';
require_once('../includes/header.php');
?>

<div class="bg-white p-6 sm:p-8 rounded-xl shadow-md">
    <h2 class="text-2xl font-semibold text-gray-800 mb-2">Formulir</h2>
    <p class="text-gray-600 mb-6">Isi data pohon baru yang ingin didaftarkan.</p>

    <form action="process_add_tree.php" method="POST" class="space-y-5">

        <div>
            <label for="id_pohon_unik" class="block text-sm font-medium text-gray-700 mb-1">ID Unik Pohon</label>
            <input 
                type="text" 
                id="id_pohon_unik" 
                name="id_pohon_unik" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                placeholder="Contoh: KRJ-005"
                required>
        </div>
        
        <div>
            <label for="nama_umum" class="block text-sm font-medium text-gray-700 mb-1">Nama Umum</label>
            <input 
                type="text" 
                id="nama_umum" 
                name="nama_umum" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                placeholder="Nama yang biasa dikenal"
                required>
        </div>

        <div>
            <label for="nama_ilmiah" class="block text-sm font-medium text-gray-700 mb-1">Nama Ilmiah (Opsional)</label>
            <input 
                type="text" 
                id="nama_ilmiah" 
                name="nama_ilmiah" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                placeholder="Nama latin atau ilmiah">
        </div>

        <div>
            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat (Opsional)</label>
            <textarea 
                id="deskripsi" 
                name="deskripsi" 
                rows="4" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                placeholder="Jelaskan ciri-ciri atau informasi penting tentang pohon ini"></textarea>
        </div>

        <div>
            <label for="tanggal_tanam" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Tanam (Opsional)</label>
            <input 
                type="date" 
                id="tanggal_tanam" 
                name="tanggal_tanam" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
        </div>
        <div>
            <label for="emoji" class="block text-sm font-medium text-gray-700 mb-1">Emoji (Opsional)</label>
            <input 
                type="text" 
                id="emoji" 
                name="emoji" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                placeholder="🌳">
        </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 mt-6">
                <a href="manage_trees.php" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                    Simpan
                </button>
        </div>
    </form>
</div>

<?php require_once('../includes/footer.php'); ?>