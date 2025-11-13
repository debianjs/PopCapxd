<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Verificar si está logueado
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$user = getUserById($_SESSION['user_id']);
$workspaces = getUserWorkspaces($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MicroHack</title>
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
            <a href="flash.php" class="nav-item active">
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
                <h1 class="dashboard-title">Mis Workspaces</h1>
                <p class="dashboard-subtitle">Gestiona tus entornos de práctica</p>
            </div>
            <a href="create-workspace.php" class="btn btn-primary">
                <i data-lucide="plus"></i>
                Nuevo Workspace
            </a>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #10b981;">
                    <i data-lucide="box"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Total Workspaces</p>
                    <p class="stat-value"><?php echo count($workspaces); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #3b82f6;">
                    <i data-lucide="play-circle"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Activos</p>
                    <p class="stat-value"><?php echo count(array_filter($workspaces, fn($w) => $w['status'] === 'running')); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #f59e0b;">
                    <i data-lucide="pause-circle"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Detenidos</p>
                    <p class="stat-value"><?php echo count(array_filter($workspaces, fn($w) => $w['status'] === 'stopped')); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #ef4444;">
                    <i data-lucide="bug"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Vulnerabilidades</p>
                    <p class="stat-value"><?php echo array_sum(array_map(fn($w) => count($w['vulnerabilities'] ?? []), $workspaces)); ?></p>
                </div>
            </div>
        </div>

        <!-- Workspaces Grid -->
        <?php if (empty($workspaces)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i data-lucide="inbox"></i>
                </div>
                <h3>No tienes workspaces</h3>
                <p>Crea tu primer workspace para comenzar a practicar</p>
                <a href="create-workspace.php" class="btn btn-primary">
                    <i data-lucide="plus-circle"></i>
                    Crear Workspace
                </a>
            </div>
        <?php else: ?>
            <div class="workspaces-grid">
                <?php foreach ($workspaces as $workspace): ?>
                    <div class="workspace-card">
                        <div class="workspace-header">
                            <div class="workspace-status-badge status-<?php echo $workspace['status']; ?>">
                                <i data-lucide="<?php echo $workspace['status'] === 'running' ? 'play' : 'pause'; ?>"></i>
                                <?php echo $workspace['status'] === 'running' ? 'Activo' : 'Detenido'; ?>
                            </div>
                            <div class="workspace-actions">
                                <button class="btn-icon" onclick="editWorkspace('<?php echo $workspace['id']; ?>')">
                                    <i data-lucide="settings"></i>
                                </button>
                                <button class="btn-icon btn-danger" onclick="deleteWorkspace('<?php echo $workspace['id']; ?>')">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </div>
                        </div>

                        <div class="workspace-body">
                            <h3 class="workspace-name"><?php echo htmlspecialchars($workspace['name']); ?></h3>
                            <p class="workspace-description"><?php echo htmlspecialchars($workspace['description'] ?? 'Sin descripción'); ?></p>

                            <div class="workspace-info">
                                <div class="info-item">
                                    <i data-lucide="bug"></i>
                                    <span><?php echo count($workspace['vulnerabilities'] ?? []); ?> Vulnerabilidades</span>
                                </div>
                                <div class="info-item">
                                    <i data-lucide="shield"></i>
                                    <span>Nivel: <?php echo htmlspecialchars($workspace['security_level'] ?? 'Medio'); ?></span>
                                </div>
                                <div class="info-item">
                                    <i data-lucide="clock"></i>
                                    <span>Creado: <?php echo date('d/m/Y', strtotime($workspace['created_at'])); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="workspace-footer">
                            <?php if ($workspace['status'] === 'running'): ?>
                                <a href="workspace.php?id=<?php echo $workspace['id']; ?>" class="btn btn-primary btn-block">
                                    <i data-lucide="external-link"></i>
                                    Abrir Workspace
                                </a>
                                <button class="btn btn-secondary btn-block" onclick="stopWorkspace('<?php echo $workspace['id']; ?>')">
                                    <i data-lucide="stop-circle"></i>
                                    Detener
                                </button>
                            <?php else: ?>
                                <button class="btn btn-primary btn-block" onclick="startWorkspace('<?php echo $workspace['id']; ?>')">
                                    <i data-lucide="play-circle"></i>
                                    Iniciar
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <script src="../assets/js/dashboard.js"></script>
    <script src="../GeminiStack.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>