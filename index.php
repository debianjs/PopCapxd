<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Si ya está logueado, redirigir al dashboard
if (isLoggedIn()) {
    header('Location: dash/flash.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MicroHack - Plataforma de Práctica de Hacking</title>
    <meta property="og:image" content="https://rogddqelmxyuvhpjvxbf.supabase.co/storage/v1/object/public/files/j26axlgbc2f.png">
    <link rel="icon" type="image/svg+xml" 
href='data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-biohazard-icon lucide-biohazard"><circle cx="12" cy="11.9" r="2"/><path d="M6.7 3.4c-.9 2.5 0 5.2 2.2 6.7C6.5 9 3.7 9.6 2 11.6"/><path d="m8.9 10.1 1.4.8"/><path d="M17.3 3.4c.9 2.5 0 5.2-2.2 6.7 2.4-1.2 5.2-.6 6.9 1.5"/><path d="m15.1 10.1-1.4.8"/><path d="M16.7 20.8c-2.6-.4-4.6-2.6-4.7-5.3-.2 2.6-2.1 4.8-4.7 5.2"/><path d="M12 13.9v1.6"/><path d="M13.5 5.4c-1-.2-2-.2-3 0"/><path d="M17 16.4c.7-.7 1.2-1.6 1.5-2.5"/><path d="M5.5 13.9c.3.9.8 1.8 1.5 2.5"/></svg>'>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.263.1/dist/umd/lucide.min.js"></script>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <div class="logo-container">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="logo-icon">
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
                <div class="logo-text">
                    <h1 class="logo-title">MicroHack</h1>
                    <p class="logo-subtitle">By Soblend</p>
                </div>
            </div>
        </div>
        <div class="header-right">
            <img src="assets/images/digitalocean-badge.png" alt="Powered by DigitalOcean" class="do-badge">
            <a href="login.php" class="btn btn-secondary">Iniciar Sesión</a>
            <a href="register.php" class="btn btn-primary">Registrarse</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <h2 class="welcome-title">Bienvenido a <span class="pixel-text">MicroHack</span></h2>
        </section>

        <!-- Features Card -->
        <section class="features-section">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        <polyline points="7.5 4.21 12 6.81 16.5 4.21"/>
                        <polyline points="7.5 19.79 7.5 14.6 3 12"/>
                        <polyline points="21 12 16.5 14.6 16.5 19.79"/>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                        <line x1="12" x2="12" y1="22.08" y2="12"/>
                    </svg>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">Características del Workspace</h3>
                    <ul class="feature-list">
                        <li>
                            <i data-lucide="zap"></i>
                            <span>Poder total del workspace</span>
                        </li>
                        <li>
                            <i data-lucide="rotate-ccw"></i>
                            <span>Recuperación en minutos una vez hecho el ataque</span>
                        </li>
                        <li>
                            <i data-lucide="bug"></i>
                            <span>Configurar vulnerabilidades</span>
                        </li>
                        <li>
                            <i data-lucide="shield"></i>
                            <span>Establecer sensibilidad y seguridad</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Disclaimer Card -->
        <section class="disclaimer-section">
            <div class="disclaimer-card">
                <div class="disclaimer-text">
                    <h3 class="disclaimer-title">
                        <i data-lucide="alert-triangle"></i>
                        Aviso Legal
                    </h3>
                    <p>Esta página está desarrollada por <strong>Soblend Development Studio</strong>. No nos hacemos responsables de lo que suceda en esta página y no nos involucramos con su uso. No damos soporte a esta página.</p>
                    <p>En caso de que su espacio de trabajo sea malogrado, considere una reinstalación o eliminación inmediata del espacio de trabajo. Esto se hace debido a que personas con alto conocimiento logren acceder a su espacio de trabajo, puedan modificar su espacio y hacer cosas maliciosas.</p>
                    <div class="disclaimer-badges">
                        <span class="badge badge-warning">
                            <i data-lucide="shield-alert"></i>
                            Solo para práctica
                        </span>
                        <span class="badge badge-info">
                            <i data-lucide="code"></i>
                            Entorno controlado
                        </span>
                        <span class="badge badge-danger">
                            <i data-lucide="lock"></i>
                            Uso responsable
                        </span>
                    </div>
                </div>
                <div class="disclaimer-image">
                    <img src="assets/images/disclaimer-image.png" alt="Security Warning">
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="cta-content">
                <h3 class="cta-title">¿Listo para empezar?</h3>
                <p class="cta-description">Crea tu workspace y comienza a practicar tus habilidades de hacking ético en un entorno seguro y controlado.</p>
                <a href="register.php" class="btn btn-primary btn-lg">
                    <i data-lucide="rocket"></i>
                    Crear Workspace
                </a>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2024 MicroHack by Soblend Development Studio. Todos los derechos reservados.</p>
            <div class="footer-links">
                <a href="#"><i data-lucide="github"></i></a>
                <a href="#"><i data-lucide="twitter"></i></a>
                <a href="#"><i data-lucide="mail"></i></a>
            </div>
        </div>
    </footer>

    <script src="GeminiStack.js"></script>
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>