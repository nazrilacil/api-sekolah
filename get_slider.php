<?php
include '../koneksi.php';
header("Content-Type: application/json");

$result = $conn->query("SELECT * FROM sliders ORDER BY created_at DESC");
$data = [];

while ($row = $result->fetch_assoc()) {
    $row['image_url'] = 'https://osissmkalmasturiyah.42web.io/uploads/' . $row['image'];
    $data[] = $row;
}

echo json_encode($data);
?>