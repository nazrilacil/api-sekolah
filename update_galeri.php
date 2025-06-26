<?php
include '../koneksi.php';
header("Content-Type: application/json");

// Validasi input
if (empty($_POST['id']) || empty($_POST['keterangan'])) {
    echo json_encode(["success" => false, "message" => "Input tidak valid"]);
    exit;
}

$id = $conn->real_escape_string($_POST['id']);
$keterangan = $conn->real_escape_string($_POST['keterangan']);
$new_foto = null;

// Handle upload foto baru jika ada
if (!empty($_FILES['foto']['name'])) {
    $foto = $_FILES['foto'];
    $file_extension = pathinfo($foto['name'], PATHINFO_EXTENSION);
    $file_name = uniqid('galeri_') . '.' . $file_extension;
    $upload_path = "../uploads/galeri/" . $file_name;
    
    if (move_uploaded_file($foto['tmp_name'], $upload_path)) {
        $new_foto = $file_name;
    }
}

// Ambil foto lama untuk dihapus nanti jika ada foto baru
$old_foto = null;
if ($new_foto) {
    $result = $conn->query("SELECT foto FROM galeri WHERE id = $id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $old_foto = $row['foto'];
    }
}

// Update database
$foto_update = $new_foto ? ", foto = '$new_foto'" : "";
$query = "UPDATE galeri SET keterangan = '$keterangan' $foto_update, updated_at = NOW() WHERE id = $id";

if ($conn->query($query)) {
    // Hapus foto lama jika ada foto baru
    if ($old_foto && file_exists("../uploads/galeri/$old_foto")) {
        unlink("../uploads/galeri/$old_foto");
    }
    echo json_encode(["success" => true, "message" => "Galeri berhasil diperbarui"]);
} else {
    // Hapus foto baru jika gagal update
    if ($new_foto && file_exists($upload_path)) {
        unlink($upload_path);
    }
    echo json_encode(["success" => false, "message" => "Gagal memperbarui galeri"]);
}

$conn->close();
?>