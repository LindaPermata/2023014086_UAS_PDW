<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

// Hapus akun
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    if ($id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: kelola_akun.php");
    exit();
}

// Ambil data akun yang akan diedit
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt_edit = $conn->prepare("SELECT id, nama, email, role FROM users WHERE id = ?");
    $stmt_edit->bind_param("i", $edit_id);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();
    $edit_user = $result_edit->fetch_assoc();
    $stmt_edit->close();
}

// Proses update akun
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_akun'])) {
    $id = intval($_POST['id']);
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt_update = $conn->prepare("UPDATE users SET nama = ?, email = ?, role = ? WHERE id = ?");
    $stmt_update->bind_param("sssi", $nama, $email, $role, $id);
    $stmt_update->execute();
    $stmt_update->close();

    header("Location: kelola_akun.php");
    exit();
}

// Ambil semua akun
$result = $conn->query("SELECT id, nama, email, role, created_at FROM users ORDER BY role, created_at DESC");

$pageTitle = 'Kelola Akun Pengguna';
$activePage = 'akun';
require_once 'templates/header.php';
?>

<div class="bg-white p-8 rounded-2xl shadow-xl">
    <?php if (isset($edit_user)): ?>
        <div class="mb-8 p-6 bg-blue-50 border border-blue-200 rounded-lg shadow-sm">
            <h3 class="text-lg font-bold text-blue-800 mb-4">Edit Akun: <?php echo htmlspecialchars($edit_user['nama']); ?></h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="id" value="<?php echo $edit_user['id']; ?>">
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Nama</label>
                    <input type="text" name="nama" value="<?php echo htmlspecialchars($edit_user['nama']); ?>" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($edit_user['email']); ?>" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Role</label>
                    <select name="role" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="asisten" <?php if ($edit_user['role'] == 'asisten') echo 'selected'; ?>>Asisten</option>
                        <option value="mahasiswa" <?php if ($edit_user['role'] == 'mahasiswa') echo 'selected'; ?>>Mahasiswa</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <a href="kelola_akun.php" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400 text-sm font-medium">Batal</a>
                    <button type="submit" name="update_akun" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-medium">Simpan</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
        <h2 class="text-3xl font-bold text-blue-700">Kelola Akun Pengguna</h2>
        <a href="../register.php" target="_blank" class="flex items-center gap-2 bg-blue-600 text-white font-semibold py-2 px-5 rounded-lg hover:bg-blue-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
            Tambah Akun Baru
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="py-3 px-5 text-left font-medium uppercase text-sm">Nama</th>
                    <th class="py-3 px-5 text-left font-medium uppercase text-sm">Email</th>
                    <th class="py-3 px-5 text-left font-medium uppercase text-sm">Peran</th>
                    <th class="py-3 px-5 text-left font-medium uppercase text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                <?php if ($result->num_rows > 0):
                    while($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-blue-50 transition">
                        <td class="py-4 px-5 font-semibold text-gray-800"><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td class="py-4 px-5 text-gray-600"><?php echo htmlspecialchars($row['email']); ?></td>
                        <td class="py-4 px-5">
                            <?php if($row['role'] == 'asisten'): ?>
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-medium">Asisten</span>
                            <?php else: ?>
                                <span class="inline-block bg-sky-100 text-sky-800 text-xs px-3 py-1 rounded-full font-medium">Mahasiswa</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 px-5 flex gap-2">
                            <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                <a href="kelola_akun.php?edit=<?php echo $row['id']; ?>" class="bg-yellow-400 text-white px-3 py-1 rounded text-xs hover:bg-yellow-500">Edit</a>
                                <a href="kelola_akun.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus akun ini?')" class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600">Hapus</a>
                            <?php else: ?>
                                <span class="text-gray-400 italic text-sm">Akun Anda</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile;
                else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-6 text-gray-500">Belum ada akun terdaftar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$conn->close();
require_once 'templates/footer.php';
?>