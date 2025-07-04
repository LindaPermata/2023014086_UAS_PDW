<?php
$pageTitle = 'Dashboard Asisten';
$activePage = 'dashboard';
require_once 'templates/header.php';
require_once '../config.php';

// Data real
$totalModul = $conn->query("SELECT COUNT(*) as total FROM modul")->fetch_assoc()['total'] ?? 0;
$totalLaporan = $conn->query("SELECT COUNT(*) as total FROM laporan")->fetch_assoc()['total'] ?? 0;
$belumDinilai = $conn->query("SELECT COUNT(*) as total FROM laporan WHERE nilai IS NULL")->fetch_assoc()['total'] ?? 0;

// Aktivitas
$aktivitas = $conn->query("
    SELECT l.id, l.tanggal_kumpul, u.nama AS nama_mahasiswa, m.nama_modul 
    FROM laporan l 
    JOIN users u ON l.id_mahasiswa = u.id 
    JOIN modul m ON l.id_modul = m.id 
    ORDER BY l.tanggal_kumpul DESC 
    LIMIT 5
");
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <!-- Kartu 1: Total Modul -->
    <!-- Kartu 1: Total Modul -->
    <a href="kelola_modul.php" class="bg-blue-50 p-8 rounded-2xl shadow-md flex items-center space-x-6 transform hover:scale-105 transition duration-300 hover:bg-blue-100">
    <div class="bg-blue-100 p-4 rounded-full">
        <img src="https://img.icons8.com/fluency/48/books.png" alt="icon"/>
    </div>
    <div>
        <p class="text-base text-blue-700 font-medium">Total Modul</p>
        <p class="text-3xl font-bold text-blue-900"><?= $totalModul ?></p>
    </div>
    </a>


    <!-- Kartu 2: Laporan Masuk -->
    <a href="laporan_masuk.php" class="bg-blue-50 p-8 rounded-2xl shadow-md flex items-center space-x-6 transform hover:scale-105 transition duration-300 hover:bg-blue-100">
        <div class="bg-blue-100 p-4 rounded-full">
            <img src="https://img.icons8.com/fluency/48/inbox.png" alt="icon"/>
        </div>
        <div>
            <p class="text-base text-blue-700 font-medium">Laporan Masuk</p>
            <p class="text-3xl font-bold text-blue-900"><?= $totalLaporan ?></p>
        </div>
    </a>

    <!-- Kartu 3: Belum Dinilai -->
    <a href="laporan_masuk.php?filter_status=belum_dinilai" class="bg-blue-50 p-8 rounded-2xl shadow-md flex items-center space-x-6 transform hover:scale-105 transition duration-300 hover:bg-blue-100">
        <div class="bg-blue-100 p-4 rounded-full">
            <img src="https://img.icons8.com/fluency/48/task.png" alt="icon"/>
        </div>
        <div>
            <p class="text-base text-blue-700 font-medium">Belum Dinilai</p>
            <p class="text-3xl font-bold text-blue-900"><?= $belumDinilai ?></p>
        </div>
    </a>
</div>

<!-- Aktivitas Terbaru -->
<div class="bg-white p-8 rounded-2xl shadow-md mt-10">
    <h3 class="text-2xl font-bold text-gray-800 mb-6">Aktivitas Laporan Terbaru</h3>
    <div class="space-y-5">
        <?php if ($aktivitas->num_rows > 0): ?>
            <?php while ($row = $aktivitas->fetch_assoc()):
                $idLaporan = $row['id'];
                $nama = htmlspecialchars($row['nama_mahasiswa']);
                $inisial = strtoupper(substr($nama, 0, 1)) . strtoupper(substr(explode(" ", $nama)[1] ?? '', 0, 1));
                $modul = htmlspecialchars($row['nama_modul']);
                $waktu = date('d M Y, H:i', strtotime($row['tanggal_kumpul']));
            ?>
            <a href="proses_nilai.php?id=<?= $idLaporan ?>" class="block hover:bg-blue-50 rounded-lg px-4 py-3 transition">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center mr-4 text-white font-bold text-lg">
                        <?= $inisial ?>
                    </div>
                    <div>
                        <p class="text-gray-800"><strong><?= $nama ?></strong> baru saja mengumpulkan laporan untuk <strong><?= $modul ?></strong></p>
                        <p class="text-sm text-gray-500"><?= $waktu ?></p>
                    </div>
                </div>
            </a>
            <div class="border-t border-gray-100"></div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-500 text-center">Belum ada laporan yang dikumpulkan.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
