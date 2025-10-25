<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/config.php';

$client = getGoogleClient();

// Check for authorization code
if (!isset($_GET['code'])) {
    redirect(BASE_URL . '/auth/login.php');
}

try {
    // Exchange authorization code for access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if (isset($token['error'])) {
        throw new Exception($token['error_description']);
    }
    
    $_SESSION['access_token'] = $token;
    $client->setAccessToken($token);
    
    // Get user info
    $oauth = new Google_Service_Oauth2($client);
    $userInfo = $oauth->userinfo->get();
    
    // Store user information in session
    $_SESSION['user_email'] = $userInfo->email;
    $_SESSION['user_name'] = $userInfo->name;
    $_SESSION['user_picture'] = $userInfo->picture;
    $_SESSION['user_id'] = $userInfo->id;
    $_SESSION['username'] = $userInfo->name;
    $_SESSION['last_activity'] = time();
    
    // Redirect to dashboard
    redirect(BASE_URL . '/index.php');
    
} catch (Exception $e) {
    error_log('OAuth Error: ' . $e->getMessage());
    redirect(BASE_URL . '/auth/login.php?error=auth_failed');
}
