<?php
include '../koneksi.php';
header("Content-Type: application/json");

// Validasi input
if (empty($_POST['title'])) {
    echo json_encode(["success" => false, "message" => "Judul harus diisi"]);
    exit;
}

if (empty($_FILES['image']['name'])) {
    echo json_encode(["success" => false, "message" => "Gambar harus diupload"]);
    exit;
}

$title = $conn->real_escape_string($_POST['title']);
$image = $_FILES['image'];

// Generate nama file unik
$file_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
$file_name = uniqid('slider_') . '.' . $file_extension;
$upload_path = "../uploads/slider/" . $file_name;

// Buat folder jika belum ada
if (!is_dir('../uploads/slider')) {
    mkdir('../uploads/slider', 0777, true);
}

// Upload file
if (move_uploaded_file($image['tmp_name'], $upload_path)) {
    $query = "INSERT INTO sliders (title, image, created_at) 
              VALUES ('$title', 'slider/$file_name', NOW())";
    
    if ($conn->query($query)) {
        echo json_encode(["success" => true, "message" => "Slider berhasil ditambahkan"]);
    } else {
        // Hapus file jika gagal insert ke database
        unlink($upload_path);
        echo json_encode(["success" => false, "message" => "Gagal menyimpan ke database"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Gagal mengupload gambar"]);
}

$conn->close();
?>