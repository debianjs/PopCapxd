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
$workspaceId = $input['workspace_id'] ?? '';

if (empty($workspaceId)) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de workspace requerido'
    ]);
    exit;
}

$data = readJSON(WORKSPACES_FILE);
$workspaceIndex = -1;
$workspace = null;

foreach ($data['workspaces'] as $index => $w) {
    if ($w['id'] === $workspaceId && $w['user_id'] === $_SESSION['user_id']) {
        $workspaceIndex = $index;
        $workspace = $w;
        break;
    }
}

if (!$workspace) {
    echo json_encode([
        'success' => false,
        'message' => 'Workspace no encontrado'
    ]);
    exit;
}

switch ($action) {
    case 'start':
        // Iniciar workspace
        $data['workspaces'][$workspaceIndex]['status'] = 'running';
        $data['workspaces'][$workspaceIndex]['started_at'] = date('Y-m-d H:i:s');
        
        // Crear carpeta del workspace si no existe
        $workspacePath = BASE_PATH . '/vulnerable-sites/' . $workspaceId;
        if (!file_exists($workspacePath)) {
            mkdir($workspacePath, 0755, true);
            
            // Generar sitio vulnerable
            createVulnerableSite($workspaceId, $workspace['vulnerabilities']);
        }
        
        writeJSON(WORKSPACES_FILE, $data);
        
        echo json_encode([
            'success' => true,
            'message' => 'Workspace iniciado correctamente',
            'workspace' => $data['workspaces'][$workspaceIndex]
        ]);
        break;

    case 'stop':
        // Detener workspace
        $data['workspaces'][$workspaceIndex]['status'] = 'stopped';
        $data['workspaces'][$workspaceIndex]['stopped_at'] = date('Y-m-d H:i:s');
        
        writeJSON(WORKSPACES_FILE, $data);
        
        echo json_encode([
            'success' => true,
            'message' => 'Workspace detenido correctamente'
        ]);
        break;

    case 'delete':
        // Eliminar workspace
        $workspacePath = BASE_PATH . '/vulnerable-sites/' . $workspaceId;
        if (file_exists($workspacePath)) {
            deleteDirectory($workspacePath);
        }
        
        array_splice($data['workspaces'], $workspaceIndex, 1);
        writeJSON(WORKSPACES_FILE, $data);
        
        echo json_encode([
            'success' => true,
            'message' => 'Workspace eliminado correctamente'
        ]);
        break;

    case 'reset':
        // Resetear workspace
        $workspacePath = BASE_PATH . '/vulnerable-sites/' . $workspaceId;
        if (file_exists($workspacePath)) {
            deleteDirectory($workspacePath);
            mkdir($workspacePath, 0755, true);
            createVulnerableSite($workspaceId, $workspace['vulnerabilities']);
        }
        
        $data['workspaces'][$workspaceIndex]['reset_at'] = date('Y-m-d H:i:s');
        writeJSON(WORKSPACES_FILE, $data);
        
        echo json_encode([
            'success' => true,
            'message' => 'Workspace reseteado correctamente'
        ]);
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Acción no válida'
        ]);
        break;
}

// Función para crear sitio vulnerable
function createVulnerableSite($workspaceId, $vulnerabilities) {
    $path = BASE_PATH . '/vulnerable-sites/' . $workspaceId;
    
    // Crear index.php vulnerable
    $indexContent = generateVulnerableIndex($vulnerabilities);
    file_put_contents($path . '/index.php', $indexContent);
    
    // Crear archivo de configuración
    $configContent = generateConfigFile($workspaceId);
    file_put_contents($path . '/config.php', $configContent);
    
    // Crear base de datos SQLite si tiene SQL Injection
    if (in_array('sql_injection', $vulnerabilities)) {
        createVulnerableDatabase($path, $workspaceId);
    }
    
    // Crear archivos adicionales según vulnerabilidades
    if (in_array('file_upload', $vulnerabilities)) {
        mkdir($path . '/uploads', 0777, true);
        $uploadContent = generateUploadPage();
        file_put_contents($path . '/upload.php', $uploadContent);
    }
    
    if (in_array('lfi', $vulnerabilities)) {
        $lfiContent = generateLFIPage();
        file_put_contents($path . '/view.php', $lfiContent);
    }
    
    if (in_array('command_injection', $vulnerabilities)) {
        $cmdContent = generateCommandInjectionPage();
        file_put_contents($path . '/ping.php', $cmdContent);
    }
}

