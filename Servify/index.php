<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servify - Inicio</title>
    <style>
        body { font-family: Arial, sans-serif; background: #111; color: #fff; padding: 32px; }
        a { color: #ffd600; display: inline-block; margin: 8px 0; }
        .card { background: #1b1b1b; padding: 24px; border-radius: 16px; max-width: 600px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Servify</h1>
        <p>Selecciona una vista para abrirla:</p>
        <p><a href="pagina_vista_usuario.php">Vista de usuario</a></p>
        <p><a href="Vista%20de%20admin/pagina_vista_admin.php">Vista de administrador</a></p>
    </div>
</body>
</html>
