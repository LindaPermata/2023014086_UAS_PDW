<?php
$pageTitle = 'Cari Praktikum';
$activePage = 'courses';
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

$id_mahasiswa = $_SESSION['user_id'];
$sql = "SELECT mp.*, pp.id as id_pendaftaran FROM mata_praktikum mp
        LEFT JOIN pendaftaran_praktikum pp ON mp.id = pp.id_praktikum AND pp.id_mahasiswa = ?
        ORDER BY mp.nama_praktikum ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_mahasiswa);
$stmt->execute();
$result = $stmt->get_result();
?>

<h1 class="text-4xl font-bold text-gray-800 mb-6">Katalog Mata Praktikum</h1>
<p class="text-gray-600 mb-8">Temukan dan daftarkan diri Anda pada mata praktikum yang tersedia di bawah ini.</p>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php if ($result->num_rows > 0):
        while($row = $result->fetch_assoc()): ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform hover:-translate-y-2 transition-transform duration-300">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($row['nama_praktikum']); ?></h3>
                <p class="text-gray-600 text-sm h-16"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
            </div>
            <div class="px-6 pb-6">
                <?php if (is_null($row['id_pendaftaran'])): ?>
                    <a href="proses_pendaftaran.php?id=<?php echo $row['id']; ?>" class="block w-full text-center bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold py-2 rounded-lg hover:opacity-90 transition-opacity">
                        Daftar Praktikum
                    </a>
                <?php else: ?>
                    <span class="block w-full text-center bg-blue-100 text-blue-700 font-semibold py-2 rounded-lg cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Sudah Terdaftar
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile;
    else: ?>
        <p class="col-span-3 text-center text-gray-500 bg-white p-8 rounded-2xl shadow-md">
            Saat ini belum ada mata praktikum yang tersedia.
        </p>
    <?php endif; ?>
</div>

<?php
$stmt->close();
$conn->close();
require_once 'templates/footer_mahasiswa.php';
?>