// Generar index vulnerable
function generateVulnerableIndex($vulnerabilities) {
    $hasXSS = in_array('xss', $vulnerabilities);
    $hasSQL = in_array('sql_injection', $vulnerabilities);
    $hasCSRF = in_array('csrf', $vulnerabilities);
    
    return '<?php
session_start();
require_once "config.php";

$message = "";

// XSS Vulnerable
if (isset($_GET["search"]) && ' . ($hasXSS ? 'true' : 'false') . ') {
    $search = $_GET["search"]; // Sin sanitizar
    $message = "Buscando: " . $search;
}

// SQL Injection Vulnerable
if (isset($_POST["username"]) && isset($_POST["password"]) && ' . ($hasSQL ? 'true' : 'false') . ') {
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    try {
        $db = new PDO("sqlite:database.db");
        // Vulnerable a SQL Injection
        $query = "SELECT * FROM users WHERE username = \'" . $username . "\' AND password = \'" . $password . "\'";
        $result = $db->query($query);
        
        if ($result && $result->fetch()) {
            $_SESSION["logged_in"] = true;
            $message = "<div class=\"success\">Login exitoso! Flag: FLAG{SQL_INJECTION_SUCCESS}</div>";
        } else {
            $message = "<div class=\"error\">Credenciales incorrectas</div>";
        }
    } catch (Exception $e) {
        $message = "<div class=\"error\">Error: " . $e->getMessage() . "</div>";
    }
}

// CSRF Vulnerable
if (isset($_POST["delete_account"]) && ' . ($hasCSRF ? 'true' : 'false') . ') {
    // Sin token CSRF
    $message = "<div class=\"success\">Cuenta eliminada! Flag: FLAG{CSRF_SUCCESS}</div>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerable Site - Practice Hacking</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
        }
        h1 { color: #333; margin-bottom: 20px; text-align: center; }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #856404;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover { background: #5568d3; }
        .success {
            background: #d4edda;
            border: 1px solid #28a745;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #dc3545;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            color: #721c24;
        }
        .links {
            margin-top: 20px;
            text-align: center;
        }
        .links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 10px;
        }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 Vulnerable Practice Site</h1>
        
        <div class="warning">
            <strong>⚠️ Sitio Intencionalmente Vulnerable</strong><br>
            Este sitio contiene vulnerabilidades para práctica de hacking ético.
        </div>

        <?php if ($message): ?>
            <?php echo $message; ?>
        <?php endif; ?>

        <h2>Login</h2>
        <form method="POST">
            <div class="form-group">
                <label>Usuario:</label>
                <input type="text" name="username" placeholder="admin" required>
            </div>
            <div class="form-group">
                <label>Contraseña:</label>
                <input type="password" name="password" placeholder="password" required>
            </div>
            <button type="submit">Iniciar Sesión</button>
        </form>

        ' . ($hasXSS ? '
        <h2 style="margin-top: 30px;">Buscar</h2>
        <form method="GET">
            <div class="form-group">
                <input type="text" name="search" placeholder="Buscar algo..." value="' . ($_GET['search'] ?? '') . '">
            </div>
            <button type="submit">Buscar</button>
        </form>
        ' : '') . '

        <div class="links">
            ' . (in_array('file_upload', $vulnerabilities) ? '<a href="upload.php">Subir Archivo</a>' : '') . '
            ' . (in_array('lfi', $vulnerabilities) ? '<a href="view.php">Ver Archivos</a>' : '') . '
            ' . (in_array('command_injection', $vulnerabilities) ? '<a href="ping.php">Ping Tool</a>' : '') . '
        </div>
    </div>
</body>
</html>';
}

// Generar archivo config.php
function generateConfigFile($workspaceId) {
    return '<?php
// Configuración del Workspace
define("WORKSPACE_ID", "' . $workspaceId . '");
define("DB_FILE", "database.db");

// Credenciales (intencionalmente expuestas para práctica)
define("ADMIN_USER", "admin");
define("ADMIN_PASS", "admin123");
define("FLAG_ADMIN", "FLAG{ADMIN_ACCESS_GRANTED}");
?>';
}

