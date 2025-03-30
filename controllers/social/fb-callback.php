<?php

session_start();
require_once __DIR__ . "/../../models/User.php";
require_once __DIR__ . "/../../models/SocialShare.php";

// Login check
if (!User::isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
}

// Initialize objects
$social = new SocialShare();
$user = new User();

// Handle FB callback
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $redirect_uri = 'http://localhost/TASK_MANAGER/controllers/social/fb-callback.php';
    
    $token_url = 'https://graph.facebook.com/v12.0/oauth/access_token?' . http_build_query([
        'client_id' => $social->getFacebookAppId(),
        'redirect_uri' => $redirect_uri,
        'client_secret' => $social->getFacebookAppSecret(),
        'code' => $code
    ]);
    
    // Fetch access token
    $response = file_get_contents($token_url);  //Request access token
    $data = json_decode($response, true);  // Decode JSON response

        if (isset($data['access_token'])) {
        // Save the access token to user's account
        $user->saveSocialToken('facebook', $data['access_token']);
    }
}

header("Location: ../tasks/dashboard.php");
exit();

?>