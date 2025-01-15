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
if (!isset($input['feeds']) || !is_array($input['feeds'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$feeds = $input['feeds'];

$mysqli = new mysqli('localhost', 'u759469055_newletteradmin', 'Brique@123#', 'u759469055_newletter');
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$stmt = $mysqli->prepare('INSERT INTO feeds (title, link, published, summary, site, logo, image, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query preparation failed']);
    exit;
}

foreach ($feeds as $feed) {
    $title = $feed['title'];
    $link = $feed['link'];
    $published = $feed['published'];
    $summary = $feed['summary'];
    $site = $feed['site'];
    $logo = $feed['logo'];
    $image = $feed['image'];
    $category_id = $feed['category_id'] ?? 1;

    $stmt->bind_param('sssssssi', $title, $link, $published, $summary, $site, $logo, $image, $category_id);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to store feed']);
        exit;
    }
}

$stmt->close();
$mysqli->close();

echo json_encode(['success' => 'Feeds stored successfully']);
