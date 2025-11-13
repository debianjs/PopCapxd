<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$workspaceId = $_GET['id'] ?? '';
if (empty($workspaceId)) {
    header('Location: flash.php');
    exit;
}

$data = readJSON(WORKSPACES_FILE);
$workspace = null;

foreach ($data['workspaces'] as $w) {
    if ($w['id'] === $workspaceId && $w['user_id'] === $_SESSION['user_id']) {
        $workspace = $w;
        break;
    }
}

if (!$workspace) {
    header('Location: flash.php');
    exit;
}

if ($workspace['status'] !== 'running') {
    header('Location: flash.php');
    exit;
}

$user = getUserById($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($workspace['name']); ?> - MicroHack</title>
    <meta property="og:image" content="https://rogddqelmxyuvhpjvxbf.supabase.co/storage/v1/object/public/files/j26axlgbc2f.png">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" type="image/svg+xml" 
href='data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-biohazard-icon lucide-biohazard"><circle cx="12" cy="11.9" r="2"/><path d="M6.7 3.4c-.9 2.5 0 5.2 2.2 6.7C6.5 9 3.7 9.6 2 11.6"/><path d="m8.9 10.1 1.4.8"/><path d="M17.3 3.4c.9 2.5 0 5.2-2.2 6.7 2.4-1.2 5.2-.6 6.9 1.5"/><path d="m15.1 10.1-1.4.8"/><path d="M16.7 20.8c-2.6-.4-4.6-2.6-4.7-5.3-.2 2.6-2.1 4.8-4.7 5.2"/><path d="M12 13.9v1.6"/><path d="M13.5 5.4c-1-.2-2-.2-3 0"/><path d="M17 16.4c.7-.7 1.2-1.6 1.5-2.5"/><path d="M5.5 13.9c.3.9.8 1.8 1.5 2.5"/></svg>'>
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
            <a href="create-workspace.php" class="nav-item">
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
                <h1 class="dashboard-title"><?php echo htmlspecialchars($workspace['name']); ?></h1>
                <p class="dashboard-subtitle">
                    <span class="status-badge status-running">
                        <i data-lucide="play"></i>
                        Activo
                    </span>
                </p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="stopWorkspace('<?php echo $workspace['id']; ?>')">
                    <i data-lucide="stop-circle"></i>
                    Detener
                </button>
                <a href="flash.php" class="btn btn-secondary">
                    <i data-lucide="arrow-left"></i>
                    Volver
                </a>
            </div>
        </div>

        <!-- Workspace Info -->
        <div class="workspace-details">
            <div class="detail-card">
                <h3 class="detail-title">
                    <i data-lucide="info"></i>
                    Información del Workspace
                </h3>
                <div class="detail-content">
                    <div class="detail-item">
                        <span class="detail-label">ID:</span>
                        <span class="detail-value"><code><?php echo $workspace['id']; ?></code></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Descripción:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($workspace['description'] ?: 'Sin descripción'); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Nivel de Seguridad:</span>
                        <span class="detail-value"><span class="badge badge-<?php echo $workspace['security_level']; ?>"><?php echo ucfirst($workspace['security_level']); ?></span></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Creado:</span>
                        <span class="detail-value"><?php echo date('d/m/Y H:i', strtotime($workspace['created_at'])); ?></span>
                    </div>
                </div>
            </div>

            <div class="detail-card">
                <h3 class="detail-title">
                    <i data-lucide="bug"></i>
                    Vulnerabilidades Activas
                </h3>
                <div class="vulnerabilities-list">
                    <?php if (empty($workspace['vulnerabilities'])): ?>
                        <p class="text-muted">No hay vulnerabilidades configuradas</p>
                    <?php else: ?>
                        <?php 
                        $vulnNames = [
                            'sql_injection' => 'SQL Injection',
                            'xss' => 'Cross-Site Scripting (XSS)',
                            'csrf' => 'Cross-Site Request Forgery',
                            'file_upload' => 'File Upload',
                            'lfi' => 'Local File Inclusion',
                            'command_injection' => 'Command Injection'
                        ];
                        foreach ($workspace['vulnerabilities'] as $vuln): 
                        ?>
                            <div class="vulnerability-item">
                                <i data-lucide="alert-triangle"></i>
                                <span><?php echo $vulnNames[$vuln] ?? $vuln; ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Target Site -->
        <div class="target-site">
            <h3 class="detail-title">
                <i data-lucide="target"></i>
                Sitio Objetivo
            </h3>
            <p class="target-description">Este es el sitio web vulnerable que debes hackear. Todos los datos están disponibles para tu práctica.</p>
            
            <div class="target-url">
                <i data-lucide="external-link"></i>
                <code>http://localhost/microhack/vulnerable-sites/<?php echo $workspace['id']; ?>/</code>
                <button class="btn btn-sm btn-primary" onclick="copyToClipboard('http://localhost/microhack/vulnerable-sites/<?php echo $workspace['id']; ?>/')">
                    <i data-lucide="copy"></i>
                </button>
            </div>

            <div class="target-credentials">
                <h4>Credenciales de Acceso:</h4>
                <div class="credentials-grid">
                    <div class="credential-item">
                        <span class="credential-label">Usuario Admin:</span>
                        <code>admin</code>
                    </div>
                    <div class="credential-item">
                        <span class="credential-label">Password Admin:</span>
                        <code>admin123</code>
                    </div>
                    <div class="credential-item">
                        <span class="credential-label">Base de Datos:</span>
                        <code>workspace_<?php echo $workspace['id']; ?></code>
                    </div>
                    <div class="credential-item">
                        <span class="credential-label">Puerto:</span>
                        <code>3306</code>
                    </div>
                </div>
            </div>

            <div class="target-actions">
                <a href="../vulnerable-sites/<?php echo $workspace['id']; ?>/" target="_blank" class="btn btn-primary">
                    <i data-lucide="external-link"></i>
                    Abrir Sitio Vulnerable
                </a>
                <button class="btn btn-secondary" onclick="resetWorkspace('<?php echo $workspace['id']; ?>')">
                    <i data-lucide="rotate-ccw"></i>
                    Resetear Workspace
                </button>
            </div>
        </div>

        <!-- Hacking Tools -->
        <div class="hacking-tools">
            <h3 class="detail-title">
                <i data-lucide="wrench"></i>
                Herramientas Recomendadas
            </h3>
            <div class="tools-grid">
                <div class="tool-card">
                    <i data-lucide="terminal"></i>
                    <h4>Burp Suite</h4>
                    <p>Interceptor de peticiones HTTP</p>
                </div>
                <div class="tool-card">
                    <i data-lucide="search"></i>
                    <h4>SQLMap</h4>
                    <p>Herramienta de SQL Injection</p>
                </div>
                <div class="tool-card">
                    <i data-lucide="code"></i>
                    <h4>OWASP ZAP</h4>
                    <p>Escáner de vulnerabilidades</p>
                </div>
                <div class="tool-card">
                    <i data-lucide="shield"></i>
                    <h4>Nikto</h4>
                    <p>Escáner de servidores web</p>
                </div>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="activity-log">
            <h3 class="detail-title">
                <i data-lucide="activity"></i>
                Registro de Actividad
            </h3>
            <div class="log-container">
                <div class="log-entry">
                    <span class="log-time"><?php echo date('H:i:s'); ?></span>
                    <span class="log-message">
                        <i data-lucide="play-circle"></i>
                        Workspace iniciado
                    </span>
                </div>
                <div class="log-entry">
                    <span class="log-time"><?php echo date('H:i:s', strtotime('-5 minutes')); ?></span>
                    <span class="log-message">
                        <i data-lucide="check-circle"></i>
                        Sitio vulnerable generado exitosamente
                    </span>
                </div>
                <div class="log-entry">
                    <span class="log-time"><?php echo date('H:i:s', strtotime('-10 minutes')); ?></span>
                    <span class="log-message">
                        <i data-lucide="database"></i>
                        Base de datos creada
                    </span>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/dashboard.js"></script>
    <script>
        lucide.createIcons();

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('URL copiada al portapapeles!');
            });
        }

        function resetWorkspace(id) {
            if (confirm('¿Estás seguro de que quieres resetear este workspace? Se restaurarán todos los archivos y la base de datos.')) {
                fetch('../api/workspace-actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'reset',
                        workspace_id: id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Workspace reseteado exitosamente');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }

        function stopWorkspace(id) {
            if (confirm('¿Detener este workspace?')) {
                fetch('../api/workspace-actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'stop',
                        workspace_id: id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'flash.php';
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }
    </script>
    <script src="../GeminiStack.js"></script>
</body>
</html>