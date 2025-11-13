<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $security_level = $_POST['security_level'] ?? 'medium';
    $vulnerabilities = $_POST['vulnerabilities'] ?? [];

    if (empty($name)) {
        $error = 'El nombre del workspace es obligatorio.';
    } else {
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
        
        $success = 'Workspace creado exitosamente!';
        header('Refresh: 2; url=flash.php');
    }
}

$user = getUserById($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Workspace - MicroHack</title>
    <meta property="og:image" content="https://rogddqelmxyuvhpjvxbf.supabase.co/storage/v1/object/public/files/j26axlgbc2f.png">
    <link rel="icon" type="image/svg+xml" 
href='data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-biohazard-icon lucide-biohazard"><circle cx="12" cy="11.9" r="2"/><path d="M6.7 3.4c-.9 2.5 0 5.2 2.2 6.7C6.5 9 3.7 9.6 2 11.6"/><path d="m8.9 10.1 1.4.8"/><path d="M17.3 3.4c.9 2.5 0 5.2-2.2 6.7 2.4-1.2 5.2-.6 6.9 1.5"/><path d="m15.1 10.1-1.4.8"/><path d="M16.7 20.8c-2.6-.4-4.6-2.6-4.7-5.3-.2 2.6-2.1 4.8-4.7 5.2"/><path d="M12 13.9v1.6"/><path d="M13.5 5.4c-1-.2-2-.2-3 0"/><path d="M17 16.4c.7-.7 1.2-1.6 1.5-2.5"/><path d="M5.5 13.9c.3.9.8 1.8 1.5 2.5"/></svg>'>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.263.1/dist/umd/lucide.min.js"></script>
</head>
<body class="dashboard-page">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="11.9" r="2"/>
                <path d="M6.7 3.4c-.9 2.5 0 5.2 2.2 6.7C6.5 9 3.7 9.6 2 11.6"/>
                <path d="m8.9 10.1 1.4.8"/>
                <path d="M17.3 3.4c.9 2.5 0 5.2-2.2 6.7 2.4-1.2 5.2-.6 6.9 1.5"/>
                <path d="m15.1 10.1-1.4.8"/>
                <path d="M16.7 20.8c-2.6-.4-4.6-2.6-4.7-5.3-.2 2.6-2.1 4.8-4.7 5.2"/>
                <path d="M12 13.9v1.6"/>
                <path d="M13.5 5.4c-1-.2-2-.2-3 0"/>
                <path d="M17 16.4c.7-.7 1.2-1.6 1.5-2.5"/>
                <path d="M5.5 13.9c.3.9.8 1.8 1.5 2.5"/>
            </svg>
            <div>
                <h2 class="pixel-text">MicroHack</h2>
                <p class="sidebar-subtitle">By Soblend</p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="flash.php" class="nav-item">
                <i data-lucide="layout-dashboard"></i>
                <span>Dashboard</span>
            </a>
            <a href="create-workspace.php" class="nav-item active">
                <i data-lucide="plus-circle"></i>
                <span>Crear Workspace</span>
            </a>
            <a href="#" class="nav-item">
                <i data-lucide="settings"></i>
                <span>Configuración</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <i data-lucide="user"></i>
                </div>
                <div class="user-details">
                    <p class="user-name"><?php echo htmlspecialchars($user['username']); ?></p>
                    <p class="user-role">Usuario</p>
                </div>
            </div>
            <a href="../api/auth.php?action=logout" class="btn btn-secondary btn-sm">
                <i data-lucide="log-out"></i>
                Salir
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-content">
        <div class="dashboard-header">
            <div>
                <h1 class="dashboard-title">Crear Nuevo Workspace</h1>
                <p class="dashboard-subtitle">Configura tu entorno de práctica</p>
            </div>
            <a href="flash.php" class="btn btn-secondary">
                <i data-lucide="arrow-left"></i>
                Volver
            </a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i data-lucide="alert-circle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i data-lucide="check-circle"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="workspace-form">
            <div class="form-section">
                <h3 class="form-section-title">
                    <i data-lucide="info"></i>
                    Información Básica
                </h3>

                <div class="form-group">
                    <label for="name" class="form-label">Nombre del Workspace *</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-input" 
                        placeholder="Ej: Mi Primer Workspace"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="form-input" 
                        rows="4"
                        placeholder="Describe tu workspace..."
                    ></textarea>
                </div>
            </div>

            <div class="form-section">
                <h3 class="form-section-title">
                    <i data-lucide="shield"></i>
                    Configuración de Seguridad
                </h3>

                <div class="form-group">
                    <label for="security_level" class="form-label">Nivel de Seguridad</label>
                    <select id="security_level" name="security_level" class="form-input">
                        <option value="low">Bajo - Más vulnerabilidades</option>
                        <option value="medium" selected>Medio - Balance</option>
                        <option value="high">Alto - Menos vulnerabilidades</option>
                    </select>
                </div>
            </div>

            <div class="form-section">
                <h3 class="form-section-title">
                    <i data-lucide="bug"></i>
                    Vulnerabilidades
                </h3>
                <p class="form-section-description">Selecciona las vulnerabilidades que incluirá tu workspace</p>

                <div class="vulnerabilities-grid">
                    <label class="vulnerability-card">
                        <input type="checkbox" name="vulnerabilities[]" value="sql_injection">
                        <div class="vulnerability-content">
                            <i data-lucide="database"></i>
                            <h4>SQL Injection</h4>
                            <p>Inyección de código SQL en formularios</p>
                        </div>
                    </label>

                    <label class="vulnerability-card">
                        <input type="checkbox" name="vulnerabilities[]" value="xss">
                        <div class="vulnerability-content">
                            <i data-lucide="code"></i>
                            <h4>XSS</h4>
                            <p>Cross-Site Scripting en inputs</p>
                        </div>
                    </label>

                    <label class="vulnerability-card">
                        <input type="checkbox" name="vulnerabilities[]" value="csrf">
                        <div class="vulnerability-content">
                            <i data-lucide="shield-off"></i>
                            <h4>CSRF</h4>
                            <p>Cross-Site Request Forgery</p>
                        </div>
                    </label>

                    <label class="vulnerability-card">
                        <input type="checkbox" name="vulnerabilities[]" value="file_upload">
                        <div class="vulnerability-content">
                            <i data-lucide="upload"></i>
                            <h4>File Upload</h4>
                            <p>Subida de archivos sin validación</p>
                        </div>
                    </label>

                    <label class="vulnerability-card">
                        <input type="checkbox" name="vulnerabilities[]" value="lfi">
                        <div class="vulnerability-content">
                            <i data-lucide="file-text"></i>
                            <h4>LFI</h4>
                            <p>Local File Inclusion</p>
                        </div>
                    </label>

                    <label class="vulnerability-card">
                        <input type="checkbox" name="vulnerabilities[]" value="command_injection">
                        <div class="vulnerability-content">
                            <i data-lucide="terminal"></i>
                            <h4>Command Injection</h4>
                            <p>Ejecución de comandos del sistema</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <a href="flash.php" class="btn btn-secondary">
                    <i data-lucide="x"></i>
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="check"></i>
                    Crear Workspace
                </button>
            </div>
        </form>
    </main>

    <script src="../GeminiStack.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>