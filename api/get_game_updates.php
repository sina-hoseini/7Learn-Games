<?php
require_once '../database/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'وارد شوید']);
    exit;
}

$room_code = isset($_GET['room_code']) ? strtoupper(trim($_GET['room_code'])) : '';
$since = isset($_GET['since']) ? $_GET['since'] : date('Y-m-d H:i:s', time() - 5);

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

// Get latest updates from other players
$sql = "SELECT rgu.*, u.username FROM room_game_updates rgu
        JOIN users u ON rgu.player_id = u.id
        WHERE rgu.room_id = ? AND rgu.player_id != ? AND rgu.created_at > ?
        ORDER BY rgu.created_at DESC
        LIMIT 10";
$stmt = $connection->prepare($sql);
$stmt->bind_param("iis", $room_id, $_SESSION['user_id'], $since);
$stmt->execute();
$result = $stmt->get_result();

$updates = [];
while ($update = $result->fetch_assoc()) {
    $updates[] = [
        'player_id' => $update['player_id'],
        'username' => $update['username'],
        'game_data' => json_decode($update['game_data'], true),
        'created_at' => $update['created_at']
    ];
}

// Also get current player states
$sql_states = "SELECT rp.*, u.username FROM room_players rp
               JOIN users u ON rp.user_id = u.id
               WHERE rp.room_id = ?
               ORDER BY rp.player_number";
$stmt_states = $connection->prepare($sql_states);
$stmt_states->bind_param("i", $room_id);
$stmt_states->execute();
$states_result = $stmt_states->get_result();

$player_states = [];
while ($state = $states_result->fetch_assoc()) {
    $player_states[] = [
        'player_number' => $state['player_number'],
        'user_id' => $state['user_id'],
        'username' => $state['username'],
        'score' => $state['score'],
        'game_state' => $state['game_state'] ? json_decode($state['game_state'], true) : null
    ];
}

echo json_encode([
    'status' => 'success',
    'updates' => $updates,
    'player_states' => $player_states
]);

$stmt->close();
$stmt_states->close();
?>
