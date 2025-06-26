<?php
include '../koneksi.php';
header("Content-Type: application/json");

// Validasi input
if (empty($_POST['id']) || empty($_POST['judul']) || empty($_POST['keterangan'])) {
    echo json_encode(["success" => false, "message" => "Input tidak valid"]);
    exit;
}

$id = $conn->real_escape_string($_POST['id']);
$judul = $conn->real_escape_string($_POST['judul']);
$keterangan = $conn->real_escape_string($_POST['keterangan']);

// Ambil data lama untuk gambar
$old_gambar = null;
$result = $conn->query("SELECT gambar FROM informasi WHERE id = $id");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $old_gambar = $row['gambar'];
}

$gambar = $old_gambar;
$new_uploaded = false;

// Handle upload gambar baru jika ada
if (!empty($_FILES['gambar']['name'])) {
    $file = $_FILES['gambar'];
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = uniqid('informasi_') . '.' . $file_extension;
    $upload_path = "../uploads/informasi/" . $file_name;

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $gambar = $file_name;
        $new_uploaded = true;
    } else {
        echo json_encode(["success" => false, "message" => "Gagal mengupload gambar baru"]);
        exit;
    }
}

$query = "UPDATE informasi SET 
            judul = '$judul',
            keterangan = '$keterangan',
            gambar = '$gambar',
            updated_at = NOW()
          WHERE id = $id";

if ($conn->query($query)) {
    // Hapus gambar lama jika ada gambar baru yang diupload
    if ($new_uploaded && $old_gambar && file_exists("../uploads/informasi/$old_gambar")) {
        unlink("../uploads/informasi/$old_gambar");
    }
    echo json_encode(["success" => true, "message" => "Informasi berhasil diperbarui"]);
} else {
    // Hapus gambar baru jika gagal update
    if ($new_uploaded && file_exists($upload_path)) {
        unlink($upload_path);
    }
    echo json_encode(["success" => false, "message" => "Gagal memperbarui informasi: " . $conn->error]);
}

$conn->close();
?>