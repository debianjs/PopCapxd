<?php
session_start();
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Documentación completa de la API REST de MicroHack">
    <meta name="keywords" content="API, REST, documentación, MicroHack, endpoints">
    
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Documentación API - MicroHack">
    <meta property="og:description" content="Documentación completa de la API REST de MicroHack">
    <meta property="og:image" content="https://rogddqelmxyuvhpjvxbf.supabase.co/storage/v1/object/public/files/j26axlgbc2f.png">
    
    <title>Documentación API - MicroHack</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href='data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-biohazard-icon lucide-biohazard"><circle cx="12" cy="11.9" r="2"/><path d="M6.7 3.4c-.9 2.5 0 5.2 2.2 6.7C6.5 9 3.7 9.6 2 11.6"/><path d="m8.9 10.1 1.4.8"/><path d="M17.3 3.4c.9 2.5 0 5.2-2.2 6.7 2.4-1.2 5.2-.6 6.9 1.5"/><path d="m15.1 10.1-1.4.8"/><path d="M16.7 20.8c-2.6-.4-4.6-2.6-4.7-5.3-.2 2.6-2.1 4.8-4.7 5.2"/><path d="M12 13.9v1.6"/><path d="M13.5 5.4c-1-.2-2-.2-3 0"/><path d="M17 16.4c.7-.7 1.2-1.6 1.5-2.5"/><path d="M5.5 13.9c.3.9.8 1.8 1.5 2.5"/></svg>'>
    
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.263.1/dist/umd/lucide.min.js"></script>
    
    <!-- Highlight.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    
    <!-- JetBrains Mono Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        .docs-page {
            background: #f8f9fa;
            min-height: 100vh;
            font-family: 'Product Sans', sans-serif;
        }
        
        .docs-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 3rem 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .docs-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .docs-title {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .docs-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
        }
        
        .docs-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .docs-sidebar {
            position: sticky;
            top: 2rem;
            height: fit-content;
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .sidebar-section {
            margin-bottom: 1.5rem;
        }
        
        .sidebar-title {
            font-weight: 600;
            font-size: 0.875rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.75rem;
        }
        
        .sidebar-links {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .sidebar-link {
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            color: #374151;
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .sidebar-link:hover {
            background: #f3f4f6;
            color: #10b981;
        }
        
        .sidebar-link.active {
            background: #d1fae5;
            color: #065f46;
            font-weight: 600;
        }
        
        .docs-content {
            background: white;
            border-radius: 1rem;
            padding: 2.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .section {
            margin-bottom: 3rem;
            scroll-margin-top: 2rem;
        }
        
        .section-title {
            font-size: 2rem;
            font-weight: bold;
            color: #111827;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .section-description {
            color: #6b7280;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .endpoint {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .endpoint-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        
        .endpoint-method {
            background: #10b981;
            color: white;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-weight: 600;
            font-size: 0.875rem;
            font-family: 'JetBrains Mono', monospace;
        }
        
        .endpoint-path {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            color: #374151;
            font-weight: 500;
        }
        
        .endpoint-title {
            font-weight: 600;
            font-size: 1.125rem;
            color: #111827;
            margin-bottom: 0.5rem;
        }
        
        .endpoint-description {
            color: #6b7280;
            margin-bottom: 1rem;
        }
        
        .param-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            font-size: 0.875rem;
        }
        
        .param-table th {
            background: #f3f4f6;
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .param-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .param-name {
            font-family: 'JetBrains Mono', monospace;
            color: #059669;
            font-weight: 500;
        }
        
        .param-type {
            font-family: 'JetBrains Mono', monospace;
            color: #6366f1;
            font-size: 0.8125rem;
        }
        
        .param-required {
            background: #fef3c7;
            color: #92400e;
            padding: 0.125rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .param-optional {
            background: #e0e7ff;
            color: #3730a3;
            padding: 0.125rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .code-block {
            margin: 1rem 0;
        }
        
        .code-header {
            background: #1f2937;
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem 0.5rem 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.875rem;
        }
        
        .code-language {
            color: #9ca3af;
        }
        
        .copy-btn {
            background: transparent;
            border: 1px solid #4b5563;
            color: #9ca3af;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            cursor: pointer;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }
        
        .copy-btn:hover {
            background: #374151;
            color: white;
            border-color: #6b7280;
        }
        
        .copy-btn.copied {
            background: #10b981;
            border-color: #10b981;
            color: white;
        }
        
        pre {
            margin: 0;
            padding: 1.25rem;
            background: #1f2937;
            border-radius: 0 0 0.5rem 0.5rem;
            overflow-x: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.875rem;
            line-height: 1.6;
        }
        
        pre code {
            font-family: 'JetBrains Mono', monospace;
        }
        
        code:not(pre code) {
            background: #f3f4f6;
            color: #059669;
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.875rem;
        }
        
        .tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .tab {
            padding: 0.75rem 1.5rem;
            background: transparent;
            border: none;
            cursor: pointer;
            font-weight: 500;
            color: #6b7280;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
            font-family: 'JetBrains Mono', monospace;
        }
        
        .tab:hover {
            color: #10b981;
        }
        
        .tab.active {
            color: #10b981;
            border-bottom-color: #10b981;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .response-example {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            margin: 1rem 0;
        }
        
        .response-title {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .status-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
        
        .status-200 { background: #d1fae5; color: #065f46; }
        .status-201 { background: #dbeafe; color: #1e40af; }
        .status-400 { background: #fee2e2; color: #991b1b; }
        .status-401 { background: #fef3c7; color: #92400e; }
        .status-404 { background: #e7e5e4; color: #44403c; }
        
        .alert-box {
            padding: 1rem;
            border-radius: 0.5rem;
            margin: 1rem 0;
            display: flex;
            gap: 0.75rem;
        }
        
        .alert-info {
            background: #dbeafe;
            border: 1px solid #93c5fd;
            color: #1e40af;
        }
        
        .alert-warning {
            background: #fef3c7;
            border: 1px solid #fde68a;
            color: #92400e;
        }
        
        .alert-success {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }
        
        @media (max-width: 1024px) {
            .docs-layout {
                grid-template-columns: 1fr;
            }
            
            .docs-sidebar {
                position: relative;
                top: 0;
            }
        }
        
        @media (max-width: 768px) {
            .docs-title {
                font-size: 2rem;
            }
            
            .docs-content {
                padding: 1.5rem;
            }
            
            .endpoint-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body class="docs-page">
    <div class="docs-header">
        <div class="docs-container">
            <h1 class="docs-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" x2="8" y1="13" y2="13"/>
                    <line x1="16" x2="8" y1="17" y2="17"/>
                    <line x1="10" x2="8" y1="9" y2="9"/>
                </svg>
                Documentación API
            </h1>
            <p class="docs-subtitle">Guía completa para integrar MicroHack en tus aplicaciones</p>
        </div>
    </div>

    <div class="docs-layout">
        <aside class="docs-sidebar">
            <div class="sidebar-section">
                <h3 class="sidebar-title">Introducción</h3>
                <div class="sidebar-links">
                    <a href="#getting-started" class="sidebar-link">
                        <i data-lucide="rocket" style="width: 16px;"></i>
                        Comenzar
                    </a>
                    <a href="#authentication" class="sidebar-link">
                        <i data-lucide="key" style="width: 16px;"></i>
                        Autenticación
                    </a>
                    <a href="#errors" class="sidebar-link">
                        <i data-lucide="alert-circle" style="width: 16px;"></i>
                        Errores
                    </a>
                </div>
            </div>

            <div class="sidebar-section">
                <h3 class="sidebar-title">Endpoints</h3>
                <div class="sidebar-links">
                    <a href="#auth-endpoints" class="sidebar-link">
                        <i data-lucide="shield" style="width: 16px;"></i>
                        Auth
                    </a>
                    <a href="#workspace-endpoints" class="sidebar-link">
                        <i data-lucide="box" style="width: 16px;"></i>
                        Workspaces
                    </a>
                    <a href="#stats-endpoints" class="sidebar-link">
                        <i data-lucide="bar-chart" style="width: 16px;"></i>
                        Estadísticas
                    </a>
                </div>
            </div>
        </aside>

        <main class="docs-content">
            <section id="getting-started" class="section">
                <h2 class="section-title">
                    <i data-lucide="rocket"></i>
                    Comenzar
                </h2>
                <p class="section-description">
                    La API de MicroHack es una API REST que permite gestionar workspaces, usuarios y vulnerabilidades de forma programática.
                </p>

                <div class="alert-box alert-info">
                    <i data-lucide="info"></i>
                    <div>
                        <strong>Base URL:</strong> <code><?php echo BASE_URL; ?>/api.php</code>
                    </div>
                </div>

                <h3 style="margin-top: 2rem; margin-bottom: 1rem; font-size: 1.25rem; font-weight: 600;">Estructura de Request</h3>
                <div class="code-block">
                    <div class="code-header">
                        <span class="code-language">JSON</span>
                        <button class="copy-btn" onclick="copyCode(this)">
                            <i data-lucide="copy" style="width: 14px;"></i>
                            Copiar
                        </button>
                    </div>
                    <pre><code>{
  "action": "nombre.accion",
  "params": {
    "parametro1": "valor1"
  }
}</code></pre>
                </div>
            </section>

            <section id="auth-endpoints" class="section">
                <h2 class="section-title">
                    <i data-lucide="shield"></i>
                    Autenticación
                </h2>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="endpoint-method">POST</span>
                        <span class="endpoint-path">/api.php</span>
                        <span class="badge badge-success">
                            <i data-lucide="unlock" style="width: 12px;"></i>
                            Público
                        </span>
                    </div>
                    <h3 class="endpoint-title">auth.register</h3>
                    <p class="endpoint-description">Registra un nuevo usuario.</p>

                    <h4 style="margin-top: 1.5rem; font-weight: 600;">Parámetros</h4>
                    <table class="param-table">
                        <thead>
                            <tr>
                                <th>Parámetro</th>
                                <th>Tipo</th>
                                <th>Requerido</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="param-name">username</span></td>
                                <td><span class="param-type">string</span></td>
                                <td><span class="param-required">Sí</span></td>
                                <td>Nombre de usuario (mín. 3 caracteres)</td>
                            </tr>
                            <tr>
                                <td><span class="param-name">password</span></td>
                                <td><span class="param-type">string</span></td>
                                <td><span class="param-required">Sí</span></td>
                                <td>Contraseña (mín. 6 caracteres)</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4 style="margin-top: 1.5rem; font-weight: 600;">Ejemplo</h4>
                    <div class="tabs">
                        <button class="tab active" onclick="switchTab(event, 'reg-curl')">cURL</button>
                        <button class="tab" onclick="switchTab(event, 'reg-js')">JavaScript</button>
                    </div>

                    <div id="reg-curl" class="tab-content active">
                        <div class="code-block">
                            <div class="code-header">
                                <span>Bash</span>
                                <button class="copy-btn" onclick="copyCode(this)">
                                    <i data-lucide="copy" style="width: 14px;"></i>
                                    Copiar
                                </button>
                            </div>
                            <pre><code>curl -X POST <?php echo BASE_URL; ?>/api.php \
  -H "Content-Type: application/json" \
  -d '{
    "action": "auth.register",
    "params": {
      "username": "nuevo_usuario",
      "password": "password123"
    }
  }'</code></pre>
                        </div>
                    </div>

                    <div id="reg-js" class="tab-content">
                        <div class="code-block">
                            <div class="code-header">
                                <span>JavaScript</span>
                                <button class="copy-btn" onclick="copyCode(this)">
                                    <i data-lucide="copy" style="width: 14px;"></i>
                                    Copiar
                                </button>
                            </div>
                            <pre><code>fetch('<?php echo BASE_URL; ?>/api.php', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    action: 'auth.register',
    params: {
      username: 'nuevo_usuario',
      password: 'password123'
    }
  })
}).then(r => r.json()).then(console.log);</code></pre>
                        </div>
                    </div>

                    <h4 style="margin-top: 1.5rem; font-weight: 600;">Respuesta</h4>
                    <div class="response-example">
                        <div class="response-title">
                            <span class="status-code status-201">201 Created</span>
                        </div>
                        <div class="code-block">
                            <div class="code-header">
                                <span>JSON</span>
                                <button class="copy-btn" onclick="copyCode(this)">
                                    <i data-lucide="copy" style="width: 14px;"></i>
                                    Copiar
                                </button>
                            </div>
                            <pre><code>{
  "success": true,
  "message": "Usuario registrado exitosamente",
  "data": {
    "id": "uuid-here",
    "username": "nuevo_usuario"
  },
  "timestamp": "2024-01-15 10:30:00"
}</code></pre>
                        </div>
                    </div>
                </div>
            </section>

            <section id="workspace-endpoints" class="section">
                <h2 class="section-title">
                    <i data-lucide="box"></i>
                    Workspaces
                </h2>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="endpoint-method">POST</span>
                        <span class="endpoint-path">/api.php</span>
                        <span class="badge badge-warning">
                            <i data-lucide="lock" style="width: 12px;"></i>
                            Requiere Auth
                        </span>
                    </div>
                    <h3 class="endpoint-title">workspace.create</h3>
                    <p class="endpoint-description">Crea un nuevo workspace vulnerable.</p>

                    <h4 style="margin-top: 1.5rem; font-weight: 600;">Ejemplo Completo</h4>
                    <div class="code-block">
                        <div class="code-header">
                            <span>Bash</span>
                            <button class="copy-btn" onclick="copyCode(this)">
                                <i data-lucide="copy" style="width: 14px;"></i>
                                Copiar
                            </button>
                        </div>
                        <pre><code>curl -X POST <?php echo BASE_URL; ?>/api.php \
  -b cookies.txt \
  -H "Content-Type: application/json" \
  -d '{
    "action": "workspace.create",
    "params": {
      "name": "Mi Workspace",
      "description": "Para práctica",
      "security_level": "low",
      "vulnerabilities": ["sql_injection", "xss"]
    }
  }'</code></pre>
                    </div>
                </div>
            </section>

            <div class="alert-box alert-success">
                <i data-lucide="check-circle"></i>
                <div>
                    <strong>¡Listo!</strong> Para ver todos los endpoints disponibles, consulta el archivo <code>api.php</code> o contacta con soporte.
                </div>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();
        hljs.highlightAll();

        function switchTab(event, tabId) {
            const button = event.currentTarget;
            const container = button.closest('.endpoint, .section');
            
            container.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            container.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            
            button.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        function copyCode(button) {
            const codeBlock = button.closest('.code-block').querySelector('code');
            const text = codeBlock.textContent;
            
            navigator.clipboard.writeText(text).then(() => {
                const icon = button.querySelector('i');
                button.classList.add('copied');
                button.innerHTML = '<i data-lucide="check" style="width: 14px;"></i> Copiado';
                lucide.createIcons();
                
                setTimeout(() => {
                    button.innerHTML = '<i data-lucide="copy" style="width: 14px;"></i> Copiar';
                    button.classList.remove('copied');
                    lucide.createIcons();
                }, 2000);
            });
        }

        // Smooth scroll
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>