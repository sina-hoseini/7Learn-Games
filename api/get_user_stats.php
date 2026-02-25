<?php
require_once '../database/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['snake' => 0, 'breakout' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];

$snakeQuery = "SELECT MAX(score) as max_score FROM scores WHERE user_id = $user_id AND game_type = 'snake'";
$breakoutQuery = "SELECT MAX(score) as max_score FROM scores WHERE user_id = $user_id AND game_type = 'breakout'";

$snakeResult = $connection->query($snakeQuery);
$breakoutResult = $connection->query($breakoutQuery);

$snakeScore = 0;
$breakoutScore = 0;

if ($snakeRow = $snakeResult->fetch_assoc()) {
    $snakeScore = $snakeRow['max_score'] ?? 0;
}

if ($breakoutRow = $breakoutResult->fetch_assoc()) {
    $breakoutScore = $breakoutRow['max_score'] ?? 0;
}

echo json_encode([
    'snake' => (int)$snakeScore,
    'breakout' => (int)$breakoutScore
]);
?>
