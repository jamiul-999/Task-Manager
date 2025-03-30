<?php

require_once __DIR__ . '/../models/User.php';

function getBearerToken() {
    $headers = getallheaders();
    return preg_match('/Bearer\s(.+)/', $headers['Authorization'] ?? '', $matches) 
        ? $matches[1] 
        : null;
}

function isAuthenticated() {
    $token = getBearerToken();
    if (!$token) return false;
    
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;
    
    $secret = 'your-secret-key';
    $signature = hash_hmac('sha256', "$parts[0].$parts[1]", $secret);
    
    if ($signature !== $parts[2]) return false;
    
    $payload = json_decode(base64_decode($parts[1]), true);
    return $payload['exp'] > time() ? $payload['user_id'] : false;
}
function login($data) {
    if (empty($data['username']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Username and password are required']);
        return;
    }

    $user = new User();
    if ($user->login($data['username'], $data['password'])) {
        $token = generateToken($user->getId());
        
        echo json_encode([
            'token' => $token,
            'user_id' => $user->getId()
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
}

function generateToken($user_id) {
    $payload = [
        'user_id' => $user_id,
        'exp' => time() + 3600 // 1 hour expiration
    ];
    
    $secret = 'your-secret-key'; // Change this in production!
    $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload = base64_encode(json_encode($payload));
    $signature = hash_hmac('sha256', "$header.$payload", $secret);
    
    return "$header.$payload.$signature";
}

function verifyToken($token) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;
    
    $secret = 'your-secret-key';
    $signature = hash_hmac('sha256', "$parts[0].$parts[1]", $secret);
    
    if ($signature !== $parts[2]) return false;
    
    $payload = json_decode(base64_decode($parts[1]), true);
    return $payload['exp'] > time() ? $payload['user_id'] : false;
}



function handleAuthRequest() {
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        login($data);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
}
?>