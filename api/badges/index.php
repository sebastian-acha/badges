<?php
// api/badges/index.php - Listar todos los badges
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$conn = getDBConnection();

$result = $conn->query("SELECT * FROM badges ORDER BY created_at DESC");
$badges = [];

while ($row = $result->fetch_assoc()) {
    $badges[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'description' => $row['description'],
        'criteria' => $row['criteria'],
        'image' => $row['image_url'] ?: "https://via.placeholder.com/200x200/4F46E5/FFFFFF?text=" . urlencode($row['name']),
        'issuer' => $row['issuer'],
        '@context' => 'https://w3id.org/openbadges/v2',
        'type' => 'BadgeClass'
    ];
}

echo json_encode(['success' => true, 'badges' => $badges]);

$conn->close();
?>