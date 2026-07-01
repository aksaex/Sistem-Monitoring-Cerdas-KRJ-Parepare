<?php
require_once('../includes/auth_admin.php');
$page_title = 'Edit Pengguna';
require_once('../includes/header.php');
require_once('../config/database.php');

// 1. Ambil ID pengguna dari URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($user_id <= 0) {
    echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4' role='alert'><p>Error: ID pengguna tidak valid.</p></div>";
    require_once('../includes/footer.php');
    exit();
}

// 2. Ambil data pengguna saat ini dari database
$sql = "SELECT nama, username, peran FROM users WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Jika pengguna tidak ditemukan
if (!$user) {
    echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4' role='alert'><p>Error: Pengguna tidak ditemukan.</p></div>";
    require_once('../includes/footer.php');
    exit();
}
?>

<div class="bg-white p-6 sm:p-8 rounded-xl shadow-md">
    <h2 class="text-2xl font-semibold text-gray-800 mb-2">Formulir Edit</h2>
    <p class="text-gray-600 mb-6">Anda mengedit data : <strong class="font-medium"><?php echo htmlspecialchars($user['nama']); ?></strong></p>

    <form action="process_edit_user.php" method="POST" class="space-y-5">
        <input type="hidden" name="id" value="<?php echo $user_id; ?>">

        <div>
            <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input 
                type="text" 
                id="nama" 
                name="nama" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                value="<?php echo htmlspecialchars($user['nama']); ?>" 
                required>
        </div>

        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input 
                type="text" 
                id="username" 
                name="username" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                value="<?php echo htmlspecialchars($user['username']); ?>" 
                required>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru (Opsional)</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="Isi jika ingin mengubah password">
            <p class="text-xs text-gray-500 mt-1">Kosongkan jika Anda tidak ingin mengubah password.</p>
        </div>

        <div>
            <label for="peran" class="block text-sm font-medium text-gray-700 mb-1">Peran (Role)</label>
            <select 
                id="peran" 
                name="peran" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                required>
                <option value="Petugas" <?php if($user['peran'] == 'Petugas') echo 'selected'; ?>>Petugas</option>
                <option value="Admin" <?php if($user['peran'] == 'Admin') echo 'selected'; ?>>Admin</option>
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

<?php require_once('../includes/footer.php'); ?>