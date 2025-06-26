<?php
include '../koneksi.php';
header("Content-Type: application/json");

// Validasi input
if (empty($_POST['id'])) {
    echo json_encode(["success" => false, "message" => "ID tidak valid"]);
    exit;
}

$id = $conn->real_escape_string($_POST['id']);

// Ambil path foto sebelum dihapus
$result = $conn->query("SELECT foto FROM galeri WHERE id = $id");
if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Galeri tidak ditemukan"]);
    exit;
}

$row = $result->fetch_assoc();
$foto_path = "../uploads/galeri/" . $row['foto'];

// Hapus dari database
if ($conn->query("DELETE FROM galeri WHERE id = $id")) {
    // Hapus file foto jika ada
    if (file_exists($foto_path)) {
        unlink($foto_path);
    }
    echo json_encode(["success" => true, "message" => "Galeri berhasil dihapus"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menghapus galeri"]);
}

$conn->close();
?>