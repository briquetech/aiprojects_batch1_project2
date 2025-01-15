<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['error' => 'Only PUT requests are allowed']);
    exit;
}

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (strpos($authHeader, 'Bearer ') !== 0) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$token = substr($authHeader, 7);
if (base64_decode($token) !== 'briquetech') {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid token']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$page = 1;
if ( isset($input['page']) && is_numeric($input['page']))
	$page = (int)$input['page'];

$limit = 10;
$offset = ($page - 1) * $limit;

$mysqli = new mysqli('localhost', 'u759469055_newletteradmin', 'Brique@123#', 'u759469055_newletter');
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$query = "SELECT * FROM feeds  group by category_id, site ORDER BY synced_on desc, id ASC LIMIT ? OFFSET ?";
$stmt = $mysqli->prepare($query);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query preparation failed']);
    exit;
}

$stmt->bind_param('ii', $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$feeds = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$mysqli->close();

echo json_encode($feeds);
?>