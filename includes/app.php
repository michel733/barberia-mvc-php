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
if (file_exists(__DIR__ . '/.env')) {
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->safeLoad();
}

require 'funciones.php';
require 'database.php';

// Conectarnos a la base de datos
ActiveRecord::setDB($db);