<?php 
require_once('../includes/auth_admin.php');
$page_title = 'Pengguna';
require_once('../includes/header.php');
require_once('../config/database.php');
?>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-xl shadow-md">
        
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold text-gray-800">Daftar Pengguna</h2>
            <a href="add_user.php" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 11a1 1 0 100-2h-1v-1a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1z" /></svg>
                Tambah
            </a>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="mb-4 <?php echo $_SESSION['message_type'] == 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'; ?> border px-4 py-3 rounded-lg" role="alert">
                <span><?php echo htmlspecialchars($_SESSION['message']); ?></span>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peran</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $sql = "SELECT id, nama, username, peran FROM users ORDER BY id ASC";
                    $result = mysqli_query($koneksi, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr class='hover:bg-gray-50'>";
                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>" . $row['id'] . "</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-700'>" . htmlspecialchars($row['nama']) . "</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm'>";
                            if ($row['peran'] == 'Admin') {
                                echo "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800'>Admin</span>";
                            } else {
                                echo "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800'>Petugas</span>";
                            }
                            echo "</td>";
                            echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-center'>";
                            echo "<a href='edit_user.php?id=" . $row['id'] . "' class='text-blue-600 hover:text-blue-800 font-medium mr-3'>Edit</a>";
                            echo "<a href='delete_user.php?id=" . $row['id'] . "' class='text-red-600 hover:text-red-800 font-medium' onclick=\"return confirm('Yakin ingin hapus pengguna ini?');\">Hapus</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center text-gray-500 py-6'>Tidak ada data pengguna.</td></tr>";
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