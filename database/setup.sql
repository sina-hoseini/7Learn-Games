-- Create Games Database
CREATE DATABASE IF NOT EXISTS games_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE games_db;

-- Create Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create Scores Table
CREATE TABLE IF NOT EXISTS scores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    game_type VARCHAR(50) NOT NULL,
    score INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_game (user_id, game_type),
    INDEX idx_game_score (game_type, score DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create Multiplayer Rooms Table
CREATE TABLE IF NOT EXISTS multiplayer_rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_code VARCHAR(8) UNIQUE NOT NULL,
    game_type VARCHAR(50) NOT NULL,
    created_by INT NOT NULL,
    max_players INT DEFAULT 2,
    status ENUM('waiting', 'playing', 'finished') DEFAULT 'waiting',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_room_code (room_code),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create Room Players Table
CREATE TABLE IF NOT EXISTS room_players (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT NOT NULL,
    user_id INT NOT NULL,
    player_number INT NOT NULL,
    score INT DEFAULT 0,
    status ENUM('waiting', 'playing', 'finished') DEFAULT 'waiting',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES multiplayer_rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_player (room_id, player_number),
    INDEX idx_user_room (user_id, room_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create Game States Table
CREATE TABLE IF NOT EXISTS game_states (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT NOT NULL,
    player_number INT NOT NULL,
    game_data JSON,
    score INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES multiplayer_rooms(id) ON DELETE CASCADE,
    UNIQUE KEY unique_player_state (room_id, player_number),
    INDEX idx_room_id (room_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
