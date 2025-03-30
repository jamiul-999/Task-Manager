<?php
session_start();

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Task.php';
require_once __DIR__ . '/../../models/SocialShare.php';

// Login check
if (!User::isLoggedIn()) {
    header('Location: ../auth/login.php');
    exit();
}

// Retrieve logged-in user's data
$user = User::getCurrentUser();
$task = new Task();
$social = new SocialShare();

// Handle task actions (add, update, delete, complete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_task'])) {
        $task->create($user['id'], $_POST['title'], $_POST['description'], $_POST['due_date']);
    } elseif (isset($_POST['update_task'])) {
        $task->update(
            $_POST['id'],
            $_POST['title'],
            $_POST['description'],
            $_POST['due_date'],
            isset($_POST['is_completed'])
        );
    } elseif (isset($_POST['delete_task'])) {
        $task->delete($_POST['id']);
    } elseif (isset($_POST['complete_task'])) {
        $task->markAsCompleted($_POST['id']);
    }
}

// Get all tasks for the user
$tasks = $task->getAll($user['id']);

// Social media URLs
$fbLoginUrl = $social->getFacebookLoginUrl();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Task Manager</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Welcome, <?php echo $user['username']; ?></span>
                <a href="../auth/logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>My Tasks</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                            <i class="bi bi-plus"></i> Add Task
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (empty($tasks)): ?>
                            <p class="text-muted">No tasks found. Add your first task!</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tasks as $t): ?>
                                            <tr class="<?php echo $t['is_completed'] ? 'table-success' : ''; ?>">
                                                <td><?php echo htmlspecialchars($t['title']); ?></td>
                                                <td><?php echo htmlspecialchars($t['description']); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($t['due_date'])); ?></td>
                                                <td>
                                                    <?php if ($t['is_completed']): ?>
                                                        <span class="badge bg-success">Completed</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <?php if (!$t['is_completed']): ?>
                                                            <form method="POST" class="d-inline">
                                                                <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                                                                <button type="submit" name="complete_task" class="btn btn-success btn-sm">
                                                                    <i class="bi bi-check"></i>
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                            <a href="share.php?task_id=<?php echo $t['id']; ?>" class="btn btn-info btn-sm">
                                                                <i class="bi bi-share"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        
                                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" 
                                                            data-bs-target="#editTaskModal<?php echo $t['id']; ?>">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                                                            <button type="submit" name="delete_task" class="btn btn-danger btn-sm" 
                                                                onclick="return confirm('Are you sure?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                    
                                                    <!-- Edit Task Modal -->
                                                    <div class="modal fade" id="editTaskModal<?php echo $t['id']; ?>" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Edit Task</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <form method="POST">
                                                                    <div class="modal-body">
                                                                        <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Title</label>
                                                                            <input type="text" class="form-control" name="title" 
                                                                                   value="<?php echo htmlspecialchars($t['title']); ?>" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Description</label>
                                                                            <textarea class="form-control" name="description" rows="3"><?php 
                                                                                echo htmlspecialchars($t['description']); 
                                                                            ?></textarea>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Due Date</label>
                                                                            <input type="date" class="form-control" name="due_date" 
                                                                                   value="<?php echo $t['due_date']; ?>" required>
                                                                        </div>
                                                                        <div class="form-check mb-3">
                                                                            <input class="form-check-input" type="checkbox" name="is_completed" 
                                                                                   id="completed<?php echo $t['id']; ?>" 
                                                                                   <?php echo $t['is_completed'] ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label" for="completed<?php echo $t['id']; ?>">
                                                                                Mark as completed
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit" name="update_task" class="btn btn-primary">Save changes</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Social Media Integration</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6>Facebook</h6>
                            <?php if ($user['facebook_token']): ?>
                                <p class="text-success"><i class="bi bi-check-circle-fill"></i> Connected</p>
                            <?php else: ?>
                                <a href="<?php echo $fbLoginUrl; ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-facebook"></i> Connect Facebook
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" class="form-control" name="due_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_task" class="btn btn-primary">Add Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>