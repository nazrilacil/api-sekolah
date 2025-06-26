<?php
include '../koneksi.php';
header("Content-Type: application/json");

// Validasi input
if (empty($_POST['keterangan'])) {
    echo json_encode(["success" => false, "message" => "Keterangan harus diisi"]);
    exit;
}

if (empty($_FILES['foto']['name'])) {
    echo json_encode(["success" => false, "message" => "Foto harus diupload"]);
    exit;
}

$keterangan = $conn->real_escape_string($_POST['keterangan']);
$foto = $_FILES['foto'];

// Generate nama file unik
$file_extension = pathinfo($foto['name'], PATHINFO_EXTENSION);
$file_name = uniqid('galeri_') . '.' . $file_extension;
$upload_path = "../uploads/galeri/" . $file_name;

// Buat folder jika belum ada
if (!is_dir('../uploads/galeri')) {
    mkdir('../uploads/galeri', 0777, true);
}

// Upload file
if (move_uploaded_file($foto['tmp_name'], $upload_path)) {
    $query = "INSERT INTO galeri (foto, keterangan, created_at) 
              VALUES ('$file_name', '$keterangan', NOW())";
    
    if ($conn->query($query)) {
        echo json_encode(["success" => true, "message" => "Galeri berhasil ditambahkan"]);
    } else {
        // Hapus file jika gagal insert ke database
        unlink($upload_path);
        echo json_encode(["success" => false, "message" => "Gagal menyimpan ke database"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Gagal mengupload foto"]);
}

$conn->close();
?>