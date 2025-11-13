<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Si ya está logueado, redirigir al dashboard
if (isLoggedIn()) {
    header('Location: dash/flash.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $turnstileToken = $_POST['cf-turnstile-response'] ?? '';

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'Por favor, completa todos los campos.';
    } elseif (empty($turnstileToken)) {
        $error = 'Por favor, completa la verificación de seguridad.';
    } elseif (strlen($username) < 3) {
        $error = 'El nombre de usuario debe tener al menos 3 caracteres.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = 'El usuario solo puede contener letras, números y guiones bajos.';
    } else {
        // Verificar Turnstile
        $turnstileResult = verifyTurnstile($turnstileToken, $_SERVER['REMOTE_ADDR']);
        
        if (!$turnstileResult['success']) {
            $error = 'Verificación de seguridad fallida. Intenta de nuevo.';
        } else {
            // Registrar usuario
            $result = registerUser($username, $password);
            if ($result['success']) {
                $success = 'Cuenta creada exitosamente. Redirigiendo...';
                $_SESSION['user_id'] = $result['user']['id'];
                $_SESSION['username'] = $result['user']['username'];
                header('Refresh: 2; url=dash/flash.php');
            } else {
                $error = $result['message'];
            }
        }
    }
}

// Manejo de errores de OAuth
if (isset($_GET['error'])) {
    $errorMessages = [
        'github_auth_failed' => 'Error en la autenticación con GitHub',
        'google_auth_failed' => 'Error en la autenticación con Google',
        'user_creation_failed' => 'Error al crear el usuario'
    ];
    $error = $errorMessages[$_GET['error']] ?? 'Error desconocido';
}

// URLs de OAuth
$githubAuthUrl = GITHUB_AUTH_URL . '?' . http_build_query([
    'client_id' => GITHUB_CLIENT_ID,
    'redirect_uri' => GITHUB_REDIRECT_URI,
    'scope' => 'user:email'
]);

