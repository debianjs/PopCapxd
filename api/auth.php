<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'logout':
        session_destroy();
        header('Location: ../index.php');
        exit;
        break;

    case 'check':
        echo json_encode([
            'logged_in' => isLoggedIn(),
            'user' => isLoggedIn() ? [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username']
            ] : null
        ]);
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Acción no válida'
        ]);
        break;
}
?>