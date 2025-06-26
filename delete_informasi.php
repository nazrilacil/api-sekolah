<?php
include 'koneksi.php';
header("Content-Type: application/json");

if (empty($_POST['id'])) {
    echo json_encode(["success" => false, "message" => "ID tidak valid"]);
    exit;
}

$id = $conn->real_escape_string($_POST['id']);

// Ambil data untuk hapus gambar
$result = $conn->query("SELECT gambar FROM informasi WHERE id = $id");
if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Informasi tidak ditemukan"]);
    exit;
}

$row = $result->fetch_assoc();
$gambar = $row['gambar'];

if ($conn->query("DELETE FROM informasi WHERE id = $id")) {
    // Hapus gambar
    if ($gambar && file_exists("../uploads/informasi/$gambar")) {
        unlink("../uploads/informasi/$gambar");
    }
    echo json_encode(["success" => true, "message" => "Informasi berhasil dihapus"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menghapus informasi"]);
}

$conn->close();
?>
