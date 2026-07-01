<?php
session_start();
// Sesuaikan jika nama folder Anda berbeda
$base_url = "/monitoring-krj"; 

// Jika pengguna sudah login, langsung arahkan ke dasbor yang sesuai
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_peran'] == 'Admin') {
        header("Location: " . $base_url . "/admin/index.php");
    } else {
        header("Location: " . $base_url . "/petugas/index.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Monitoring Cerdas KRJ</title>
    
<link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-200">

    <div class="flex items-center justify-center min-h-screen p-4">
        
        <div class="bg-white p-8 sm:p-10 rounded-2xl shadow-xl w-full max-w-sm text-center border border-gray-200">
            
            <img src="<?php echo htmlspecialchars($base_url); ?>/assets/images/krj.png" alt="Logo KRJ" class="mx-auto h-20 w-auto mb-4">
            
            <h1 class="text-2xl font-bold text-green-800 mb-2">MONITORING CERDAS</h1>
            <p class="text-gray-600 mb-8 text-sm">Silakan login untuk melanjutkan</p>

            <form action="login_process.php" method="POST">
                <div class="mb-5 text-left">
                    <label for="username" class="block mb-2 text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" placeholder="admin / petugas" required>
                </div>
                
                <div class="mb-6 text-left">
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                </div>
                
                <?php 
                // Menampilkan pesan error jika ada (dengan gaya baru)
                if (isset($_GET['error'])) {
                    echo '<p class="text-red-500 text-sm mb-4">' . htmlspecialchars($_GET['error']) . '</p>';
                }
                ?>

                <button type="submit" class="w-full text-white bg-green-700 hover:bg-green-800 font-medium rounded-lg text-sm px-5 py-3 text-center transition-all duration-300">
                    Login
                </button>
            </form>

        </div>
    </div>

</body>
</html>