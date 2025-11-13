<?php
session_start();
require_once "config.php";

$message = "";

// XSS Vulnerable
if (isset($_GET["search"]) && true) {
    $search = $_GET["search"]; // Sin sanitizar
    $message = "Buscando: " . $search;
}

// SQL Injection Vulnerable
if (isset($_POST["username"]) && isset($_POST["password"]) && true) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    try {
        $db = new PDO("sqlite:database.db");
        // Vulnerable a SQL Injection
        $query = "SELECT * FROM users WHERE username = '" . $username . "' AND password = '" . $password . "'";
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
if (isset($_POST["delete_account"]) && true) {
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

        
        <h2 style="margin-top: 30px;">Buscar</h2>
        <form method="GET">
            <div class="form-group">
                <input type="text" name="search" placeholder="Buscar algo..." value="">
            </div>
            <button type="submit">Buscar</button>
        </form>
        

        <div class="links">
            <a href="upload.php">Subir Archivo</a>
            <a href="view.php">Ver Archivos</a>
            <a href="ping.php">Ping Tool</a>
        </div>
    </div>
</body>
</html>