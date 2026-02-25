<?php
// Database Connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'games_db';

try {
    $connection = new mysqli($host, $user, $password, $database);
    
    if ($connection->connect_error) {
        die("اتصال به دیتابیس ناموفق: " . $connection->connect_error);
    }
    
    $connection->set_charset("utf8mb4");
} catch (Exception $e) {
    die("خطا: " . $e->getMessage());
}

// Create tables if not exists
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$sql_scores = "CREATE TABLE IF NOT EXISTS scores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    game_type VARCHAR(50) NOT NULL,
    score INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

// Multiplayer tables
$sql_rooms = "CREATE TABLE IF NOT EXISTS multiplayer_rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_code VARCHAR(20) UNIQUE NOT NULL,
    game_type VARCHAR(50) NOT NULL,
    created_by INT NOT NULL,
    status VARCHAR(20) DEFAULT 'waiting',
    max_players INT DEFAULT 2,
    current_players INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
)";

$sql_room_players = "CREATE TABLE IF NOT EXISTS room_players (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT NOT NULL,
    user_id INT NOT NULL,
    player_number INT NOT NULL,
    score INT DEFAULT 0,
    game_state LONGTEXT,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES multiplayer_rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_room_player (room_id, user_id)
)";

$sql_room_updates = "CREATE TABLE IF NOT EXISTS room_game_updates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT NOT NULL,
    player_id INT NOT NULL,
    game_data LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES multiplayer_rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES users(id) ON DELETE CASCADE
)";

$connection->query($sql_users);
$connection->query($sql_scores);
$connection->query($sql_rooms);
$connection->query($sql_room_players);
$connection->query($sql_room_updates);

// Sessions
if (!isset($_SESSION)) {
    session_start();
}
?>
