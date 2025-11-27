<?php
// config.php - Configuración de base de datos
define('DB_HOST', '127.0.0.1');
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
//header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
//header('Access-Control-Allow-Headers: Content-Type, Authorization');
//header('Content-Type: application/json');



//if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//    exit(0);
//}


// config.php - Reemplaza la sección de headers CORS con esto:

// Permitir acceso desde cualquier origen (dinámico)
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // Cachear preflight por 1 día
}

// Manejar preflight request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         

    // AQUÍ ESTÁ EL TRUCO: Aceptamos cualquier header que el navegador pida
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

header('Content-Type: application/json');
?>