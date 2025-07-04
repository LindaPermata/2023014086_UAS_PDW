<?php
require_once 'config.php';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (empty($nama) || empty($email) || empty($password) || empty($role)) {
        $message = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Format email tidak valid!";
    } else {
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = "Email sudah terdaftar. Silakan gunakan email lain.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql_insert = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssss", $nama, $email, $hashed_password, $role);
            if ($stmt_insert->execute()) {
                header("Location: login.php?status=registered");
                exit();
            } else {
                $message = "Terjadi kesalahan. Silakan coba lagi.";
            }
            $stmt_insert->close();
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi - SIMPRAK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F8F7FF;
        }
        .register-container {
            background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
        }
        .form-panel {
            background-color: white;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="flex flex-col md:flex-row w-full max-w-4xl shadow-2xl rounded-2xl overflow-hidden">
            <div class="w-full md:w-1/2 p-8 md:p-12 text-white register-container flex flex-col justify-center">
                <h1 class="text-4xl font-bold mb-3">Gabung Bersama Kami!</h1>
                <p class="mb-8">Daftarkan diri Anda untuk mulai mengikuti praktikum, mengumpulkan tugas, dan melihat nilai dengan mudah.</p>
                <div class="border-t border-white/20 pt-4">
                    <p class="text-sm">Sudah punya akun?</p>
                    <a href="login.php" class="inline-block mt-2 bg-white text-blue-700 font-bold py-2 px-6 rounded-full hover:bg-gray-100 transition-all">Login di Sini</a>
                </div>
            </div>
            <div class="w-full md:w-1/2 p-8 md:p-12 form-panel">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Buat Akun Baru</h2>
                <?php if (!empty($message)): ?>
                    <p class="bg-red-100 text-red-700 p-3 rounded-lg text-center mb-4"><?php echo htmlspecialchars($message); ?></p>
                <?php endif; ?>

                <form action="register.php" method="post" class="space-y-4">
                    <div>
                        <label for="nama" class="block font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div>
                        <label for="email" class="block font-semibold text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div>
                        <label for="password" class="block font-semibold text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div>
                        <label for="role" class="block font-semibold text-gray-700 mb-1">Daftar Sebagai</label>
                        <select id="role" name="role" required class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="asisten">Asisten</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-500 text-white font-bold py-3 px-4 rounded-lg hover:opacity-90 transition-opacity">
                        Daftar
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
