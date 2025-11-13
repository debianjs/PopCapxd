<?php
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    
    // Vulnerable: sin validación de tipo de archivo
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        $message = "<div class=\"success\">Archivo subido: " . htmlspecialchars(basename($_FILES["file"]["name"])) . "<br>Flag: FLAG{FILE_UPLOAD_SUCCESS}</div>";
    } else {
        $message = "<div class=\"error\">Error al subir archivo</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload File</title>
    <style>
        body { font-family: Arial; padding: 40px; background: #f0f0f0; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        input[type="file"] { margin: 20px 0; }
        button { padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .success { background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0; color: #155724; }
        .error { background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Subir Archivo</h1>
        <?php echo $message; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <br>
            <button type="submit">Subir</button>
        </form>
        <br>
        <a href="index.php">← Volver</a>
    </div>
</body>
</html>