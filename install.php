<?php
// install.php - Script de instalación de la base de datos
require_once 'config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Crear base de datos
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    echo "Base de datos creada exitosamente<br>";
} else {
    echo "Error creando base de datos: " . $conn->error . "<br>";
}

$conn->select_db(DB_NAME);

// Crear tabla de badges
$sql = "CREATE TABLE IF NOT EXISTS badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    criteria TEXT NOT NULL,
    image_url VARCHAR(500),
    issuer VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'badges' creada exitosamente<br>";
} else {
    echo "Error creando tabla badges: " . $conn->error . "<br>";
}

// Crear tabla de assertions
$sql = "CREATE TABLE IF NOT EXISTS assertions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assertion_id VARCHAR(100) UNIQUE NOT NULL,
    badge_id INT NOT NULL,
    recipient_name VARCHAR(255) NOT NULL,
    recipient_email VARCHAR(255) NOT NULL,
    recipient_hash VARCHAR(255) NOT NULL,
    issued_on DATETIME NOT NULL,
    evidence VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE,
    INDEX idx_assertion_id (assertion_id),
    INDEX idx_recipient_email (recipient_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'assertions' creada exitosamente<br>";
} else {
    echo "Error creando tabla assertions: " . $conn->error . "<br>";
}

// Crear tabla de API keys
$sql = "CREATE TABLE IF NOT EXISTS api_keys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    api_key VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_api_key (api_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'api_keys' creada exitosamente<br>";
} else {
    echo "Error creando tabla api_keys: " . $conn->error . "<br>";
}

// Insertar API key de ejemplo
$apiKey = 'obapi_' . bin2hex(random_bytes(16));
$stmt = $conn->prepare("INSERT INTO api_keys (api_key, name) VALUES (?, 'API Key Principal')");
$stmt->bind_param("s", $apiKey);
$stmt->execute();

echo "<br><strong>Instalación completada!</strong><br>";
echo "<strong>Tu API Key:</strong> " . $apiKey . "<br>";
echo "<strong>¡GUARDA ESTA CLAVE EN UN LUGAR SEGURO!</strong><br>";

$conn->close();
?>