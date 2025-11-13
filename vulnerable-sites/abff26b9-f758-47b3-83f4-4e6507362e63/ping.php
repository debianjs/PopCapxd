<?php
$output = "";
if (isset($_POST["ip"])) {
    $ip = $_POST["ip"];
    // Vulnerable a Command Injection
    $output = shell_exec("ping -c 4 " . $ip);
    if (strpos($output, "FLAG") === false) {
        $output .= "\n\nFlag: FLAG{COMMAND_INJECTION_SUCCESS}";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ping Tool</title>
    <style>
        body { font-family: Arial; padding: 40px; background: #f0f0f0; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        input { padding: 10px; width: 300px; border: 1px solid #ddd; border-radius: 5px; }
        button { padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; }
        pre { background: #f8f8f8; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏓 Ping Tool</h1>
        <form method="POST">
            <input type="text" name="ip" placeholder="Ingresa IP o hostname" value="<?php echo htmlspecialchars($_POST['ip'] ?? ''); ?>">
            <button type="submit">Ping</button>
        </form>
        <?php if ($output): ?>
            <h3>Resultado:</h3>
            <pre><?php echo htmlspecialchars($output); ?></pre>
        <?php endif; ?>
        <br>
        <a href="index.php">← Volver</a>
    </div>
</body>
</html>