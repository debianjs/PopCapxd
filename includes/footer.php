<!-- Scripts principales -->
    <script src="<?php echo BASE_URL ?? ''; ?>/assets/js/main.js"></script>
    
    <?php if (isset($dashboardPage) && $dashboardPage): ?>
        <script src="<?php echo BASE_URL ?? ''; ?>/assets/js/dashboard.js"></script>
    <?php endif; ?>
    
    <!-- Scripts adicionales opcionales -->
    <?php if (isset($additionalScripts)): ?>
        <?php echo $additionalScripts; ?>
    <?php endif; ?>
    
    <!-- Inicializar iconos de Lucide -->
    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
</body>
</html>