<?php
// api/badges/create.php - Crear un badge
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Verificar API Key
$headers = getallheaders();
$apiKey = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

if (empty($apiKey)) {
    http_response_code(401);
    echo json_encode(['error' => 'API Key requerida']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['name']) || !isset($data['description']) || !isset($data['criteria'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

$conn = getDBConnection();

$stmt = $conn->prepare("INSERT INTO badges (name, description, criteria, image_url, issuer, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
$im=$data['imageUrl'] ?? "https://via.placeholder.com/200x200/4F46E5/FFFFFF?text=" . urlencode($data['name']);
$is=$data['issuer'] ?? 'Eduhive';
$stmt->bind_param("sssss", 
    $data['name'], 
    $data['description'], 
    $data['criteria'],
    $im,
    $is
);

if ($stmt->execute()) {
    $badgeId = $conn->insert_id;
    
    $badge = [
        'id' => $badgeId,
        'name' => $data['name'],
        'description' => $data['description'],
        'criteria' => $data['criteria'],
        'image' => $data['imageUrl'] ?? "https://via.placeholder.com/200x200/4F46E5/FFFFFF?text=" . urlencode($data['name']),
        '@context' => 'https://w3id.org/openbadges/v2',
        'type' => 'BadgeClass'
    ];
    
    echo json_encode(['success' => true, 'badge' => $badge]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al crear badge']);
}

$stmt->close();
$conn->close();
?>
