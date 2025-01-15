<?php
header('Content-Type: application/json');
// Set error reporting
ini_set('log_errors', 1);
ini_set('error_log', '500.log');
ini_set('display_errors', 0);

$authHeader = getallheaders()['Authorization'] ?? '';
$token = str_replace('Bearer ', '', $authHeader);
if (base64_decode($token) !== 'briquetech') {
	http_response_code(401);
	echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
	exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$mysqli = new mysqli('localhost', 'u759469055_newletteradmin', 'Brique@123#', 'u759469055_newletter');
if ($mysqli->connect_error) {
	die("Connection failed: " . $mysqli->connect_error);
}

function validateUser($user_id, $mysqli) {
	$senderValid = $mysqli->query("SELECT 1 FROM users WHERE id = $user_id")->num_rows > 0;
	return $senderValid;
}
if (isset($input['action'])) {
    if ($input['action'] == "putmessage" && $method == 'PUT') {
    	if (!isset($input['user_id'], $input['message_text'])) {
    		echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    		exit;
    	}
    
    	$user_id = $input['user_id'];
    	$message_text = $input['message_text'];
    
    	if (!validateUser($user_id, $mysqli)) {
    		echo json_encode(['status' => 'error', 'message' => 'Invalid User']);
    		exit;
    	}
    
    	$stmt = $mysqli->prepare("INSERT INTO messages (user_id, message_text) VALUES (?, ?)");
    	$stmt->bind_param("is", $user_id, $message_text);
    	$stmt->execute();
    	$message_id = $stmt->insert_id;
    	$stmt->close();
    
    	echo json_encode(['status' => 1, 'message_id' => $message_id]);
    } elseif ($input['action'] == "allmessages" && $method == 'POST') {
    	if (!isset($input['user_id'])) {
    		echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    		exit;
    	}
    
    	$user_id = $input['user_id'];
    
    	$result = $mysqli->query("SELECT m.message_id, m.user_id, m.message_text, m.sent_at, m.delivered_at, m.read_at, u.username AS sender_name FROM messages m, users u where m.user_id = u.id AND m.user_id = $user_id ORDER BY m.sent_at ASC");
    	$messages = [];
    	while ($row = $result->fetch_assoc()) {
    		$messages[] = [
    			'message_id' => $row['message_id'],
    			'chat_id' => $row['chat_id'],
    			'user' => [
    				'id' => $row['sender_id'],
    				'username' => $row['sender_name']
    			],
    			'message_text' => $row['message_text'],
    			'sent_at' => $row['sent_at']
    		];
    	}
    
    	echo json_encode(['messages' => $messages]);
    } elseif ($input['action'] == "deletemessage" && $method == 'DELETE') {
    	if (!isset($input['message_id'])) {
    		echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    		exit;
    	}
    
    	$message_id = $input['message_id'];
    	$stmt = $mysqli->prepare("DELETE FROM messages WHERE message_id = ?");
    	$stmt->bind_param("i", $message_id);
    	$stmt->execute();
    	$stmt->close();
    
    	echo json_encode(['status' => 'success']);
    } else {
    	http_response_code(405);
    	echo json_encode(['status' => 'error', 'message' => 'Illegal action']);
    }
}
else {
	http_response_code(405);
	echo json_encode(['status' => 'error', 'message' => 'Illegal action']);
}

$mysqli->close();