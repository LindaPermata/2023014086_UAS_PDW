<?php
$pageTitle = 'Kelola Mata Praktikum';
$activePage = 'praktikum';
require_once 'templates/header.php';
require_once '../config.php';

// Simpan / Update Praktikum
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_praktikum'])) {
    $nama = $_POST['nama_praktikum'];
    $deskripsi = $_POST['deskripsi'];
    $id = $_POST['id_praktikum'];

    if (empty($id)) {
        $stmt = $conn->prepare("INSERT INTO mata_praktikum (nama_praktikum, deskripsi) VALUES (?, ?)");
        $stmt->bind_param("ss", $nama, $deskripsi);
    } else {
        $stmt = $conn->prepare("UPDATE mata_praktikum SET nama_praktikum = ?, deskripsi = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nama, $deskripsi, $id);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: kelola_praktikum.php");
    exit();
}

// Hapus Praktikum
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM mata_praktikum WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: kelola_praktikum.php");
    exit();
}

// Ambil Data Praktikum untuk Edit
$praktikum_edit = ['id' => '', 'nama_praktikum' => '', 'deskripsi' => ''];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM mata_praktikum WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $praktikum_edit = $result->fetch_assoc();
    }
    $stmt->close();
}
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form Praktikum -->
    <div class="bg-white p-8 rounded-2xl shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-blue-700"><?php echo empty($praktikum_edit['id']) ? 'Tambah' : 'Edit'; ?> Praktikum</h2>
        <form action="kelola_praktikum.php" method="POST" class="space-y-4">
            <input type="hidden" name="id_praktikum" value="<?php echo $praktikum_edit['id']; ?>">
            <div>
                <label for="nama_praktikum" class="block font-semibold text-gray-700 mb-1">Nama Praktikum</label>
                <input type="text" name="nama_praktikum" id="nama_praktikum" value="<?php echo htmlspecialchars($praktikum_edit['nama_praktikum']); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <label for="deskripsi" class="block font-semibold text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required><?php echo htmlspecialchars($praktikum_edit['deskripsi']); ?></textarea>
            </div>
            <button type="submit" name="submit_praktikum" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 transition">
                Simpan Praktikum
            </button>
        </form>
    </div>

    <!-- Tabel Praktikum -->
    <div class="lg:col-span-2 bg-white p-8 rounded-2xl shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-blue-700">Daftar Mata Praktikum</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-b-2 border-blue-200 bg-blue-50">
                    <tr>
                        <th class="py-3 px-4 text-left uppercase font-semibold text-sm text-blue-700">Nama Praktikum</th>
                        <th class="py-3 px-4 text-left uppercase font-semibold text-sm text-blue-700">Deskripsi</th>
                        <th class="py-3 px-4 text-left uppercase font-semibold text-sm text-blue-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800">
                    <?php
                    $result = $conn->query("SELECT * FROM mata_praktikum ORDER BY created_at DESC");
                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()): ?>
                            <tr class="border-b border-gray-100 hover:bg-blue-50 transition">
                                <td class="py-4 px-4 font-semibold"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                                <td class="py-4 px-4"><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                                <td class="py-4 px-4 flex items-center space-x-2">
                                    <a href="kelola_praktikum.php?edit=<?php echo $row['id']; ?>" class="bg-yellow-400 text-white py-1 px-3 rounded-md text-xs font-semibold hover:bg-yellow-500">Edit</a>
                                    <a href="kelola_praktikum.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="bg-red-500 text-white py-1 px-3 rounded-md text-xs font-semibold hover:bg-red-600">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr><td colspan="3" class="text-center py-6 text-gray-500">Belum ada data praktikum.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>
