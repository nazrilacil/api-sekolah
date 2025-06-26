<?php
include 'koneksi.php';

$query = $conn->query("SELECT tentang_sekolah, visi, misi, sambutan_kepsek FROM pengaturan LIMIT 1");

if ($query) {
    $data = $query->fetch_assoc();
    echo json_encode([
        'status' => 'success',
        'data' => $data
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal mengambil data tentang sekolah.'
    ]);
}
?>
