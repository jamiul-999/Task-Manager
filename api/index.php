<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/tasks.php';

// Get the request path
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base_path = '/Task_Manager/api'; // Adjust to your project path
$endpoint = str_replace($base_path, '', $request_uri);
$endpoint = trim($endpoint, '/');
$parts = explode('/', $endpoint);

// Route the request
$resource = $parts[0] ?? '';
$id = $parts[1] ?? null;

switch ($resource) {
    case 'auth':
        handleAuthRequest();
        break;
    case 'tasks':
        handleTasksRequest($id);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Resource not found']);
        break;
}
?>