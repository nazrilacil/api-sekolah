<?php
include 'koneksi.php';

$query = $conn->query("SELECT 
    nama,
    email,
    telepon,
    alamat,
    logo,
    favicon,
    foto_sekolah,
    nama_kepsek,
    foto_kepsek,
    google_maps
FROM pengaturan LIMIT 1");

if ($query) {
    $data = $query->fetch_assoc();

    // Tambahkan path URL gambar jika perlu
    $data['logo'] = 'https://osissmkalmasturiyah.42web.io/uploads/identitas/' . $data['logo'];
    $data['favicon'] = 'https://osissmkalmasturiyah.42web.io/uploads/identitas/' . $data['favicon'];
    $data['foto_sekolah'] = 'https://osissmkalmasturiyah.42web.io/uploads/identitas/' . $data['foto_sekolah'];
    $data['foto_kepsek'] = 'https://osissmkalmasturiyah.42web.io/uploads/identitas/' . $data['foto_kepsek'];

    echo json_encode([
        'status' => 'success',
        'data' => $data
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal mengambil data identitas sekolah.'
    ]);
}
?>
