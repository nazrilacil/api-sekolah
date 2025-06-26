<?php
include '../koneksi.php';
header("Content-Type: application/json");

// Validasi input
if (empty($_POST['id'])) {
    echo json_encode(["success" => false, "message" => "ID tidak valid"]);
    exit;
}

$id = $conn->real_escape_string($_POST['id']);

// Ambil path gambar sebelum dihapus
$result = $conn->query("SELECT image FROM sliders WHERE id = $id");
if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Slider tidak ditemukan"]);
    exit;
}

$row = $result->fetch_assoc();
$image_path = "../uploads/" . $row['image'];

// Hapus dari database
if ($conn->query("DELETE FROM sliders WHERE id = $id")) {
    // Hapus file gambar jika ada
    if (file_exists($image_path)) {
        unlink($image_path);
    }
    echo json_encode(["success" => true, "message" => "Slider berhasil dihapus"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menghapus slider"]);
}

$conn->close();
?>