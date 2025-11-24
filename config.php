<?php
// config.php - Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'qaayzymy_badgeuser');
define('DB_PASS', 'RQweC>9+c-g]J*7');
define('DB_NAME', 'qaayzymy_badges');

// Conectar a la base de datos
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die(json_encode(['error' => 'Error de conexión: ' . $conn->connect_error]));
    }
    return $conn;
}

// Habilitar CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}
?>