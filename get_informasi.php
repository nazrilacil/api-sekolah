<?php
include 'koneksi.php';
header("Content-Type: application/json");

$result = $conn->query("SELECT * FROM informasi ORDER BY created_at DESC");
$data = [];

while ($row = $result->fetch_assoc()) {
    $row['gambar_url'] = 'https://osissmkalmasturiyah.42web.io/uploads/informasi/' . $row['gambar'];
    $data[] = $row;
}

echo json_encode($data);
?>
