<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_laporan = intval($_POST['id_laporan']);
    $nilai = intval($_POST['nilai']);

    $stmt = $conn->prepare("UPDATE laporan SET nilai = ? WHERE id = ?");
    $stmt->bind_param("ii", $nilai, $id_laporan);

    if ($stmt->execute()) {
        header("Location: laporan_masuk.php?status=sukses");
    } else {
        header("Location: laporan_masuk.php?status=gagal");
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: laporan_masuk.php");
    exit();
}
