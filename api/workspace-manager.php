<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {
    case 'create':
        $name = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');
        $security_level = $input['security_level'] ?? 'medium';
        $vulnerabilities = $input['vulnerabilities'] ?? [];
        
        if (empty($name)) {
            echo json_encode([
                'success' => false,
                'message' => 'El nombre es requerido'
            ]);
            exit;
        }
        
        $data = readJSON(WORKSPACES_FILE);
        
        $newWorkspace = [
            'id' => generateUUID(),
            'user_id' => $_SESSION['user_id'],
            'name' => $name,
            'description' => $description,
            'status' => 'stopped',
            'security_level' => $security_level,
            'vulnerabilities' => $vulnerabilities,
            'created_at' => date('Y-m-d H:i:s'),
            'url' => null
        ];
        
        $data['workspaces'][] = $newWorkspace;
        writeJSON(WORKSPACES_FILE, $data);
        
        echo json_encode([
            'success' => true,
            'message' => 'Workspace creado exitosamente',
            'workspace' => $newWorkspace
        ]);
        break;

    case 'update':
        $workspaceId = $input['workspace_id'] ?? '';
        $name = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');
        $security_level = $input['security_level'] ?? '';
        $vulnerabilities = $input['vulnerabilities'] ?? null;
        
        if (empty($workspaceId)) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de workspace requerido'
            ]);
            exit;
        }
        
        $data = readJSON(WORKSPACES_FILE);
        $found = false;
        
        foreach ($data['workspaces'] as $index => $w) {
            if ($w['id'] === $workspaceId && $w['user_id'] === $_SESSION['user_id']) {
                if (!empty($name)) {
                    $data['workspaces'][$index]['name'] = $name;
                }
                if (!empty($description)) {
                    $data['workspaces'][$index]['description'] = $description;
                }
                if (!empty($security_level)) {
                    $data['workspaces'][$index]['security_level'] = $security_level;
                }
                if ($vulnerabilities !== null) {
                    $data['workspaces'][$index]['vulnerabilities'] = $vulnerabilities;
                }
                $data['workspaces'][$index]['updated_at'] = date('Y-m-d H:i:s');
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            echo json_encode([
                'success' => false,
                'message' => 'Workspace no encontrado'
            ]);
            exit;
        }
        
        writeJSON(WORKSPACES_FILE, $data);
        
        echo json_encode([
            'success' => true,
            'message' => 'Workspace actualizado exitosamente'
        ]);
        break;

    case 'get':
        $workspaceId = $input['workspace_id'] ?? '';
        
        if (empty($workspaceId)) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de workspace requerido'
            ]);
            exit;
        }
        
        $data = readJSON(WORKSPACES_FILE);
        
        foreach ($data['workspaces'] as $w) {
            if ($w['id'] === $workspaceId && $w['user_id'] === $_SESSION['user_id']) {
                echo json_encode([
                    'success' => true,
                    'workspace' => $w
                ]);
                exit;
            }
        }
        
        echo json_encode([
            'success' => false,
            'message' => 'Workspace no encontrado'
        ]);
        break;

    case 'list':
        $workspaces = getUserWorkspaces($_SESSION['user_id']);
        
        echo json_encode([
            'success' => true,
            'workspaces' => $workspaces,
            'count' => count($workspaces)
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