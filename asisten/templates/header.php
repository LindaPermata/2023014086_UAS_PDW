<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Asisten - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F8F7FF;
        }
        .sidebar {
            background: linear-gradient(180deg, #3B82F6 0%, #1E3A8A 100%);
        }
        .sidebar-item.active {
            background-color: #2563EB; /* Tailwind blue-600 */
            box-shadow: 0 4px 15px -5px rgba(59, 130, 246, 0.6); /* biru lembut */
        }
        .btn-primary {
            background: linear-gradient(135deg, #3B82F6 0%, #1E3A8A 100%);
        }
        .btn-danger {
            background: linear-gradient(135deg, #FF6B6B 0%, #FF947A 100%);
        }
    </style>
</head>
<body class="bg-[#F8F7FF]">
<div class="flex h-screen">
    <aside class="w-64 sidebar text-white flex-shrink-0 flex flex-col">
        <div class="p-6 text-center border-b border-blue-800">
            <h3 class="text-2xl font-bold">Panel Asisten</h3>
            <p class="text-sm text-gray-200 mt-1">Hi, <?php echo htmlspecialchars($_SESSION['nama']); ?></p>
        </div>
        <nav class="flex-grow p-4">
            <ul class="space-y-2">
                <?php
                    $baseClass = 'flex items-center px-4 py-3 rounded-lg transition-colors duration-200';
                    $active = 'sidebar-item active';
                    $inactive = 'hover:bg-blue-800';
                ?>
                <li>
                    <a href="dashboard.php"
                       class="<?php echo ($activePage == 'dashboard') ? $active : $inactive; ?> <?php echo $baseClass; ?>">
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="kelola_akun.php"
                       class="<?php echo ($activePage == 'akun') ? $active : $inactive; ?> <?php echo $baseClass; ?>">
                        <span>Kelola Akun</span>
                    </a>
                </li>
                <li>
                    <a href="kelola_praktikum.php"
                       class="<?php echo ($activePage == 'praktikum') ? $active : $inactive; ?> <?php echo $baseClass; ?>">
                        <span>Kelola Praktikum</span>
                    </a>
                </li>
                <li>
                    <a href="kelola_modul.php"
                       class="<?php echo ($activePage == 'modul') ? $active : $inactive; ?> <?php echo $baseClass; ?>">
                        <span>Kelola Modul</span>
                    </a>
                </li>
                <li>
                    <a href="laporan_masuk.php"
                       class="<?php echo ($activePage == 'laporan') ? $active : $inactive; ?> <?php echo $baseClass; ?>">
                        <span>Laporan Masuk</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="p-4 border-t border-blue-800">
            <a href="../logout.php" class="w-full text-center btn-danger text-white font-bold py-2 px-4 rounded-lg block">Logout</a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-[#F8F7FF] p-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-8"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
