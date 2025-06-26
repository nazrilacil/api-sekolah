<?php
include 'koneksi.php';
header("Content-Type: application/json");

$result = $conn->query("SELECT * FROM galeri ORDER BY created_at DESC");
$data = [];

while ($row = $result->fetch_assoc()) {
    $row['foto_url'] = 'https://osissmkalmasturiyah.42web.io/uploads/galeri/' . $row['foto'];
    $data[] = $row;
}

echo json_encode($data);
?>
