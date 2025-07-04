<?php
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once 'templates/header_mahasiswa.php'; 
require_once '../config.php';

$id_mahasiswa = $_SESSION['user_id'];

// 1. Jumlah Praktikum Diikuti
$stmt1 = $conn->prepare("SELECT COUNT(*) FROM pendaftaran_praktikum WHERE id_mahasiswa = ?");
$stmt1->bind_param("i", $id_mahasiswa);
$stmt1->execute();
$stmt1->bind_result($jumlah_praktikum);
$stmt1->fetch();
$stmt1->close();

// 2. Jumlah Tugas Selesai
$stmt2 = $conn->prepare("SELECT COUNT(*) FROM laporan WHERE id_mahasiswa = ? AND nilai IS NOT NULL");
$stmt2->bind_param("i", $id_mahasiswa);
$stmt2->execute();
$stmt2->bind_result($tugas_selesai);
$stmt2->fetch();
$stmt2->close();

// 3. Jumlah Tugas Menunggu
$stmt3 = $conn->prepare("SELECT COUNT(*) FROM laporan WHERE id_mahasiswa = ? AND nilai IS NULL");
$stmt3->bind_param("i", $id_mahasiswa);
$stmt3->execute();
$stmt3->bind_result($tugas_menunggu);
$stmt3->fetch();
$stmt3->close();
?>

<div class="bg-gradient-to-r from-blue-500 to-blue-700 text-white p-8 rounded-xl shadow-lg mb-8">
    <h1 class="text-3xl font-bold">Selamat Datang Kembali, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h1>
    <p class="mt-2 opacity-90">Terus semangat dalam menyelesaikan semua modul praktikummu.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-blue-600"><?= $jumlah_praktikum ?></div>
        <div class="mt-2 text-lg text-gray-600">Praktikum Diikuti</div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-blue-500"><?= $tugas_selesai ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Selesai</div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-blue-400"><?= $tugas_menunggu ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Menunggu</div>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-md">
    <h3 class="text-2xl font-bold text-gray-800 mb-4">Notifikasi Terbaru</h3>
    <ul class="space-y-4">
        <li class="flex items-start p-3 border-b border-gray-100 last:border-b-0">
            <span class="text-xl mr-4">🔔</span>
            <div>Nilai untuk <a href="#" class="font-semibold text-blue-600 hover:underline">Modul 1: HTML & CSS</a> telah diberikan.</div>
        </li>
        <li class="flex items-start p-3 border-b border-gray-100 last:border-b-0">
            <span class="text-xl mr-4">⏳</span>
            <div>Batas waktu pengumpulan laporan untuk <a href="#" class="font-semibold text-blue-600 hover:underline">Modul 2: PHP Native</a> adalah besok!</div>
        </li>
        <li class="flex items-start p-3">
            <span class="text-xl mr-4">✅</span>
            <div>Anda berhasil mendaftar pada mata praktikum <a href="#" class="font-semibold text-blue-600 hover:underline">Jaringan Komputer</a>.</div>
        </li>
    </ul>
</div>

<?php
require_once 'templates/footer_mahasiswa.php';
?>
