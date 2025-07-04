<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'asisten') {
        header("Location: asisten/dashboard.php");
    } elseif ($_SESSION['role'] == 'mahasiswa') {
        header("Location: mahasiswa/dashboard.php");
    }
    exit();
}

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    if (empty($email) || empty($password)) {
        $message = "Email dan password harus diisi!";
    } else {
        $sql = "SELECT id, nama, email, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];
                if ($user['role'] == 'asisten') {
                    header("Location: asisten/dashboard.php");
                } else {
                    header("Location: mahasiswa/dashboard.php");
                }
                exit();
            } else {
                $message = "Password yang Anda masukkan salah.";
            }
        } else {
            $message = "Akun dengan email tersebut tidak ditemukan.";
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
    <title>Login - SIMPRAK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F8F7FF;
        }
        .login-container {
            background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
        }
        .form-panel {
            background-color: white;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center">
        <div class="flex flex-col md:flex-row w-full max-w-4xl shadow-2xl rounded-2xl overflow-hidden">
            <div class="w-full md:w-1/2 p-8 md:p-12 text-white login-container flex flex-col justify-center">
                <h1 class="text-4xl font-bold mb-3">Selamat Datang Kembali!</h1>
                <p class="mb-8">Masuk untuk melanjutkan dan mengelola semua tugas praktikum Anda di satu tempat.</p>
                <div class="border-t border-white/20 pt-4">
                    <p class="text-sm">Belum punya akun?</p>
                    <a href="register.php" class="inline-block mt-2 bg-white text-blue-700 font-bold py-2 px-6 rounded-full hover:bg-gray-100 transition-all">Daftar Sekarang</a>
                </div>
            </div>
            <div class="w-full md:w-1/2 p-8 md:p-12 form-panel">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Login ke SIMPRAK</h2>
                <p class="text-gray-500 mb-6">Sistem Informasi Manajemen Praktikum</p>

                <?php
                    if (isset($_GET['status']) && $_GET['status'] == 'registered') {
                        echo '<p class="bg-green-100 text-green-700 p-3 rounded-lg text-center mb-4">Registrasi berhasil! Silakan login.</p>';
                    }
                    if (!empty($message)) {
                        echo '<p class="bg-red-100 text-red-700 p-3 rounded-lg text-center mb-4">' . htmlspecialchars($message) . '</p>';
                    }
                ?>

                <form action="login.php" method="post" class="space-y-5">
                    <div>
                        <label for="email" class="block font-semibold text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div>
                        <label for="password" class="block font-semibold text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-500 text-white font-bold py-3 px-4 rounded-lg hover:opacity-90 transition-opacity">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
