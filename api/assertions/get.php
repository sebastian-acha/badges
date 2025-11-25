<?php
// api/assertions/get.php - Obtener una assertion para verificación
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$assertionId = $_GET['id'] ?? '';

if (empty($assertionId)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de assertion requerido']);
    exit;
}

var_dump($_GET[]);
die();
$conn = getDBConnection();

$stmt = $conn->prepare("
    SELECT a.*, b.* 
    FROM assertions a 
    JOIN badges b ON a.badge_id = b.id 
    WHERE a.assertion_id = ?
");
$stmt->bind_param("s", $assertionId);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    http_response_code(404);
    echo json_encode(['error' => 'Assertion no encontrada']);
    exit;
}

$assertion = [
    'id' => $data['assertion_id'],
    'type' => 'Assertion',
    '@context' => 'https://w3id.org/openbadges/v2',
    'badge' => [
        'name' => $data['name'],
        'description' => $data['description'],
        'criteria' => $data['criteria'],
        'image' => $data['image_url']
    ],
    'recipient' => [
        'type' => 'email',
        'identity' => $data['recipient_hash'],
        'hashed' => true
    ],
    'issuedOn' => $data['issued_on'],
    'verification' => [
        'type' => 'hosted'
    ]
];

if (!empty($data['evidence'])) {
    $assertion['evidence'] = $data['evidence'];
}

echo json_encode($assertion);

$stmt->close();
$conn->close();
?>