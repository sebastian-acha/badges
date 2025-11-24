<?php
// api/badges/issue.php - Emitir un badge
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

if (!isset($data['badgeId']) || !isset($data['recipient']['email']) || !isset($data['recipient']['name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

$conn = getDBConnection();

// Obtener información del badge
$stmt = $conn->prepare("SELECT * FROM badges WHERE id = ?");
$stmt->bind_param("i", $data['badgeId']);
$stmt->execute();
$result = $stmt->get_result();
$badge = $result->fetch_assoc();

if (!$badge) {
    http_response_code(404);
    echo json_encode(['error' => 'Badge no encontrado']);
    exit;
}

// Crear assertion (emisión)
$assertionId = 'urn:uuid:' . bin2hex(random_bytes(16));
$recipientHash = 'sha256$' . hash('sha256', $data['recipient']['email']);
$issuedOn = date('c');

$ev=$data['evidence'] ?? '';

$stmt = $conn->prepare("INSERT INTO assertions (assertion_id, badge_id, recipient_name, recipient_email, recipient_hash, issued_on, evidence) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sisssss",
    $assertionId,
    $data['badgeId'],
    $data['recipient']['name'],
    $data['recipient']['email'],
    $recipientHash,
    $issuedOn,
    $ev
);

if ($stmt->execute()) {
    $assertion = [
        'id' => $assertionId,
        'type' => 'Assertion',
        '@context' => 'https://w3id.org/openbadges/v2',
        'badge' => [
            'name' => $badge['name'],
            'description' => $badge['description'],
            'criteria' => $badge['criteria'],
            'image' => $badge['image_url'] ?: "https://via.placeholder.com/200x200/4F46E5/FFFFFF?text=" . urlencode($badge['name'])
        ],
        'recipient' => [
            'type' => 'email',
            'identity' => $recipientHash,
            'hashed' => true
        ],
        'recipientName' => $data['recipient']['name'],
        'recipientEmail' => $data['recipient']['email'],
        'issuedOn' => $issuedOn,
        'verification' => [
            'type' => 'hosted',
            'url' => 'https://tudominio.com/api/assertions/' . $assertionId
        ]
    ];
    
    if (!empty($data['evidence'])) {
        $assertion['evidence'] = $data['evidence'];
    }
    
    echo json_encode(['success' => true, 'assertion' => $assertion]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al emitir badge']);
}

$stmt->close();
$conn->close();
?>