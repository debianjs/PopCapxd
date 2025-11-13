<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MicroHack - Plataforma de práctica de hacking ético">
    <meta name="author" content="Soblend Development Studio">
    <title><?php echo $pageTitle ?? 'MicroHack - Plataforma de Hacking Ético'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo BASE_URL ?? ''; ?>/assets/images/favicon.svg">
    
    <!-- Estilos -->
    <link rel="stylesheet" href="<?php echo BASE_URL ?? ''; ?>/assets/css/style.css">
    
    <!-- Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.263.1/dist/umd/lucide.min.js"></script>
    
    <!-- Estilos adicionales opcionales -->
    <?php if (isset($additionalStyles)): ?>
        <?php echo $additionalStyles; ?>
    <?php endif; ?>
</head>
<body class="<?php echo $bodyClass ?? ''; ?>">