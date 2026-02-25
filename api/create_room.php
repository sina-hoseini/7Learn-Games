<?php
require_once '../database/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'وارد شوید']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$game_type = isset($data['game_type']) ? $data['game_type'] : 'snake';
$max_players = isset($data['max_players']) ? (int)$data['max_players'] : 2;

// Validate
if (!in_array($game_type, ['snake', 'breakout'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'نوع بازی نامعتبر']);
    exit;
}

if ($max_players < 2 || $max_players > 4) {
    $max_players = 2;
}

// Generate unique room code
$room_code = strtoupper(substr(md5(uniqid()), 0, 8));

// Create room
$sql = "INSERT INTO multiplayer_rooms (room_code, game_type, created_by, max_players) 
        VALUES (?, ?, ?, ?)";
$stmt = $connection->prepare($sql);
$stmt->bind_param("ssii", $room_code, $game_type, $_SESSION['user_id'], $max_players);

if ($stmt->execute()) {
    $room_id = $connection->insert_id;
    
    // Add creator as player
    $player_number = 1;
    $sql_player = "INSERT INTO room_players (room_id, user_id, player_number) VALUES (?, ?, ?)";
    $stmt_player = $connection->prepare($sql_player);
    $stmt_player->bind_param("iii", $room_id, $_SESSION['user_id'], $player_number);
    $stmt_player->execute();
    
    echo json_encode([
        'status' => 'success',
        'room_id' => $room_id,
        'room_code' => $room_code,
        'game_type' => $game_type,
        'message' => 'اتاق با موفقیت ایجاد شد',
        'max_players' => $max_players,
        'message' => 'اتاق ایجاد شد'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'خطا در ایجاد اتاق']);
}

$stmt->close();
?>
