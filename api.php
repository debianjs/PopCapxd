<?php
/**
 * MicroHack API REST
 * Endpoint único para todas las operaciones de la plataforma
 * 
 * Uso:
 * POST /api.php
 * Content-Type: application/json
 * Body: {"action": "nombre_accion", "params": {...}}
 */

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once 'includes/config.php';
require_once 'includes/functions.php';

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Función para responder
function respond($success, $message = '', $data = null, $code = 200) {
    http_response_code($code);
    $response = [
        'success' => $success,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

// Función para verificar autenticación
function requireAuth() {
    if (!isLoggedIn()) {
        respond(false, 'No autorizado. Debes iniciar sesión.', null, 401);
    }
}

// Obtener datos de la petición
$input = file_get_contents('php://input');
$request = json_decode($input, true);

// Si no es JSON, intentar con POST/GET
if (!$request) {
    $request = array_merge($_POST, $_GET);
}

$action = $request['action'] ?? '';
$params = $request['params'] ?? $request;

// ========================================
// ROUTER DE ACCIONES
// ========================================

switch ($action) {
    
    // ========================================
    // AUTENTICACIÓN
    // ========================================
    
    case 'auth.register':
        $username = trim($params['username'] ?? '');
        $password = $params['password'] ?? '';
        $email = trim($params['email'] ?? '');
        
        if (empty($username) || empty($password)) {
            respond(false, 'Usuario y contraseña son requeridos', null, 400);
        }
        
        if (strlen($username) < 3) {
            respond(false, 'El usuario debe tener al menos 3 caracteres', null, 400);
        }
        
        if (strlen($password) < 6) {
            respond(false, 'La contraseña debe tener al menos 6 caracteres', null, 400);
        }
        
        $result = registerUser($username, $password, $email);
        
        if ($result['success']) {
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['username'] = $result['user']['username'];
            respond(true, 'Usuario registrado exitosamente', $result['user']);
        } else {
            respond(false, $result['message'], null, 400);
        }
        break;
    
    case 'auth.login':
        $username = trim($params['username'] ?? '');
        $password = $params['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            respond(false, 'Usuario y contraseña son requeridos', null, 400);
        }
        
        $result = loginUser($username, $password);
        
        if ($result['success']) {
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['username'] = $result['user']['username'];
            respond(true, 'Login exitoso', [
                'user' => $result['user'],
                'session_id' => session_id()
            ]);
        } else {
            respond(false, $result['message'], null, 401);
        }
        break;
    
    case 'auth.logout':
        requireAuth();
        
        $username = $_SESSION['username'] ?? 'Usuario';
        session_destroy();
        
        respond(true, "Sesión cerrada exitosamente. Hasta pronto, $username!");
        break;
    
    case 'auth.check':
        respond(true, 'Estado de autenticación', [
            'logged_in' => isLoggedIn(),
            'user_id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null
        ]);
        break;
    
    case 'auth.me':
        requireAuth();
        
        $user = getUserById($_SESSION['user_id']);
        
        if ($user) {
            // Remover contraseña de la respuesta
            unset($user['password']);
            respond(true, 'Información del usuario', $user);
        } else {
            respond(false, 'Usuario no encontrado', null, 404);
        }
        break;
    
    // ========================================
    // WORKSPACES - CRUD
    // ========================================
    
    case 'workspace.create':
        requireAuth();
        
        $name = trim($params['name'] ?? '');
        $description = trim($params['description'] ?? '');
        $security_level = $params['security_level'] ?? 'medium';
        $vulnerabilities = $params['vulnerabilities'] ?? [];
        
        if (empty($name)) {
            respond(false, 'El nombre del workspace es requerido', null, 400);
        }
        
        if (!in_array($security_level, ['low', 'medium', 'high'])) {
            respond(false, 'Nivel de seguridad inválido', null, 400);
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
            'updated_at' => date('Y-m-d H:i:s'),
            'url' => null,
            'stats' => [
                'total_starts' => 0,
                'total_resets' => 0,
                'last_activity' => null
            ]
        ];
        
        $data['workspaces'][] = $newWorkspace;
        writeJSON(WORKSPACES_FILE, $data);
        
        respond(true, 'Workspace creado exitosamente', $newWorkspace, 201);
        break;
    
    case 'workspace.list':
        requireAuth();
        
        $workspaces = getUserWorkspaces($_SESSION['user_id']);
        
        respond(true, 'Lista de workspaces obtenida', [
            'workspaces' => $workspaces,
            'total' => count($workspaces),
            'active' => count(array_filter($workspaces, fn($w) => $w['status'] === 'running')),
            'stopped' => count(array_filter($workspaces, fn($w) => $w['status'] === 'stopped'))
        ]);
        break;
    
    case 'workspace.get':
        requireAuth();
        
        $workspaceId = $params['workspace_id'] ?? '';
        
        if (empty($workspaceId)) {
            respond(false, 'ID de workspace requerido', null, 400);
        }
        
        $data = readJSON(WORKSPACES_FILE);
        
        foreach ($data['workspaces'] as $workspace) {
            if ($workspace['id'] === $workspaceId && $workspace['user_id'] === $_SESSION['user_id']) {
                respond(true, 'Workspace encontrado', $workspace);
            }
        }
        
        respond(false, 'Workspace no encontrado', null, 404);
        break;
    
    case 'workspace.update':
        requireAuth();
        
        $workspaceId = $params['workspace_id'] ?? '';
        
        if (empty($workspaceId)) {
            respond(false, 'ID de workspace requerido', null, 400);
        }
        
        $data = readJSON(WORKSPACES_FILE);
        $found = false;
        
        foreach ($data['workspaces'] as $index => $workspace) {
            if ($workspace['id'] === $workspaceId && $workspace['user_id'] === $_SESSION['user_id']) {
                // Actualizar campos permitidos
                if (isset($params['name'])) {
                    $data['workspaces'][$index]['name'] = trim($params['name']);
                }
                if (isset($params['description'])) {
                    $data['workspaces'][$index]['description'] = trim($params['description']);
                }
                if (isset($params['security_level'])) {
                    $data['workspaces'][$index]['security_level'] = $params['security_level'];
                }
                if (isset($params['vulnerabilities'])) {
                    $data['workspaces'][$index]['vulnerabilities'] = $params['vulnerabilities'];
                }
                
                $data['workspaces'][$index]['updated_at'] = date('Y-m-d H:i:s');
                
                writeJSON(WORKSPACES_FILE, $data);
                $found = true;
                
                respond(true, 'Workspace actualizado exitosamente', $data['workspaces'][$index]);
            }
        }
        
        if (!$found) {
            respond(false, 'Workspace no encontrado', null, 404);
        }
        break;
    
    case 'workspace.delete':
        requireAuth();
        
        $workspaceId = $params['workspace_id'] ?? '';
        
        if (empty($workspaceId)) {
            respond(false, 'ID de workspace requerido', null, 400);
        }
        
        $data = readJSON(WORKSPACES_FILE);
        $found = false;
        
        foreach ($data['workspaces'] as $index => $workspace) {
            if ($workspace['id'] === $workspaceId && $workspace['user_id'] === $_SESSION['user_id']) {
                // Eliminar directorio del workspace
                $workspacePath = BASE_PATH . '/vulnerable-sites/' . $workspaceId;
                if (file_exists($workspacePath)) {
                    deleteDirectory($workspacePath);
                }
                
                array_splice($data['workspaces'], $index, 1);
                writeJSON(WORKSPACES_FILE, $data);
                $found = true;
                
                respond(true, 'Workspace eliminado exitosamente');
            }
        }
        
        if (!$found) {
            respond(false, 'Workspace no encontrado', null, 404);
        }
        break;
    
    // ========================================
    // WORKSPACES - ACCIONES
    // ========================================
    
    case 'workspace.start':
        requireAuth();
        
        $workspaceId = $params['workspace_id'] ?? '';
        
        if (empty($workspaceId)) {
            respond(false, 'ID de workspace requerido', null, 400);
        }
        
        $data = readJSON(WORKSPACES_FILE);
        $found = false;
        
        foreach ($data['workspaces'] as $index => $workspace) {
            if ($workspace['id'] === $workspaceId && $workspace['user_id'] === $_SESSION['user_id']) {
                if ($workspace['status'] === 'running') {
                    respond(false, 'El workspace ya está activo', null, 400);
                }
                
                // Crear directorio y archivos vulnerables
                $workspacePath = BASE_PATH . '/vulnerable-sites/' . $workspaceId;
                if (!file_exists($workspacePath)) {
                    mkdir($workspacePath, 0755, true);
                    createVulnerableSite($workspaceId, $workspace['vulnerabilities']);
                }
                
                // Actualizar estado
                $data['workspaces'][$index]['status'] = 'running';
                $data['workspaces'][$index]['started_at'] = date('Y-m-d H:i:s');
                $data['workspaces'][$index]['url'] = BASE_URL . '/vulnerable-sites/' . $workspaceId . '/';
                $data['workspaces'][$index]['stats']['total_starts']++;
                $data['workspaces'][$index]['stats']['last_activity'] = date('Y-m-d H:i:s');
                
                writeJSON(WORKSPACES_FILE, $data);
                $found = true;
                
                respond(true, 'Workspace iniciado exitosamente', $data['workspaces'][$index]);
            }
        }
        
        if (!$found) {
            respond(false, 'Workspace no encontrado', null, 404);
        }
        break;
    
    case 'workspace.stop':
        requireAuth();
        
        $workspaceId = $params['workspace_id'] ?? '';
        
        if (empty($workspaceId)) {
            respond(false, 'ID de workspace requerido', null, 400);
        }
        
        $data = readJSON(WORKSPACES_FILE);
        $found = false;
        
        foreach ($data['workspaces'] as $index => $workspace) {
            if ($workspace['id'] === $workspaceId && $workspace['user_id'] === $_SESSION['user_id']) {
                if ($workspace['status'] === 'stopped') {
                    respond(false, 'El workspace ya está detenido', null, 400);
                }
                
                $data['workspaces'][$index]['status'] = 'stopped';
                $data['workspaces'][$index]['stopped_at'] = date('Y-m-d H:i:s');
                $data['workspaces'][$index]['stats']['last_activity'] = date('Y-m-d H:i:s');
                
                writeJSON(WORKSPACES_FILE, $data);
                $found = true;
                
                respond(true, 'Workspace detenido exitosamente', $data['workspaces'][$index]);
            }
        }
        
        if (!$found) {
            respond(false, 'Workspace no encontrado', null, 404);
        }
        break;
    
    case 'workspace.reset':
        requireAuth();
        
        $workspaceId = $params['workspace_id'] ?? '';
        
        if (empty($workspaceId)) {
            respond(false, 'ID de workspace requerido', null, 400);
        }
        
        $data = readJSON(WORKSPACES_FILE);
        $found = false;
        
        foreach ($data['workspaces'] as $index => $workspace) {
            if ($workspace['id'] === $workspaceId && $workspace['user_id'] === $_SESSION['user_id']) {
                // Eliminar y recrear archivos
                $workspacePath = BASE_PATH . '/vulnerable-sites/' . $workspaceId;
                if (file_exists($workspacePath)) {
                    deleteDirectory($workspacePath);
                }
                
                mkdir($workspacePath, 0755, true);
                createVulnerableSite($workspaceId, $workspace['vulnerabilities']);
                
                $data['workspaces'][$index]['reset_at'] = date('Y-m-d H:i:s');
                $data['workspaces'][$index]['stats']['total_resets']++;
                $data['workspaces'][$index]['stats']['last_activity'] = date('Y-m-d H:i:s');
                
                writeJSON(WORKSPACES_FILE, $data);
                $found = true;
                
                respond(true, 'Workspace reseteado exitosamente', $data['workspaces'][$index]);
            }
        }
        
        if (!$found) {
            respond(false, 'Workspace no encontrado', null, 404);
        }
        break;
    
    // ========================================
    // ESTADÍSTICAS
    // ========================================
    
    case 'stats.user':
        requireAuth();
        
        $workspaces = getUserWorkspaces($_SESSION['user_id']);
        
        $stats = [
            'total_workspaces' => count($workspaces),
            'active_workspaces' => count(array_filter($workspaces, fn($w) => $w['status'] === 'running')),
            'stopped_workspaces' => count(array_filter($workspaces, fn($w) => $w['status'] === 'stopped')),
            'total_vulnerabilities' => array_sum(array_map(fn($w) => count($w['vulnerabilities'] ?? []), $workspaces)),
            'total_starts' => array_sum(array_map(fn($w) => $w['stats']['total_starts'] ?? 0, $workspaces)),
            'total_resets' => array_sum(array_map(fn($w) => $w['stats']['total_resets'] ?? 0, $workspaces)),
            'vulnerabilities_breakdown' => []
        ];
        
        // Contar vulnerabilidades por tipo
        $vulnCount = [];
        foreach ($workspaces as $workspace) {
            foreach ($workspace['vulnerabilities'] ?? [] as $vuln) {
                $vulnCount[$vuln] = ($vulnCount[$vuln] ?? 0) + 1;
            }
        }
        $stats['vulnerabilities_breakdown'] = $vulnCount;
        
        respond(true, 'Estadísticas del usuario', $stats);
        break;
    
    case 'stats.platform':
        // Estadísticas globales (no requiere auth)
        
        $usersData = readJSON(USERS_FILE);
        $workspacesData = readJSON(WORKSPACES_FILE);
        
        $stats = [
            'total_users' => count($usersData['users'] ?? []),
            'total_workspaces' => count($workspacesData['workspaces'] ?? []),
            'active_workspaces' => count(array_filter($workspacesData['workspaces'] ?? [], fn($w) => $w['status'] === 'running')),
            'platform_version' => '1.0.0',
            'uptime' => 'Available'
        ];
        
        respond(true, 'Estadísticas de la plataforma', $stats);
        break;
    
    // ========================================
    // UTILIDADES
    // ========================================
    
    case 'util.verify_turnstile':
        $token = $params['token'] ?? '';
        $userIP = $_SERVER['REMOTE_ADDR'] ?? null;
        
        if (empty($token)) {
            respond(false, 'Token de Turnstile requerido', null, 400);
        }
        
        $result = verifyTurnstile($token, $userIP);
        
        if ($result['success']) {
            respond(true, 'Verificación de Turnstile exitosa');
        } else {
            respond(false, $result['message'], null, 400);
        }
        break;
    
    case 'util.health':
        // Health check endpoint
        respond(true, 'API funcionando correctamente', [
            'status' => 'healthy',
            'version' => '1.0.0',
            'timestamp' => date('Y-m-d H:i:s'),
            'php_version' => phpversion()
        ]);
        break;
    
    // ========================================
    // VULNERABILIDADES - INFO
    // ========================================
    
    case 'vulnerabilities.list':
        $vulnerabilities = [
            [
                'id' => 'sql_injection',
                'name' => 'SQL Injection',
                'description' => 'Inyección de código SQL en formularios sin validación',
                'severity' => 'critical',
                'category' => 'injection'
            ],
            [
                'id' => 'xss',
                'name' => 'Cross-Site Scripting (XSS)',
                'description' => 'Inyección de scripts maliciosos en páginas web',
                'severity' => 'high',
                'category' => 'injection'
            ],
            [
                'id' => 'csrf',
                'name' => 'Cross-Site Request Forgery',
                'description' => 'Acciones no autorizadas sin tokens de validación',
                'severity' => 'medium',
                'category' => 'authentication'
            ],
            [
                'id' => 'file_upload',
                'name' => 'Unrestricted File Upload',
                'description' => 'Subida de archivos sin validación de tipo',
                'severity' => 'critical',
                'category' => 'upload'
            ],
            [
                'id' => 'lfi',
                'name' => 'Local File Inclusion',
                'description' => 'Lectura de archivos locales del servidor',
                'severity' => 'high',
                'category' => 'path-traversal'
            ],
            [
                'id' => 'command_injection',
                'name' => 'Command Injection',
                'description' => 'Ejecución de comandos del sistema operativo',
                'severity' => 'critical',
                'category' => 'injection'
            ]
        ];
        
        respond(true, 'Lista de vulnerabilidades disponibles', [
            'vulnerabilities' => $vulnerabilities,
            'total' => count($vulnerabilities)
        ]);
        break;
    
    // ========================================
    // ERROR - ACCIÓN NO ENCONTRADA
    // ========================================
    
    default:
        respond(false, 'Acción no válida o no encontrada', [
            'action_received' => $action,
            'available_actions' => [
                'auth' => ['register', 'login', 'logout', 'check', 'me'],
                'workspace' => ['create', 'list', 'get', 'update', 'delete', 'start', 'stop', 'reset'],
                'stats' => ['user', 'platform'],
                'util' => ['verify_turnstile', 'health'],
                'vulnerabilities' => ['list']
            ]
        ], 404);
        break;
}

// Función auxiliar para eliminar directorios
function deleteDirectory($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return unlink($dir);
    
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
    }
    
    return rmdir($dir);
}
?>