// Crear base de datos vulnerable
function createVulnerableDatabase($path, $workspaceId) {
    $db = new PDO('sqlite:' . $path . '/database.db');
    
    // Crear tabla de usuarios
    $db->exec('CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY,
        username TEXT,
        password TEXT,
        email TEXT,
        is_admin INTEGER
    )');
    
    // Insertar datos de prueba
    $db->exec("INSERT INTO users VALUES 
        (1, 'admin', 'admin123', 'admin@vulnerable.local', 1),
        (2, 'user', 'password', 'user@vulnerable.local', 0),
        (3, 'test', 'test123', 'test@vulnerable.local', 0)
    ");
    
    // Crear tabla de flags
    $db->exec('CREATE TABLE IF NOT EXISTS flags (
        id INTEGER PRIMARY KEY,
        flag TEXT,
        description TEXT
    )');
    
    $db->exec("INSERT INTO flags VALUES 
        (1, 'FLAG{SQL_INJECTION_MASTER}', 'SQL Injection completado'),
        (2, 'FLAG{DATABASE_DUMP_SUCCESS}', 'Dump de base de datos exitoso'),
        (3, 'FLAG{ADMIN_CREDENTIALS_FOUND}', 'Credenciales de admin encontradas')
    ");
}

// Generar página de upload vulnerable
function generateUploadPage() {
    return '<?php
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    
    // Vulnerable: sin validación de tipo de archivo
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        $message = "<div class=\"success\">Archivo subido: " . htmlspecialchars(basename($_FILES["file"]["name"])) . "<br>Flag: FLAG{FILE_UPLOAD_SUCCESS}</div>";
    } else {
        $message = "<div class=\"error\">Error al subir archivo</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload File</title>
    <style>
        body { font-family: Arial; padding: 40px; background: #f0f0f0; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        input[type="file"] { margin: 20px 0; }
        button { padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .success { background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0; color: #155724; }
        .error { background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Subir Archivo</h1>
        <?php echo $message; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <br>
            <button type="submit">Subir</button>
        </form>
        <br>
        <a href="index.php">← Volver</a>
    </div>
</body>
</html>';
}

// Generar página LFI vulnerable
function generateLFIPage() {
    return '<?php
$file = $_GET["file"] ?? "index.php";

// Vulnerable a LFI
if (file_exists($file)) {
    echo "<h1>Contenido del archivo: " . htmlspecialchars($file) . "</h1>";
    echo "<pre>" . htmlspecialchars(file_get_contents($file)) . "</pre>";
    echo "<p>Flag: FLAG{LFI_SUCCESS}</p>";
} else {
    echo "Archivo no encontrado";
}
?>
<br><a href=\"index.php\">← Volver</a>';
}

// Generar página Command Injection vulnerable
function generateCommandInjectionPage() {
    return '<?php
$output = "";
if (isset($_POST["ip"])) {
    $ip = $_POST["ip"];
    // Vulnerable a Command Injection
    $output = shell_exec("ping -c 4 " . $ip);
    if (strpos($output, "FLAG") === false) {
        $output .= "\n\nFlag: FLAG{COMMAND_INJECTION_SUCCESS}";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ping Tool</title>
    <style>
        body { font-family: Arial; padding: 40px; background: #f0f0f0; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        input { padding: 10px; width: 300px; border: 1px solid #ddd; border-radius: 5px; }
        button { padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; }
        pre { background: #f8f8f8; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏓 Ping Tool</h1>
        <form method="POST">
            <input type="text" name="ip" placeholder="Ingresa IP o hostname" value="<?php echo htmlspecialchars($_POST[\'ip\'] ?? \'\'); ?>">
            <button type="submit">Ping</button>
        </form>
        <?php if ($output): ?>
            <h3>Resultado:</h3>
            <pre><?php echo htmlspecialchars($output); ?></pre>
        <?php endif; ?>
        <br>
        <a href="index.php">← Volver</a>
    </div>
</body>
</html>';
}

// Función para eliminar directorio recursivamente
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