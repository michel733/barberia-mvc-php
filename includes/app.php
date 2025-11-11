<?php 
use Model\ActiveRecord;
require __DIR__ . '/../vendor/autoload.php';

// --- ESTA ES LA FORMA RECOMENDADA ---
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load(); // Carga el .env o falla si no existe

require 'funciones.php';
require 'database.php';


// Conectarnos a la base de datos
ActiveRecord::setDB($db);