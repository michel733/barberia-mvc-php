<?php 
use Model\ActiveRecord;

// Composer autoload (verificar existencia para evitar fatal cuando vendor no está instalado)
$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
	require $autoload;
} else {
	// Registrar el error y mostrar mensaje genérico en producción
	error_log("Autoload not found: {$autoload}");
	http_response_code(500);
	echo "Aplicación no disponible: dependencias no instaladas.";
	exit;
}

// Cargar variables de entorno (si existe .env)
// Cargar variables de entorno (si existe .env)
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
	// Intentar cargar con vlucas/phpdotenv si está disponible
	if (class_exists('Dotenv\\Dotenv')) {
		try {
			$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
			$dotenv->safeLoad();
			error_log('[ENV] phpdotenv cargado desde ' . $envPath);
		} catch (Exception $e) {
			error_log('[ENV ERROR] phpdotenv fallo: ' . $e->getMessage());
		}
	} else {
		error_log('[ENV WARN] phpdotenv no encontrado, usando fallback para cargar .env');
	}

	// Verificar que las variables críticas estén disponibles; si no, cargar manualmente como fallback
	if (!getenv('EMAIL_HOST') || !getenv('EMAIL_USER') || !getenv('EMAIL_PASSWORD')) {
		$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($lines as $line) {
			$line = trim($line);
			if ($line === '' || strpos($line, '#') === 0) continue;
			if (strpos($line, '=') === false) continue;
			list($key, $value) = explode('=', $line, 2);
			$key = trim($key);
			$value = trim($value);
			// Remove surrounding quotes
			$value = trim($value, "\"'");
			putenv("{$key}={$value}");
            
			// populate superglobals
			if (!array_key_exists($key, $_ENV)) $_ENV[$key] = $value;
			if (!array_key_exists($key, $_SERVER)) $_SERVER[$key] = $value;
		}
		error_log('[ENV] Variables cargadas desde .env mediante fallback: ' . $envPath);
	}
}

require 'funciones.php';
require 'database.php';

// Conectarnos a la base de datos
ActiveRecord::setDB($db);