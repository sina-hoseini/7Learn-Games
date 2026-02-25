<?php
require_once '../database/database.php';

$game = $_GET['game'] ?? 'snake';

$query = "SELECT u.username, MAX(s.score) as max_score 
          FROM scores s 
          JOIN users u ON s.user_id = u.id 
          WHERE s.game_type = '$game' 
          GROUP BY u.id 
          ORDER BY max_score DESC 
          LIMIT 10";

$result = $connection->query($query);

$leaderboard = [];
$rank = 1;

while ($row = $result->fetch_assoc()) {
    $leaderboard[] = [
        'rank' => $rank++,
        'username' => $row['username'],
        'score' => $row['max_score']
    ];
}

header('Content-Type: application/json');
echo json_encode($leaderboard);
?>
