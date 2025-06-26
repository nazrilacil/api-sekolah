<?php
include 'koneksi.php';
header("Content-Type: application/json");

// Validasi input
if (empty($_POST['judul']) || empty($_POST['keterangan'])) {
    echo json_encode(["success" => false, "message" => "Judul dan keterangan harus diisi"]);
    exit;
}

$judul = $conn->real_escape_string($_POST['judul']);
$keterangan = $conn->real_escape_string($_POST['keterangan']);
$created_by = $conn->real_escape_string($_POST['created_by'] ?? 'admin'); // Default jika tidak ada

// Handle gambar
$gambar = null;
if (!empty($_FILES['gambar']['name'])) {
    $file = $_FILES['gambar'];
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = uniqid('informasi_') . '.' . $file_extension;
    $upload_path = "../uploads/informasi/" . $file_name;

    // Buat folder jika belum ada
    if (!is_dir('../uploads/informasi')) {
        mkdir('../uploads/informasi', 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $gambar = $file_name;
    } else {
        echo json_encode(["success" => false, "message" => "Gagal mengupload gambar"]);
        exit;
    }
} else {
    echo json_encode(["success" => false, "message" => "Gambar harus diupload"]);
    exit;
}

$query = "INSERT INTO informasi (judul, keterangan, gambar, created_at, updated_at, created_by) 
          VALUES ('$judul', '$keterangan', '$gambar', NOW(), NOW(), '$created_by')";

if ($conn->query($query)) {
    echo json_encode(["success" => true, "message" => "Informasi berhasil ditambahkan"]);
} else {
    // Hapus gambar jika gagal
    if ($gambar && file_exists($upload_path)) {
        unlink($upload_path);
    }
    echo json_encode(["success" => false, "message" => "Gagal menambahkan informasi: " . $conn->error]);
}

$conn->close();
?>
