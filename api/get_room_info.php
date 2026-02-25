<?php
require_once '../database/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'وارد شوید']);
    exit;
}

$room_code = isset($_GET['room_code']) ? strtoupper(trim($_GET['room_code'])) : '';
$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;

if (!$room_code && !$room_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'اطلاعات اتاق لازم است']);
    exit;
}

// Get room info
if ($room_code) {
    $sql = "SELECT * FROM multiplayer_rooms WHERE room_code = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $room_code);
} else {
    $sql = "SELECT * FROM multiplayer_rooms WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $room_id);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'اتاق پیدا نشد']);
    exit;
}

$room = $result->fetch_assoc();

// Get players in room
$sql_players = "SELECT rp.*, u.username FROM room_players rp 
                JOIN users u ON rp.user_id = u.id 
                WHERE rp.room_id = ? 
                ORDER BY rp.player_number";
$stmt_players = $connection->prepare($sql_players);
$stmt_players->bind_param("i", $room['id']);
$stmt_players->execute();
$players_result = $stmt_players->get_result();

$players = [];
while ($player = $players_result->fetch_assoc()) {
    $players[] = [
        'player_number' => $player['player_number'],
        'username' => $player['username'],
        'user_id' => $player['user_id'],
        'score' => $player['score'],
        'game_state' => $player['game_state']
    ];
}

echo json_encode([
    'status' => 'success',
    'room' => [
        'id' => $room['id'],
        'room_code' => $room['room_code'],
        'game_type' => $room['game_type'],
        'status' => $room['status'],
        'max_players' => $room['max_players'],
        'current_players' => $room['current_players'],
        'created_at' => $room['created_at']
    ],
    'players' => $players
]);

$stmt->close();
$stmt_players->close();
?>
