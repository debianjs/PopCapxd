<?php
session_start();
require_once 'includes/config.php';
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada | MicroHack</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.263.1/dist/umd/lucide.min.js"></script>
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 2rem;
        }
        .error-container {
            background: white;
            padding: 3rem;
            border-radius: 1rem;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .error-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            color: #10b981;
        }
        .error-code {
            font-size: 4rem;
            font-weight: bold;
            color: #10b981;
            margin-bottom: 1rem;
            font-family: 'Press Start 2P', cursive;
        }
        .error-title {
            font-size: 1.5rem;
            color: #1f2937;
            margin-bottom: 1rem;
        }
        .error-description {
            color: #6b7280;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body class="error-page">
    <div class="error-container">
        <div class="error-icon">
            <i data-lucide="alert-circle"></i>
        </div>
        <h1 class="error-code">404</h1>
        <h2 class="error-title">Página no encontrada</h2>
        <p class="error-description">
            Lo sentimos, la página que buscas no existe o ha sido movida.
        </p>
        <a href="index.php" class="btn btn-primary">
            <i data-lucide="home"></i>
            Volver al inicio
        </a>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>