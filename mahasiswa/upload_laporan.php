<?php
session_start();
require_once '../config.php';

// Pastikan user adalah mahasiswa dan file diunggah via POST
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_laporan'])) {
    $id_modul     = intval($_POST['id_modul']);
    $id_praktikum = intval($_POST['id_praktikum']);
    $id_mahasiswa = $_SESSION['user_id'];

    // Pastikan file diunggah tanpa error
    if ($_FILES['file_laporan']['error'] === 0) {
        $target_dir = "../uploads/laporan/";
        
        // Pastikan folder tersedia dan bisa ditulis
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0775, true);
        }

        if (is_writable($target_dir)) {
            $ext = pathinfo($_FILES["file_laporan"]["name"], PATHINFO_EXTENSION);
            $filename = "laporan_" . $id_modul . "_" . $id_mahasiswa . "_" . time() . "." . $ext;
            $destination = $target_dir . $filename;

            if (move_uploaded_file($_FILES["file_laporan"]["tmp_name"], $destination)) {
                $stmt = $conn->prepare("INSERT INTO laporan (id_modul, id_mahasiswa, file_laporan) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $id_modul, $id_mahasiswa, $filename);
                $stmt->execute();
                $stmt->close();

                header("Location: detail_praktikum.php?id=$id_praktikum&upload=sukses");
                exit();
            }
        }
    }
}

// Jika gagal
$id_praktikum = $_POST['id_praktikum'] ?? 0;
header("Location: detail_praktikum.php?id=$id_praktikum&upload=gagal");
exit();
