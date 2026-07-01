<?php
require_once('../includes/auth_admin.php');
$page_title = 'Tambah Pengguna';
require_once('../includes/header.php');
?>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Formulir</h2>
        <p class="text-gray-600 mb-6">Isi Data pengguna baru di bawah.</p>

        <form action="process_add_user.php" method="POST" class="space-y-5">
            
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input 
                    type="text" 
                    id="nama" 
                    name="nama" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                    placeholder="Contoh: Muh. Aksa"
                    required>
            </div>
            
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                    placeholder="Username untuk login"
                    required>
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                    placeholder="Minimal 4 karakter"
                    required>
            </div>
            
            <div>
                <label for="peran" class="block text-sm font-medium text-gray-700 mb-1">Peran (Role)</label>
                <select 
                    id="peran" 
                    name="peran" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" 
                    required>
                    <option value="Petugas">Petugas</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 mt-6">
                <a href="manage_users.php" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">
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
</div>

<?php require_once('../includes/footer.php'); ?>