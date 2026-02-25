<?php
require_once '../database/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'وارد شوید']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$room_code = isset($data['room_code']) ? strtoupper(trim($data['room_code'])) : '';

if (!$room_code) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'کد اتاق لازم است']);
    exit;
}

// Check if room exists
$sql = "SELECT * FROM multiplayer_rooms WHERE room_code = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $room_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'اتاق پیدا نشد']);
    exit;
}

$room = $result->fetch_assoc();
$room_id = $room['id'];

// Check room status
if ($room['status'] !== 'waiting') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'این اتاق در حال بازی است']);
    exit;
}

// Check if room is full
if ($room['current_players'] >= $room['max_players']) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'اتاق پر است']);
    exit;
}

// Check if player already in room
$sql_check = "SELECT * FROM room_players WHERE room_id = ? AND user_id = ?";
$stmt_check = $connection->prepare($sql_check);
$stmt_check->bind_param("ii", $room_id, $_SESSION['user_id']);
$stmt_check->execute();

if ($stmt_check->get_result()->num_rows > 0) {
    echo json_encode([
        'status' => 'success',
        'room_id' => $room_id,
        'room_code' => $room_code,
        'message' => 'شما قبلاً در این اتاق هستید'
    ]);
    exit;
}

// Add player to room
$player_number = $room['current_players'] + 1;
$sql_add = "INSERT INTO room_players (room_id, user_id, player_number) VALUES (?, ?, ?)";
$stmt_add = $connection->prepare($sql_add);
$stmt_add->bind_param("iii", $room_id, $_SESSION['user_id'], $player_number);

if ($stmt_add->execute()) {
    // Update room player count
    $sql_update = "UPDATE multiplayer_rooms SET current_players = current_players + 1 WHERE id = ?";
    $stmt_update = $connection->prepare($sql_update);
    $stmt_update->bind_param("i", $room_id);
    $stmt_update->execute();
    
    // If room is full, start game
    if ($player_number >= $room['max_players']) {
        $status = 'playing';
        $sql_status = "UPDATE multiplayer_rooms SET status = ? WHERE id = ?";
        $stmt_status = $connection->prepare($sql_status);
        $stmt_status->bind_param("si", $status, $room_id);
        $stmt_status->execute();
    }
    
    echo json_encode([
        'status' => 'success',
        'room_id' => $room_id,
        'room_code' => $room_code,
        'player_number' => $player_number,
        'message' => 'به اتاق پیوستید'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'خطا در پیوستن به اتاق']);
}

$stmt->close();
$stmt_check->close();
$stmt_add->close();
?>
