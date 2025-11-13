<?php
// Configuración general
define('BASE_PATH', dirname(__DIR__));
define('DATA_PATH', BASE_PATH . '/data');
define('BASE_URL', 'https://microhack.wuaze.com'); // Cambiar según tu dominio

// Crear carpeta data si no existe
if (!file_exists(DATA_PATH)) {
    mkdir(DATA_PATH, 0755, true);
}

// Archivos JSON
define('USERS_FILE', DATA_PATH . '/users.json');
define('WORKSPACES_FILE', DATA_PATH . '/workspaces.json');
define('SESSIONS_FILE', DATA_PATH . '/sessions.json');

// Inicializar archivos JSON si no existen
if (!file_exists(USERS_FILE)) {
    file_put_contents(USERS_FILE, json_encode(['users' => []], JSON_PRETTY_PRINT));
}

if (!file_exists(WORKSPACES_FILE)) {
    file_put_contents(WORKSPACES_FILE, json_encode(['workspaces' => []], JSON_PRETTY_PRINT));
}

if (!file_exists(SESSIONS_FILE)) {
    file_put_contents(SESSIONS_FILE, json_encode(['sessions' => []], JSON_PRETTY_PRINT));
}

// Timezone
date_default_timezone_set('America/Lima');

// ========================================
// OAuth Configuration
// ========================================

// GitHub OAuth
define('GITHUB_CLIENT_ID', 'Ov23liYkSM0j5uNlnD1X');
define('GITHUB_CLIENT_SECRET', 'a3f9ebe1e9816b8d132de0674a48dca6f018ceed');
define('GITHUB_REDIRECT_URI', BASE_URL . '/oauth/github-callback.php');

// Google OAuth
define('GOOGLE_CLIENT_ID', '84085481564-p4pgb10t0q10roe54j4uk2814v3ovuiv.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', ''); // Agregar si lo tienes
define('GOOGLE_REDIRECT_URI', BASE_URL . '/oauth/google-callback.php');

// Cloudflare Turnstile
define('TURNSTILE_SITE_KEY', '0x4AAAAAACAbE_P3vihn0rx3');
define('TURNSTILE_SECRET_KEY', '0x4AAAAAACAbExXqZtWUr0CB_FphZBQTrmE');

// OAuth URLs
define('GITHUB_AUTH_URL', 'https://github.com/login/oauth/authorize');
define('GITHUB_TOKEN_URL', 'https://github.com/login/oauth/access_token');
define('GITHUB_API_URL', 'https://api.github.com/user');

define('GOOGLE_AUTH_URL', 'https://accounts.google.com/o/oauth2/v2/auth');
define('GOOGLE_TOKEN_URL', 'https://oauth2.googleapis.com/token');
define('GOOGLE_API_URL', 'https://www.googleapis.com/oauth2/v2/userinfo');
?>