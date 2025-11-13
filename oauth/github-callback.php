<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_GET['code'])) {
    header('Location: ../login.php?error=github_auth_failed');
    exit;
}

$code = $_GET['code'];

// Obtener datos del usuario de GitHub
$result = getGitHubUser($code);

if (!$result['success']) {
    header('Location: ../login.php?error=' . urlencode($result['message']));
    exit;
}

// Crear o actualizar usuario
$userResult = createOrUpdateOAuthUser($result['user']);

if ($userResult['success']) {
    $_SESSION['user_id'] = $userResult['user']['id'];
    $_SESSION['username'] = $userResult['user']['username'];
    $_SESSION['oauth_provider'] = 'github';
    
    header('Location: ../dash/flash.php');
    exit;
} else {
    header('Location: ../login.php?error=user_creation_failed');
    exit;
}
?>