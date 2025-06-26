<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Haders, Authorization, X-Requested-With");

include_once '../koneksi.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

file_put_contents('login_requests.log', date('Y-m-d H:i:s') . " Request: " . file_get_contents('php://input') . "\n", FILE_APPEND);

$json = file_get_contents('php://input');
$data = json_decode($json);

if(!$data || empty($data->username) || empty($data->password)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Username dan password harus diisi",
        "received_data" => $json
    ]);
    exit();
}

$username = trim($data->username);
$password = trim($data->password);

$query = "SELECT id, name, username, password, level FROM pengguna 
          WHERE LOWER(username) = LOWER(?)";
$stmt = $conn->prepare($query);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Prepare failed: " . $conn->error
    ]);
    exit();
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Username tidak ditemukan"
    ]);
    exit();
}

$user = $result->fetch_assoc();

$hashed_password = md5($password);
if($hashed_password !== $user['password']) {
    $debug = "Input password: $password\n";
    $debug .= "Hashed input: $hashed_password\n";
    $debug .= "DB password: " . $user['password'] . "\n";
    file_put_contents('password_debug.log', $debug, FILE_APPEND);
    
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Password salah",
        "debug" => $debug
    ]);
    exit();
}

unset($user['password']);

http_response_code(200);
echo json_encode([
    "success" => true,
    "message" => "Login berhasil",
    "user" => $user
]);

$stmt->close();
$conn->close();
?>