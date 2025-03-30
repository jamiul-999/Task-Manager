<?php

session_start();

require_once __DIR__ . "/../../models/User.php";
require_once __DIR__ . "/../../models/Task.php";
require_once __DIR__ . "/../../models/SocialShare.php";

// Login check
if (!User::isLoggedIn()) {
    header('Location: ../auth/login.php');
    exit();
}

// Retrieve login data
$task_id = $_GET['task_id'] ?? 0;
$user = User::getCurrentUser();
$task = new Task();
$social = new SocialShare();

// Get task details
$task_details = $task->getById($task_id);
if (!$task_details || $task_details['user_id'] != $user['id']) {
    header('Location: ../tasks/dashboard.php');
    exit();
}

// The message/post to be shared
$message = "I just completed a task: {$task_details['title']} - {$task_details['description']}";
$success = '';
$error = '';

// Check token and post to FB
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['share_facebook']) && $user['facebook_token']) {
        $result = $social->postToFacebook($user['facebook_token'], $message);
        if (isset($result['id'])) {
            $success = "Successfully shared on Facebook!";
        } else {
            $error = "Failed to share on Facebook: " . ($result['error']['message'] ?? 'Unknown error');
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Task - Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Task Manager</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Welcome, <?php echo $user['username']; ?></span>
                <a href="dashboard.php" class="btn btn-outline-light">Back to Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Share Completed Task</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <div class="mb-4">
                            <h6>Task Details</h6>
                            <div class="card">
                                <div class="card-body">
                                    <h5><?php echo htmlspecialchars($task_details['title']); ?></h5>
                                    <p><?php echo htmlspecialchars($task_details['description']); ?></p>
                                    <small class="text-muted">
                                        Completed on <?php echo date('M d, Y', strtotime($task_details['created_at'])); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h6>Share on Social Media</h6>
                            <form method="POST">
                                <?php if ($user['facebook_token']): ?>
                                    <button type="submit" name="share_facebook" class="btn btn-primary mb-2 w-100">
                                        <i class="bi bi-facebook"></i> Share on Facebook
                                    </button>
                                <?php else: ?>
                                    <p class="text-muted">Connect your Facebook account to share</p>
                                <?php endif; ?>
                                
                                <?php if ($user['google_token']): ?>
                                    <button type="submit" name="share_google" class="btn btn-danger mb-2 w-100">
                                        <i class="bi bi-google"></i> Share on Google My Business
                                    </button>
                                <?php else: ?>
                                    <p class="text-muted">Connect your Google account to share</p>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>