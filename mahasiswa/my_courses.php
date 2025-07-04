<?php
$pageTitle = 'Praktikum Saya';
$activePage = 'my_courses';
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

$id_mahasiswa = $_SESSION['user_id'];

$sql = "SELECT mp.id, mp.nama_praktikum, mp.deskripsi
        FROM mata_praktikum mp
        JOIN pendaftaran_praktikum pp ON mp.id = pp.id_praktikum
        WHERE pp.id_mahasiswa = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_mahasiswa);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="bg-white p-6 rounded-xl shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Praktikum yang Anda Ikuti</h2>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
            <p><strong>Berhasil!</strong> Anda telah mendaftar ke praktikum baru.</p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if ($result->num_rows > 0):
            while($row = $result->fetch_assoc()): ?>
            <div class="border border-blue-100 rounded-2xl p-6 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow">
                <div>
                    <h3 class="text-lg font-bold text-blue-700"><?php echo htmlspecialchars($row['nama_praktikum']); ?></h3>
                    <p class="text-gray-600 mt-2 text-sm"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                </div>
                <a href="detail_praktikum.php?id=<?php echo $row['id']; ?>" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-center transition">
                    Lihat Detail & Tugas
                </a>
            </div>
        <?php endwhile;
        else: ?>
            <p class="col-span-3 text-gray-500">Anda belum mendaftar di praktikum manapun. Silakan <a href="courses.php" class="text-blue-600 hover:underline font-semibold">cari praktikum</a>.</p>
        <?php endif; ?>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
require_once 'templates/footer_mahasiswa.php';
?>
