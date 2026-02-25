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
$game_data = isset($data['game_data']) ? json_encode($data['game_data']) : '{}';
$score = isset($data['score']) ? (int)$data['score'] : 0;

if (!$room_code) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'کد اتاق لازم است']);
    exit;
}

// Get room_id from room_code
$sql_get_room = "SELECT id FROM multiplayer_rooms WHERE room_code = ?";
$stmt_get_room = $connection->prepare($sql_get_room);
$stmt_get_room->bind_param("s", $room_code);
$stmt_get_room->execute();
$result_get_room = $stmt_get_room->get_result();

if ($result_get_room->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'اتاق پیدا نشد']);
    exit;
}

$room = $result_get_room->fetch_assoc();
$room_id = $room['id'];
$stmt_get_room->close();

// Verify player is in room
$sql_check = "SELECT * FROM room_players WHERE room_id = ? AND user_id = ?";
$stmt_check = $connection->prepare($sql_check);
$stmt_check->bind_param("ii", $room_id, $_SESSION['user_id']);
$stmt_check->execute();

if ($stmt_check->get_result()->num_rows === 0) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'شما در این اتاق نیستید']);
    exit;
}

// Update player game state
$sql_update = "UPDATE room_players SET game_state = ?, score = ? WHERE room_id = ? AND user_id = ?";
$stmt_update = $connection->prepare($sql_update);
$stmt_update->bind_param("ssii", $game_data, $score, $room_id, $_SESSION['user_id']);

if ($stmt_update->execute()) {
    // Also insert into updates table
    $sql_insert = "INSERT INTO room_game_updates (room_id, player_id, game_data) VALUES (?, ?, ?)";
    $stmt_insert = $connection->prepare($sql_insert);
    $stmt_insert->bind_param("iis", $room_id, $_SESSION['user_id'], $game_data);
    $stmt_insert->execute();
    
    echo json_encode(['status' => 'success', 'message' => 'حالت بازی ذخیره شد']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'خطا در ذخیره‌سازی']);
}

$stmt_check->close();
$stmt_update->close();
?>
