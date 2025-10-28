<?php
// Debug endpoint to return recent mail debug log entries as JSON
require_once __DIR__ . '/../includes/app.php';

$logFile = __DIR__ . '/../includes/mail_debug.log';
$lines = [];
if (file_exists($logFile)) {
    $all = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lines = array_slice($all, -200);
}
header('Content-Type: application/json');
echo json_encode([
    'ok' => true,
    'lines' => $lines
], JSON_UNESCAPED_UNICODE);
