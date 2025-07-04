<?php
$pageTitle = 'Kelola Modul Praktikum';
$activePage = 'modul';
require_once 'templates/header.php';
require_once '../config.php';

$upload_message = '';
$upload_message_type = '';
$edit_mode = false;

// === TAMBAH / UPDATE MODUL ===
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_modul'])) {
    $id_modul = $_POST['id_modul'];
    $id_praktikum = $_POST['id_praktikum'];
    $nama_modul = $_POST['nama_modul'];
    $deskripsi = $_POST['deskripsi'];
    $file_materi_path = '';

    $target_dir = "../uploads/materi/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0775, true);

    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == 0) {
        $file_materi_path = time() . '_' . basename($_FILES["file_materi"]["name"]);
        $file_path = $target_dir . $file_materi_path;

        if (!move_uploaded_file($_FILES["file_materi"]["tmp_name"], $file_path)) {
            $upload_message = "Gagal upload file.";
            $upload_message_type = 'error';
        }
    }

    if ($id_modul == '') {
        $stmt = $conn->prepare("INSERT INTO modul (id_praktikum, nama_modul, deskripsi, file_materi) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id_praktikum, $nama_modul, $deskripsi, $file_materi_path);
    } else {
        if ($file_materi_path != '') {
            $stmt = $conn->prepare("UPDATE modul SET id_praktikum=?, nama_modul=?, deskripsi=?, file_materi=? WHERE id=?");
            $stmt->bind_param("isssi", $id_praktikum, $nama_modul, $deskripsi, $file_materi_path, $id_modul);
        } else {
            $stmt = $conn->prepare("UPDATE modul SET id_praktikum=?, nama_modul=?, deskripsi=? WHERE id=?");
            $stmt->bind_param("issi", $id_praktikum, $nama_modul, $deskripsi, $id_modul);
        }
    }

    if ($stmt->execute()) {
        $upload_message = "Modul berhasil disimpan.";
        $upload_message_type = 'success';
    } else {
        $upload_message = "Gagal menyimpan modul.";
        $upload_message_type = 'error';
    }

    $stmt->close();
}

// === HAPUS MODUL ===
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $stmt = $conn->prepare("DELETE FROM modul WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: kelola_modul.php");
    exit();
}

// === FORM EDIT ===
$modul_edit = ['id' => '', 'id_praktikum' => '', 'nama_modul' => '', 'deskripsi' => '', 'file_materi' => ''];
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM modul WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $modul_edit = $result->fetch_assoc();
    $stmt->close();
}
?>

<!-- Form Tambah/Edit Modul -->
<div class="bg-white p-8 rounded-2xl shadow-lg mb-10">
    <h2 class="text-2xl font-bold mb-6 text-blue-800"><?php echo $edit_mode ? 'Edit' : 'Tambah'; ?> Modul Praktikum</h2>

    <?php if (!empty($upload_message)): ?>
        <div class="p-4 mb-6 rounded-lg <?php echo ($upload_message_type == 'success') ? 'bg-green-100 border-l-4 border-green-500 text-green-700' : 'bg-red-100 border-l-4 border-red-500 text-red-700'; ?>">
            <?php echo $upload_message; ?>
        </div>
    <?php endif; ?>

    <form action="kelola_modul.php" method="POST" enctype="multipart/form-data" class="space-y-5">
        <input type="hidden" name="id_modul" value="<?php echo $modul_edit['id']; ?>">

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Mata Praktikum</label>
            <select name="id_praktikum" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:outline-none">
                <option value="">-- Pilih Praktikum --</option>
                <?php
                $praktikum_list = $conn->query("SELECT id, nama_praktikum FROM mata_praktikum ORDER BY nama_praktikum");
                while ($p = $praktikum_list->fetch_assoc()) {
                    $selected = $modul_edit['id_praktikum'] == $p['id'] ? 'selected' : '';
                    echo "<option value='{$p['id']}' $selected>" . htmlspecialchars($p['nama_praktikum']) . "</option>";
                }
                ?>
            </select>
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Nama Modul</label>
            <input type="text" name="nama_modul" required value="<?php echo htmlspecialchars($modul_edit['nama_modul']); ?>"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Upload File Materi</label>
            <?php if (!empty($modul_edit['file_materi'])): ?>
                <p class="text-sm text-gray-600 mb-1">File saat ini: <a class="text-blue-600 underline" href="../uploads/materi/<?php echo $modul_edit['file_materi']; ?>" target="_blank"><?php echo $modul_edit['file_materi']; ?></a></p>
            <?php endif; ?>
            <input type="file" name="file_materi"
                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                          file:rounded-full file:border-0 file:text-sm file:font-semibold
                          file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Deskripsi Singkat</label>
            <textarea name="deskripsi" rows="4"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:outline-none"><?php echo htmlspecialchars($modul_edit['deskripsi']); ?></textarea>
        </div>

        <div>
            <button type="submit" name="submit_modul"
                    class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 transition">
                <?php echo $edit_mode ? 'Simpan Perubahan' : '+ Tambahkan Modul'; ?>
            </button>
        </div>
    </form>
</div>

<!-- Daftar Modul -->
<div class="bg-white p-8 rounded-2xl shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-blue-800">Daftar Modul Praktikum</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full text-gray-700 border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-5 text-left text-sm font-semibold uppercase">Nama Modul</th>
                    <th class="py-3 px-5 text-left text-sm font-semibold uppercase">Praktikum</th>
                    <th class="py-3 px-5 text-left text-sm font-semibold uppercase">File</th>
                    <th class="py-3 px-5 text-left text-sm font-semibold uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT m.id, m.nama_modul, m.deskripsi, m.file_materi, p.nama_praktikum 
                                        FROM modul m 
                                        JOIN mata_praktikum p ON m.id_praktikum = p.id 
                                        ORDER BY m.created_at DESC");
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                    <tr class="border-t hover:bg-blue-50 transition">
                        <td class="py-4 px-5 font-medium"><?php echo htmlspecialchars($row['nama_modul']); ?></td>
                        <td class="py-4 px-5"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                        <td class="py-4 px-5">
                            <?php if (!empty($row['file_materi'])): ?>
                                <a href="../uploads/materi/<?php echo $row['file_materi']; ?>" class="text-blue-600 underline text-sm" target="_blank">Lihat File</a>
                            <?php else: ?>
                                <span class="text-gray-400 italic text-sm">Tidak ada</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 px-5 space-x-2">
                            <a href="kelola_modul.php?edit=<?php echo $row['id']; ?>" class="bg-yellow-400 text-white py-1 px-3 rounded text-sm hover:bg-yellow-500">Edit</a>
                            <a href="kelola_modul.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Hapus modul ini?')" class="bg-red-500 text-white py-1 px-3 rounded text-sm hover:bg-red-600">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="4" class="text-center py-6 text-gray-500">Belum ada modul.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>
