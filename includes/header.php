<?php
// Selalu mulai session di awal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Definisikan base URL agar path selalu benar
$base_url = "/monitoring-krj"; // Sesuaikan jika nama folder Anda berbeda

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: " . $base_url . "/index.php");
    exit();
}

$user_role = $_SESSION['user_peran'];
$user_name = $_SESSION['user_nama'];
$user_avatar = strtoupper(substr($user_name, 0, 1));
$is_admin = ($user_role == 'Admin');
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Monitoring Cerdas KRJ</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }
        .sidebar.open {
            transform: translateX(0) !important;
        }
        .active-menu {
            background-color: #C6F6D5;
            color: #22543D;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-gray-100 antialiased">

    <div>
        <!-- ======== HEADER NAVBAR ======== -->
        <header class="bg-white shadow-sm p-3 flex justify-between items-center fixed top-0 left-0 right-0 z-30 md:ml-64">
            <div class="flex items-center gap-4">
                <!-- Tombol toggle sidebar (mobile) -->
                <button id="sidebarToggle" class="text-gray-600 text-2xl md:hidden">&#9776;</button>
                <h1 class="text-xl font-bold text-gray-800"><?php echo $page_title; ?></h1>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden sm:inline text-sm font-medium text-gray-700"><?php echo htmlspecialchars($user_name); ?></span>
                <div class="w-9 h-9 <?php echo $is_admin ? 'bg-red-600' : 'bg-green-700'; ?> text-white rounded-full flex items-center justify-center font-bold text-sm">
                    <?php echo htmlspecialchars($user_avatar); ?>
                </div>
            </div>
        </header>

        <!-- ======== SIDEBAR ======== -->
        <aside id="sidebar" class="sidebar bg-white w-64 fixed top-0 left-0 h-full shadow-lg transform -translate-x-full md:translate-x-0 z-40">
            <div class="p-4 text-center border-b">
                <img src="<?php echo $base_url; ?>/assets/images/krj.png" alt="Logo KRJ" class="mx-auto h-14 w-auto mb-2">
                <h2 class="font-bold text-green-800"><?php echo htmlspecialchars($user_role); ?> Panel</h2>
            </div>
            
            <nav class="mt-4 flex flex-col justify-between h-[calc(100%-120px)]">
                <div>
                    <?php
                    // Sidebar dinamis tergantung role
                    if ($is_admin) {
                        include('sidebar_admin.php');
                    } else {
                        include('sidebar_petugas.php');
                    }
                    ?>
                </div>

                <!-- Tombol Logout -->
                <a href="<?php echo $base_url; ?>/logout.php" class="flex items-center p-3 mx-2 mb-4 rounded-lg text-red-600 hover:bg-red-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="ml-3">Logout</span>
                </a>
            </nav>
        </aside>

        <!-- ======== MAIN CONTENT ======== -->
        <!-- padding atas diperbesar agar tidak ketutupan header -->
        <main id="mainContent" class="md:ml-64 pt-28 sm:pt-32 p-4 sm:p-6 transition-all duration-300">
