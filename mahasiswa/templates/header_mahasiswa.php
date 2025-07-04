<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Mahasiswa - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F8F7FF;
        }
        .active-nav {
            background-color: #3B82F6;
            color: white;
        }
        .btn-primary {
            background: linear-gradient(135deg, #3B82F6 0%, #06B6D4 100%);
        }
        .btn-danger {
            background: linear-gradient(135deg, #EF4444 0%, #F87171 100%);
        }
    </style>
</head>
<body class="bg-[#F8F7FF]">
    <header class="bg-white shadow-md sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex-shrink-0">
                    <a href="dashboard.php" class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#3B82F6] to-[#06B6D4]">
                        SIMPRAK
                    </a>
                </div>

                <nav class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <?php
                            $baseClass = 'px-4 py-2 rounded-lg text-sm font-semibold transition-colors duration-300';
                            $activeClass = 'bg-gradient-to-r from-[#3B82F6] to-[#06B6D4] text-white shadow-md';
                            $inactiveClass = 'text-gray-500 hover:bg-blue-100 hover:text-blue-700';
                        ?>
                        <a href="dashboard.php" class="<?php echo ($activePage == 'dashboard') ? $activeClass : $inactiveClass; ?> <?php echo $baseClass; ?>">Dashboard</a>
                        <a href="my_courses.php" class="<?php echo ($activePage == 'my_courses') ? $activeClass : $inactiveClass; ?> <?php echo $baseClass; ?>">Praktikum Saya</a>
                        <a href="courses.php" class="<?php echo ($activePage == 'courses') ? $activeClass : $inactiveClass; ?> <?php echo $baseClass; ?>">Cari Praktikum</a>
                    </div>
                </nav>

                <div class="hidden md:block">
                    <div class="ml-4 flex items-center space-x-4">
                        <span class="font-semibold text-gray-700">Hi, <?php echo strtok(htmlspecialchars($_SESSION['nama']), " "); ?>!</span>
                        <a href="../logout.php" class="btn-danger text-white font-bold py-2 px-4 rounded-lg shadow-sm hover:opacity-90 transition-opacity">
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
