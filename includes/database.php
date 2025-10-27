<?php

// Obtener variables de entorno de forma robusta (getenv() como fallback)
$dbHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? '127.0.0.1');
$dbUser = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root');
$dbPass = getenv('DB_PASSWORD') ?: ($_ENV['DB_PASSWORD'] ?? '');
$dbName = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? '');
$dbPort = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '3306');

// Si el host es 'localhost' forzamos 127.0.0.1 para usar TCP (evita errores de socket)
if ($dbHost === 'localhost') {
    $dbHost = '127.0.0.1';
}

// Intentar conectar (silenciar warnings y comprobar el resultado)
$db = @mysqli_connect($dbHost, $dbUser, $dbPass, $dbName, (int)$dbPort);

if (!$db) {
    // Mensaje amigable y log técnico (en producción NO mostrar detalles sensibles)
    error_log("[DB ERROR] mysqli_connect failed: (" . mysqli_connect_errno() . ") " . mysqli_connect_error());
    // Mostrar mensaje genérico al usuario
    echo "Error: no se pudo conectar a la base de datos. Comprueba la configuración del servidor.";
    // Para entornos de desarrollo puedes descomentar las siguientes líneas para más detalle
    // echo "errno de depuración: " . mysqli_connect_errno();
    // echo "error de depuración: " . mysqli_connect_error();
    exit;
}

$db->set_charset("utf8");