$googleAuthUrl = GOOGLE_AUTH_URL . '?' . http_build_query([
    'client_id' => GOOGLE_CLIENT_ID,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope' => 'email profile',
    'access_type' => 'online'
]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - MicroHack</title>
    <meta property="og:image" content="https://rogddqelmxyuvhpjvxbf.supabase.co/storage/v1/object/public/files/j26axlgbc2f.png">
    <link rel="icon" type="image/svg+xml" 
href='data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-biohazard-icon lucide-biohazard"><circle cx="12" cy="11.9" r="2"/><path d="M6.7 3.4c-.9 2.5 0 5.2 2.2 6.7C6.5 9 3.7 9.6 2 11.6"/><path d="m8.9 10.1 1.4.8"/><path d="M17.3 3.4c.9 2.5 0 5.2-2.2 6.7 2.4-1.2 5.2-.6 6.9 1.5"/><path d="m15.1 10.1-1.4.8"/><path d="M16.7 20.8c-2.6-.4-4.6-2.6-4.7-5.3-.2 2.6-2.1 4.8-4.7 5.2"/><path d="M12 13.9v1.6"/><path d="M13.5 5.4c-1-.2-2-.2-3 0"/><path d="M17 16.4c.7-.7 1.2-1.6 1.5-2.5"/><path d="M5.5 13.9c.3.9.8 1.8 1.5 2.5"/></svg>'>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.263.1/dist/umd/lucide.min.js"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body class="auth-page">
    <div class="auth-container">
        <!-- Logo Section -->
        <div class="auth-logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="logo-icon">
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
            <h1 class="pixel-text">MicroHack</h1>
            <p class="auth-subtitle">By Soblend</p>
        </div>

        <!-- Register Form -->
        <div class="auth-card">
            <div class="auth-header">
                <h2 class="auth-title">Crear Cuenta</h2>
                <p class="auth-description">Regístrate para empezar a practicar</p>
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

            <!-- OAuth Buttons -->
<div class="oauth-buttons">
    <a href="<?php echo htmlspecialchars($githubAuthUrl); ?>" class="btn-oauth btn-github">
        <svg width="22" height="22" viewBox="0 0 98 96" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M48.854 0C21.839 0 0 22 0 49.217c0 21.756 13.993 40.172 33.405 46.69 2.427.49 3.316-1.059 3.316-2.362 0-1.141-.08-5.052-.08-9.127-13.59 2.934-16.42-5.867-16.42-5.867-2.184-5.704-5.42-7.17-5.42-7.17-4.448-3.015.324-3.015.324-3.015 4.934.326 7.523 5.052 7.523 5.052 4.367 7.496 11.404 5.378 14.235 4.074.404-3.178 1.699-5.378 3.074-6.6-10.839-1.141-22.243-5.378-22.243-24.283 0-5.378 1.94-9.778 5.014-13.2-.485-1.222-2.184-6.275.486-13.038 0 0 4.125-1.304 13.426 5.052a46.97 46.97 0 0 1 12.214-1.63c4.125 0 8.33.571 12.213 1.63 9.302-6.356 13.427-5.052 13.427-5.052 2.67 6.763.97 11.816.485 13.038 3.155 3.422 5.015 7.822 5.015 13.2 0 18.905-11.404 23.06-22.324 24.283 1.78 1.548 3.316 4.481 3.316 9.126 0 6.6-.08 11.897-.08 13.526 0 1.304.89 2.853 3.316 2.364 19.412-6.52 33.405-24.935 33.405-46.691C97.707 22 75.788 0 48.854 0z" fill="currentColor"/>
        </svg>
        <span>Registrarse con GitHub</span>
    </a>
    
    <a href="<?php echo htmlspecialchars($googleAuthUrl); ?>" class="btn-oauth btn-google">
        <svg width="22" height="22" viewBox="0 0 48 48">
            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
        </svg>
        <span>Registrarse con Google</span>
    </a>
</div>

<div class="divider">
    <span>o regístrate con email</span>
</div>

            <form method="POST" action="register.php" class="auth-form">
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i data-lucide="user"></i>
                        Nombre de Usuario
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-input" 
                        placeholder="Crea tu usuario (min. 3 caracteres)"
                        value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                        required
                        autocomplete="username"
                        minlength="3"
                        pattern="[a-zA-Z0-9_]+"
                        title="Solo letras, números y guiones bajos"
                    >
                    <small class="form-hint">Solo letras, números y guiones bajos (_)</small>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i data-lucide="lock"></i>
                        Contraseña
                    </label>
                    <div class="password-input-wrapper">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input" 
                            placeholder="Crea una contraseña (min. 6 caracteres)"
                            required
                            autocomplete="new-password"
                            minlength="6"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i data-lucide="eye" id="eye-icon-password"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="password-strength">
                        <div class="strength-bar" id="strength-bar"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">
                        <i data-lucide="lock"></i>
                        Confirmar Contraseña
                    </label>
                    <div class="password-input-wrapper">
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            class="form-input" 
                            placeholder="Confirma tu contraseña"
                            required
                            autocomplete="new-password"
                            minlength="6"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                            <i data-lucide="eye" id="eye-icon-confirm"></i>
                        </button>
                    </div>
                </div>

                <!-- Cloudflare Turnstile -->
                <div class="form-group">
                    <div class="cf-turnstile" data-sitekey="<?php echo TURNSTILE_SITE_KEY; ?>" data-theme="light"></div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" required>
                        <span>Acepto los <a href="#" class="auth-link">términos y condiciones</a> y entiendo que esto es solo para práctica ética</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i data-lucide="user-plus"></i>
                    Crear Cuenta
                </button>
            </form>

            <div class="auth-footer">
                <p>¿Ya tienes una cuenta? <a href="login.php" class="auth-link">Inicia sesión aquí</a></p>
                <a href="index.php" class="auth-link-back">
                    <i data-lucide="arrow-left"></i>
                    Volver al inicio
                </a>
            </div>
        </div>

        <!-- Security Info -->
        <div class="auth-info">
            <div class="info-item">
                <i data-lucide="shield-check"></i>
                <span>Entorno seguro de práctica</span>
            </div>
            <div class="info-item">
                <i data-lucide="code"></i>
                <span>Hacking ético controlado</span>
            </div>
            <div class="info-item">
                <i data-lucide="zap"></i>
                <span>Workspaces instantáneos</span>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIconId = inputId === 'password' ? 'eye-icon-password' : 'eye-icon-confirm';
            const eyeIcon = document.getElementById(eyeIconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.outerHTML = `<i data-lucide="eye-off" id="${eyeIconId}"></i>`;
            } else {
                passwordInput.type = 'password';
                eyeIcon.outerHTML = `<i data-lucide="eye" id="${eyeIconId}"></i>`;
            }
            
            lucide.createIcons();
        }

        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strength-bar');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            strengthBar.className = 'strength-bar';
            if (strength <= 2) {
                strengthBar.classList.add('weak');
            } else if (strength <= 3) {
                strengthBar.classList.add('medium');
            } else {
                strengthBar.classList.add('strong');
            }
        });

        // Validar que las contraseñas coincidan
        const confirmPassword = document.getElementById('confirm_password');
        const form = document.querySelector('.auth-form');

        form.addEventListener('submit', function(e) {
            if (passwordInput.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
            }
        });
    </script>
</body>
</html>