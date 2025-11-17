<?php
/**
 * Script de verificación de configuración
 * Ejecutar antes de subir a producción
 */

echo "=== Verificación de Configuración AppSalon ===\n\n";

// 1. Verificar PHP
echo "1. Versión de PHP: " . phpversion() . "\n";
if (version_compare(phpversion(), '8.0.0', '<')) {
    echo "   ⚠️  ADVERTENCIA: Se requiere PHP 8.0 o superior\n";
} else {
    echo "   ✅ Versión correcta\n";
}

// 2. Verificar extensiones
echo "\n2. Extensiones PHP:\n";
$extensiones = ['mysqli', 'pdo', 'mbstring', 'openssl'];
foreach ($extensiones as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✅ $ext\n";
    } else {
        echo "   ❌ $ext - NO INSTALADA\n";
    }
}

// 3. Verificar archivos críticos
echo "\n3. Archivos críticos:\n";
$archivos = [
    'includes/.env' => 'Configuración de entorno',
    'vendor/autoload.php' => 'Dependencias Composer',
    'public/build/css/app.css' => 'CSS compilado',
    'public/build/js/app.js' => 'JavaScript compilado'
];

foreach ($archivos as $archivo => $desc) {
    if (file_exists(__DIR__ . '/' . $archivo)) {
        echo "   ✅ $desc ($archivo)\n";
    } else {
        echo "   ❌ $desc ($archivo) - NO ENCONTRADO\n";
    }
}

// 4. Verificar permisos
echo "\n4. Permisos:\n";
if (file_exists(__DIR__ . '/includes/.env')) {
    $perms = substr(sprintf('%o', fileperms(__DIR__ . '/includes/.env')), -4);
    echo "   includes/.env: $perms\n";
    if ($perms === '0644' || $perms === '0600') {
        echo "   ✅ Permisos correctos\n";
    } else {
        echo "   ⚠️  Recomendado: 644 o 600\n";
    }
}

// 5. Verificar .env
echo "\n5. Variables de entorno:\n";
if (file_exists(__DIR__ . '/includes/.env')) {
    $env = parse_ini_file(__DIR__ . '/includes/.env');
    $vars = ['DB_HOST', 'DB_USER', 'DB_NAME', 'EMAIL_HOST', 'EMAIL_USER', 'PROJECT_URL'];
    
    foreach ($vars as $var) {
        if (isset($env[$var]) && !empty($env[$var])) {
            // No mostrar valores sensibles
            if (strpos($var, 'PASSWORD') !== false) {
                echo "   ✅ $var: [CONFIGURADO]\n";
            } else {
                echo "   ✅ $var: " . $env[$var] . "\n";
            }
        } else {
            echo "   ❌ $var: NO CONFIGURADO\n";
        }
    }
}

// 6. Verificar conexión a BD
echo "\n6. Conexión a Base de Datos:\n";
if (file_exists(__DIR__ . '/includes/.env')) {
    $env = parse_ini_file(__DIR__ . '/includes/.env');
    
    // Intentar con puerto 3307 (como está en database.php)
    $conn = @mysqli_connect(
        $env['DB_HOST'] ?? 'localhost',
        $env['DB_USER'] ?? '',
        $env['DB_PASSWORD'] ?? '',
        $env['DB_NAME'] ?? '',
        3307
    );
    
    if ($conn) {
        echo "   ✅ Conexión exitosa (puerto 3307)\n";
        mysqli_close($conn);
    } else {
        // Intentar con puerto por defecto
        $conn = @mysqli_connect(
            $env['DB_HOST'] ?? 'localhost',
            $env['DB_USER'] ?? '',
            $env['DB_PASSWORD'] ?? '',
            $env['DB_NAME'] ?? ''
        );
        
        if ($conn) {
            echo "   ⚠️  Conexión exitosa con puerto 3306 (por defecto)\n";
            echo "   ⚠️  NOTA: database.php usa puerto 3307, considerar cambiar\n";
            mysqli_close($conn);
        } else {
            echo "   ❌ Error de conexión: " . mysqli_connect_error() . "\n";
        }
    }
}

echo "\n=== Fin de la verificación ===\n";
echo "\nSi hay errores (❌) o advertencias (⚠️), corrígelos antes de subir a producción.\n";
