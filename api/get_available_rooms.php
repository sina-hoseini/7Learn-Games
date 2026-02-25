<?php
require_once '../database/database.php';

header('Content-Type: application/json');

// Get all waiting rooms with player count
$sql = "SELECT mr.*, u.username as creator, COUNT(rp.id) as current_players 
        FROM multiplayer_rooms mr
        JOIN users u ON mr.created_by = u.id
        LEFT JOIN room_players rp ON mr.id = rp.room_id
        WHERE mr.status = 'waiting'
        GROUP BY mr.id
        ORDER BY mr.created_at DESC
        LIMIT 20";
$stmt = $connection->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$rooms = [];
while ($room = $result->fetch_assoc()) {
    $rooms[] = [
        'id' => $room['id'],
        'room_code' => $room['room_code'],
        'game_type' => $room['game_type'],
        'creator' => $room['creator'],
        'status' => $room['status'],
        'max_players' => $room['max_players'],
        'current_players' => (int)$room['current_players'],
        'created_at' => $room['created_at']
    ];
}

echo json_encode([
    'status' => 'success',
    'rooms' => $rooms
]);

$stmt->close();
?>
