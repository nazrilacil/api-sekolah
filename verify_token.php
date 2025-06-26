<?php
header('Content-Type: application/json');
require_once '../koneksi.php';
require_once '../jwt_config.php'; // Include konfigurasi JWT

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

// Fungsi untuk mengambil token dari header
function getBearerToken() {
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER['Authorization']);
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(
            array_map('ucwords', array_keys($requestHeaders)), 
            array_values($requestHeaders)
        );
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    
    if ($headers && preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        return $matches[1];
    }
    return null;
}

try {
    // Ambil token dari request
    $token = getBearerToken();
    
    if (!$token) {
        throw new Exception('Token tidak ditemukan');
    }

    // Verifikasi token
    $decoded = JWT::decode($token, new Key(JWT_SECRET, JWT_ALGORITHM));
    
    // Pastikan token memiliki user ID
    if (!isset($decoded->user_id)) {
        throw new Exception('Token tidak valid: user_id tidak ditemukan');
    }
    
    $user_id = $decoded->user_id;
    
    // Cek ke database apakah user masih valid
    $query = "SELECT id, name, username, level FROM pengguna WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('User tidak ditemukan di database');
    }
    
    $user = $result->fetch_assoc();
    
    // Jika verifikasi sukses, kembalikan data user
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Token valid',
        'user' => $user
    ]);
    
} catch (ExpiredException $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Token kedaluwarsa'
    ]);
} catch (SignatureInvalidException $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Signature tidak valid'
    ]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    $conn->close();
}