<?php
// Función para generar UUID
function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

// Leer archivo JSON
function readJSON($file) {
    if (!file_exists($file)) {
        return [];
    }
    $content = file_get_contents($file);
    return json_decode($content, true) ?: [];
}

// Escribir archivo JSON
function writeJSON($file, $data) {
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

// Verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Registrar usuario
function registerUser($username, $password) {
    $data = readJSON(USERS_FILE);
    
    // Verificar si el usuario ya existe
    foreach ($data['users'] as $user) {
        if (strtolower($user['username']) === strtolower($username)) {
            return ['success' => false, 'message' => 'El nombre de usuario ya está en uso.'];
        }
    }
    
    // Crear nuevo usuario
    $newUser = [
        'id' => generateUUID(),
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $data['users'][] = $newUser;
    writeJSON(USERS_FILE, $data);
    
    return [
        'success' => true,
        'user' => [
            'id' => $newUser['id'],
            'username' => $newUser['username']
        ]
    ];
}

// Login usuario
function loginUser($username, $password) {
    $data = readJSON(USERS_FILE);
    
    foreach ($data['users'] as $user) {
        if (strtolower($user['username']) === strtolower($username)) {
            if (password_verify($password, $user['password'])) {
                return [
                    'success' => true,
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username']
                    ]
                ];
            } else {
                return ['success' => false, 'message' => 'Contraseña incorrecta.'];
            }
        }
    }
    
    return ['success' => false, 'message' => 'Usuario no encontrado.'];
}

// Obtener usuario por ID
function getUserById($userId) {
    $data = readJSON(USERS_FILE);
    
    foreach ($data['users'] as $user) {
        if ($user['id'] === $userId) {
            return $user;
        }
    }
    
    return null;
}

// Obtener workspaces del usuario
function getUserWorkspaces($userId) {
    $data = readJSON(WORKSPACES_FILE);
    $userWorkspaces = [];
    
    foreach ($data['workspaces'] as $workspace) {
        if ($workspace['user_id'] === $userId) {
            $userWorkspaces[] = $workspace;
        }
    }
    
    return $userWorkspaces;
}

// ========================================
// OAuth Functions
// ========================================

// Verificar Cloudflare Turnstile
function verifyTurnstile($token, $userIP = null) {
    if (empty($token)) {
        return ['success' => false, 'message' => 'Token de Turnstile requerido'];
    }
    
    $data = [
        'secret' => TURNSTILE_SECRET_KEY,
        'response' => $token,
    ];
    
    if ($userIP) {
        $data['remoteip'] = $userIP;
    }
    
    $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if ($result && isset($result['success']) && $result['success'] === true) {
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Verificación de Turnstile fallida'];
}

// GitHub OAuth - Obtener usuario
function getGitHubUser($code) {
    // Intercambiar código por token
    $tokenData = [
        'client_id' => GITHUB_CLIENT_ID,
        'client_secret' => GITHUB_CLIENT_SECRET,
        'code' => $code,
        'redirect_uri' => GITHUB_REDIRECT_URI
    ];
    
    $ch = curl_init(GITHUB_TOKEN_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    
    $tokenResponse = curl_exec($ch);
    curl_close($ch);
    
    $tokenResult = json_decode($tokenResponse, true);
    
    if (!isset($tokenResult['access_token'])) {
        return ['success' => false, 'message' => 'Error al obtener token de GitHub'];
    }
    
    $accessToken = $tokenResult['access_token'];
    
    // Obtener datos del usuario
    $ch = curl_init(GITHUB_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'User-Agent: MicroHack-App'
    ]);
    
    $userResponse = curl_exec($ch);
    curl_close($ch);
    
    $userData = json_decode($userResponse, true);
    
    if (!isset($userData['id'])) {
        return ['success' => false, 'message' => 'Error al obtener datos de usuario'];
    }
    
    return [
        'success' => true,
        'user' => [
            'oauth_provider' => 'github',
            'oauth_id' => $userData['id'],
            'username' => $userData['login'],
            'email' => $userData['email'] ?? '',
            'avatar' => $userData['avatar_url'] ?? '',
            'name' => $userData['name'] ?? $userData['login']
        ]
    ];
}

// Google OAuth - Obtener usuario
function getGoogleUser($code) {
    // Intercambiar código por token
    $tokenData = [
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'code' => $code,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'grant_type' => 'authorization_code'
    ];
    
    $ch = curl_init(GOOGLE_TOKEN_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $tokenResponse = curl_exec($ch);
    curl_close($ch);
    
    $tokenResult = json_decode($tokenResponse, true);
    
    if (!isset($tokenResult['access_token'])) {
        return ['success' => false, 'message' => 'Error al obtener token de Google'];
    }
    
    $accessToken = $tokenResult['access_token'];
    
    // Obtener datos del usuario
    $ch = curl_init(GOOGLE_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken
    ]);
    
    $userResponse = curl_exec($ch);
    curl_close($ch);
    
    $userData = json_decode($userResponse, true);
    
    if (!isset($userData['id'])) {
        return ['success' => false, 'message' => 'Error al obtener datos de usuario'];
    }
    
    return [
        'success' => true,
        'user' => [
            'oauth_provider' => 'google',
            'oauth_id' => $userData['id'],
            'username' => explode('@', $userData['email'])[0],
            'email' => $userData['email'],
            'avatar' => $userData['picture'] ?? '',
            'name' => $userData['name'] ?? $userData['email']
        ]
    ];
}

// Crear o actualizar usuario OAuth
function createOrUpdateOAuthUser($oauthData) {
    $data = readJSON(USERS_FILE);
    
    // Buscar si el usuario ya existe
    foreach ($data['users'] as $index => $user) {
        if (isset($user['oauth_provider']) && 
            $user['oauth_provider'] === $oauthData['oauth_provider'] &&
            $user['oauth_id'] === $oauthData['oauth_id']) {
            
            // Usuario existente - actualizar datos
            $data['users'][$index]['email'] = $oauthData['email'];
            $data['users'][$index]['avatar'] = $oauthData['avatar'];
            $data['users'][$index]['name'] = $oauthData['name'];
            $data['users'][$index]['last_login'] = date('Y-m-d H:i:s');
            
            writeJSON(USERS_FILE, $data);
            
            return [
                'success' => true,
                'user' => $data['users'][$index]
            ];
        }
    }
    
    // Nuevo usuario
    $newUser = [
        'id' => generateUUID(),
        'username' => $oauthData['username'],
        'email' => $oauthData['email'],
        'oauth_provider' => $oauthData['oauth_provider'],
        'oauth_id' => $oauthData['oauth_id'],
        'avatar' => $oauthData['avatar'],
        'name' => $oauthData['name'],
        'created_at' => date('Y-m-d H:i:s'),
        'last_login' => date('Y-m-d H:i:s')
    ];
    
    $data['users'][] = $newUser;
    writeJSON(USERS_FILE, $data);
    
    return [
        'success' => true,
        'user' => $newUser
    ];
}
?>