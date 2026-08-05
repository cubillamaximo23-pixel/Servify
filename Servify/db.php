<?php
$dbHost = getenv('SERVIFY_DB_HOST') ?: 'localhost';
$dbPort = (int) (getenv('SERVIFY_DB_PORT') ?: 3306);
$dbName = getenv('SERVIFY_DB_NAME') ?: 'servify_db';
$dbUser = getenv('SERVIFY_DB_USER') ?: 'root';
$dbPass = getenv('SERVIFY_DB_PASS') ?: '';

$dbStatus = 'desconectado';
$dbMessage = 'La conexión a MySQL todavía no se ha probado.';
$dbError = '';
$connection = null;

try {
    $connection = new mysqli($dbHost, $dbUser, $dbPass, '', $dbPort);

    if ($connection->connect_error) {
        throw new RuntimeException($connection->connect_error);
    }

    $connection->set_charset('utf8');

    if (!$connection->select_db($dbName)) {
        $dbStatus = 'error';
        $dbError = "No existe la base de datos '{$dbName}'.";
        $dbMessage = 'La base de datos aún no está creada. Puedes crearla desde phpMyAdmin o MySQL Workbench.';
    } else {
        $dbStatus = 'conectado';
        $dbMessage = "Conexión activa con la base de datos '{$dbName}'.";
    }
} catch (Throwable $e) {
    $dbStatus = 'error';
    $dbError = $e->getMessage();
    $dbMessage = 'No se pudo establecer la conexión con MySQL. Revisa host, usuario, contraseña y nombre de la base de datos.';
}

function isDbConnected(): bool
{
    global $dbStatus;
    return $dbStatus === 'conectado';
}
?>
