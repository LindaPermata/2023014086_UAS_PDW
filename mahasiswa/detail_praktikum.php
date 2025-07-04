<?php
$id_praktikum = $_GET['id'] ?? 0;
require_once '../config.php';

$stmt_praktikum = $conn->prepare("SELECT nama_praktikum FROM mata_praktikum WHERE id = ?");
$stmt_praktikum->bind_param("i", $id_praktikum);
$stmt_praktikum->execute();
$praktikum = $stmt_praktikum->get_result()->fetch_assoc();
$pageTitle = 'Detail: ' . htmlspecialchars($praktikum['nama_praktikum']);
$activePage = 'my_courses';
require_once 'templates/header_mahasiswa.php';

$id_mahasiswa = $_SESSION['user_id'];
?>

<div class="bg-white p-8 rounded-2xl shadow-lg">
    <h2 class="text-3xl font-bold text-gray-800 mb-8">Daftar Modul & Tugas</h2>
    <?php if (isset($_GET['upload']) && $_GET['upload'] == 'sukses'): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
            <p class="font-bold">Laporan berhasil diunggah!</p>
        </div>
    <?php elseif (isset($_GET['upload']) && $_GET['upload'] == 'gagal'): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
            <p class="font-bold">Gagal mengunggah laporan.</p>
        </div>
    <?php endif; ?>

    <div class="space-y-6">
    <?php
    $sql = "SELECT m.id, m.nama_modul, m.deskripsi, m.file_materi, l.file_laporan, l.nilai, l.feedback
            FROM modul m
            LEFT JOIN laporan l ON m.id = l.id_modul AND l.id_mahasiswa = ?
            WHERE m.id_praktikum = ? ORDER BY m.created_at ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_mahasiswa, $id_praktikum);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0):
        while($modul = $result->fetch_assoc()):
    ?>
        <div class="border border-gray-200 rounded-xl p-6">
            <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($modul['nama_modul']); ?></h3>
            <p class="text-gray-500 mt-1 mb-4 text-sm"><?php echo htmlspecialchars($modul['deskripsi']); ?></p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <div>
                    <h4 class="font-bold mb-2 text-gray-700">Materi & Penilaian</h4>
                    
                    <?php if (!empty($modul['file_materi'])): ?>
                        <a href="../uploads/materi/<?php echo $modul['file_materi']; ?>" target="_blank" class="text-blue-600 hover:underline font-semibold">Unduh Materi</a>
                    <?php else: ?>
                        <p class="text-gray-400 text-sm italic">Tidak ada file materi yang diunggah.</p>
                    <?php endif; ?>

                    <div class="mt-4">
                        <p class="font-semibold text-gray-700">Status Nilai:</p>
                        <?php if(!is_null($modul['nilai'])): ?>
                            <p class="text-3xl font-bold text-green-600"><?php echo $modul['nilai']; ?></p>
                            <p class="mt-1 text-sm text-gray-600"><strong>Feedback:</strong> <?php echo htmlspecialchars($modul['feedback']); ?></p>
                        <?php else: ?>
                            <p class="text-gray-500">Belum dinilai</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold mb-2 text-gray-700">Pengumpulan Laporan</h4>
                    <?php if (is_null($modul['file_laporan'])): ?>
                        <form action="upload_laporan.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id_modul" value="<?php echo $modul['id']; ?>">
                            <input type="hidden" name="id_praktikum" value="<?php echo $id_praktikum; ?>">
                            <input type="file" name="file_laporan" required 
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full 
                                file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                            <button type="submit" class="mt-2 w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-blue-700 transition">
                                Kumpulkan
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="text-blue-600 font-semibold">✓ Laporan sudah dikumpulkan.</p>
                        <a href="../uploads/laporan/<?php echo $modul['file_laporan']; ?>" target="_blank" class="text-sm text-blue-600 hover:underline">Lihat file</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php
        endwhile;
    else:
        echo "<p class='text-center text-gray-500'>Belum ada modul untuk praktikum ini.</p>";
    endif;
    $stmt->close();
    ?>
    </div>
</div>

<?php
$conn->close();
require_once 'templates/footer_mahasiswa.php';
?>
