<?php
$file = $_GET["file"] ?? "index.php";

// Vulnerable a LFI
if (file_exists($file)) {
    echo "<h1>Contenido del archivo: " . htmlspecialchars($file) . "</h1>";
    echo "<pre>" . htmlspecialchars(file_get_contents($file)) . "</pre>";
    echo "<p>Flag: FLAG{LFI_SUCCESS}</p>";
} else {
    echo "Archivo no encontrado";
}
?>
<br><a href=\"index.php\">← Volver</a>