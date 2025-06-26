<?php
include 'koneksi.php';
header("Content-Type: application/json");

// Validasi input
if (empty($_POST['id']) || empty($_POST['title'])) {
    echo json_encode(["success" => false, "message" => "Input tidak valid"]);
    exit;
}

$id = $conn->real_escape_string($_POST['id']);
$title = $conn->real_escape_string($_POST['title']);
$new_image = null;

// Handle upload gambar baru jika ada
if (!empty($_FILES['image']['name'])) {
    $image = $_FILES['image'];
    $file_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
    $file_name = uniqid('slider_') . '.' . $file_extension;
    $upload_path = "../uploads/slider/" . $file_name;
    
    if (move_uploaded_file($image['tmp_name'], $upload_path)) {
        $new_image = "slider/$file_name";
    }
}

// Ambil gambar lama untuk dihapus nanti jika ada gambar baru
$old_image = null;
if ($new_image) {
    $result = $conn->query("SELECT image FROM sliders WHERE id = $id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $old_image = $row['image'];
    }
}

// Update database
$image_update = $new_image ? ", image = '$new_image'" : "";
$query = "UPDATE sliders SET title = '$title' $image_update WHERE id = $id";

if ($conn->query($query)) {
    // Hapus gambar lama jika ada gambar baru
    if ($old_image && file_exists("../uploads/$old_image")) {
        unlink("../uploads/$old_image");
    }
    echo json_encode(["success" => true, "message" => "Slider berhasil diperbarui"]);
} else {
    // Hapus gambar baru jika gagal update
    if ($new_image && file_exists($upload_path)) {
        unlink($upload_path);
    }
    echo json_encode(["success" => false, "message" => "Gagal memperbarui slider"]);
}

$conn->close();
?>
