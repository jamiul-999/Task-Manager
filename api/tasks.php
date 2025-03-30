<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Task.php';
function handleTasksRequest($id = null) {
    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    $user_id = verifyToken(getBearerToken());
    $task = new Task();
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if ($id) {
                getTask($id, $user_id);
            } else {
                getAllTasks($user_id);
            }
            break;
            
        case 'POST':
            createTask($user_id);
            break;
            
        case 'PUT':
            if ($id) {
                updateTask($id, $user_id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Task ID required']);
            }
            break;
            
        case 'DELETE':
            if ($id) {
                deleteTask($id, $user_id);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Task ID required']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

function getAllTasks($user_id) {
    $tasks = (new Task())->getAll($user_id);
    echo json_encode($tasks);
}

function getTask($id, $user_id) {
    $task = (new Task())->getById($id);
    
    if (!$task || $task['user_id'] != $user_id) {
        http_response_code(404);
        echo json_encode(['error' => 'Task not found']);
        return;
    }
    
    echo json_encode($task);
}

function createTask($user_id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['title'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Title is required']);
        return;
    }
    
    $task = new Task();
    if ($task->create(
        $user_id,
        $data['title'],
        $data['description'] ?? '',
        $data['due_date'] ?? null
    )) {
        http_response_code(201);
        echo json_encode(['message' => 'Task created']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Task creation failed']);
    }
}

function updateTask($id, $user_id) {
    $task = new Task();
    $existing = $task->getById($id);
    
    if (!$existing || $existing['user_id'] != $user_id) {
        http_response_code(404);
        echo json_encode(['error' => 'Task not found']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($task->update(
        $id,
        $data['title'] ?? $existing['title'],
        $data['description'] ?? $existing['description'],
        $data['due_date'] ?? $existing['due_date'],
        $data['is_completed'] ?? $existing['is_completed']
    )) {
        echo json_encode(['message' => 'Task updated']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Task update failed']);
    }
}

function deleteTask($id, $user_id) {
    $task = new Task();
    $existing = $task->getById($id);
    
    if (!$existing || $existing['user_id'] != $user_id) {
        http_response_code(404);
        echo json_encode(['error' => 'Task not found']);
        return;
    }
    
    if ($task->delete($id)) {
        echo json_encode(['message' => 'Task deleted']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Task deletion failed']);
    }
}
?>