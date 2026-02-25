<?php
require_once '../database/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$game = $data['game'] ?? '';
$score = $data['score'] ?? 0;

if (empty($game) || !is_numeric($score)) {
    http_response_code(400);
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "INSERT INTO scores (user_id, game_type, score) VALUES ($user_id, '$game', $score)";

if ($connection->query($query)) {
    echo json_encode(['success' => true, 'score' => $score]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $connection->error]);
}
?>
