<?php
require_once 'database/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$room_code = isset($_GET['room_code']) ? strtoupper(trim($_GET['room_code'])) : '';

if (!$room_code) {
    header('Location: multiplayer.php');
    exit;
}

// Get room_id from room_code
$sql_room = "SELECT id FROM multiplayer_rooms WHERE room_code = ?";
$stmt_room = $connection->prepare($sql_room);
$stmt_room->bind_param("s", $room_code);
$stmt_room->execute();
$result_room = $stmt_room->get_result();

if ($result_room->num_rows === 0) {
    header('Location: multiplayer.php');
    exit;
}

$room_data = $result_room->fetch_assoc();
$room_id = $room_data['id'];
$stmt_room->close();

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بازی مار - مولتی‌پلیر</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .multiplayer-game-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .game-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .game-header h1 {
            font-size: 28px;
            color: #333;
            margin: 0;
        }

        .room-info {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
        }

        .room-info strong {
            font-size: 18px;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .games-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(460px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .game-board {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .game-board:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .game-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 12px;
            padding: 10px;
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
            border-radius: 8px;
            font-size: 16px;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        #gamesWrapper canvas {
            display: block;
            background: linear-gradient(135deg, #000033 0%, #003366 50%, #000011 100%);
            border: 3px solid #2ecc71;
            border-radius: 10px;
            margin: 0 auto;
            box-shadow: 0 0 20px rgba(46, 204, 113, 0.2),
                        inset 0 0 10px rgba(46, 204, 113, 0.1);
            image-rendering: crisp-edges;
            transition: box-shadow 0.3s ease;
            width: 100%;
            max-width: 420px;
            height: auto;
        }

        #gamesWrapper canvas:hover {
            box-shadow: 0 0 30px rgba(46, 204, 113, 0.4),
                        inset 0 0 15px rgba(46, 204, 113, 0.2);
        }

        .player-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 12px;
        }

        .player-stat {
            background: linear-gradient(135deg, #f0f0f0 0%, #e8e8e8 100%);
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }

        .player-stat:hover {
            background: linear-gradient(135deg, #e8e8e8 0%, #e0e0e0 100%);
            border-color: #2ecc71;
        }

        .player-stat small {
            display: block;
            color: #666;
            margin-bottom: 6px;
            font-size: 12px;
            font-weight: 500;
        }

        .player-stat strong {
            display: block;
            font-size: 20px;
            color: #2ecc71;
            font-weight: bold;
        }

        .controls-section {
            background: rgba(255, 255, 255, 0.98);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .button-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .btn-play, .btn-pause, .btn-reset, .btn-exit {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-play:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .btn-play {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
        }

        .btn-pause {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
        }

        .btn-reset {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
        }

        .btn-exit {
            background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
            color: white;
        }

        .btn-play:disabled, .btn-pause:disabled, .btn-reset:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
            background-color: #6c757d;
            color: white;
        }

        .btn-play:disabled, .btn-pause:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .controls-info {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .controls-info p {
            margin-bottom: 10px;
            font-weight: bold;
        }

        .controls-info ul {
            list-style: none;
            padding: 0;
            text-align: right;
        }

        .controls-info li {
            margin: 5px 0;
        }

        .leaderboard {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .leaderboard h3 {
            margin-top: 0;
            color: #667eea;
        }

        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
        }

        .leaderboard-table th {
            background-color: #667eea;
            color: white;
            padding: 10px;
            text-align: right;
        }

        .leaderboard-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .leaderboard-table tr:hover {
            background-color: #f5f5f5;
        }

        .medal {
            font-size: 24px;
            margin-right: 10px;
        }

        .waiting-players {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }

        .player-list {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .player-badge {
            background: #667eea;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
        }

        .status-message {
            text-align: center;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .status-waiting {
            background: #fff3cd;
            color: #856404;
        }

        .status-playing {
            background: #d4edda;
            color: #155724;
        }

        .status-finished {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="multiplayer-game-container">
        <div class="game-header">
            <h1>🐍 بازی مار - مولتی‌پلیر</h1>
            <div class="room-info">
                کد اتاق: <strong><?php echo htmlspecialchars($room_code); ?></strong>
            </div>
            <a href="multiplayer.php" class="btn-exit">بازگشت به لابی</a>
        </div>

        <div id="statusMessage"></div>

        <div id="waitingSection" class="waiting-players">
            <p>⏳ در انتظار بازیکنان دیگر...</p>
            <div class="player-list" id="playerList"></div>
        </div>

        <div id="gameSection" style="display: none;">
            <div class="leaderboard">
                <h3>📊 امتیازات زنده</h3>
                <table class="leaderboard-table">
                    <thead>
                        <tr>
                            <th>رتبه</th>
                            <th>بازیکن</th>
                            <th>امتیاز</th>
                            <th>طول مار</th>
                        </tr>
                    </thead>
                    <tbody id="leaderboardBody">
                    </tbody>
                </table>
            </div>

            <div class="games-wrapper" id="gamesWrapper">
            </div>

            <div class="controls-section">
                <div class="controls-info">
                    <p>🎮 کنترل ها:</p>
                    <ul>
                        <li>⬅️ ➡️ ⬆️ ⬇️ یا Arrow Keys برای حرکت</li>
                        <li>Space برای شروع/مکث</li>
                        <li>R برای شروع دوباره</li>
                    </ul>
                </div>

                <div class="button-group">
                    <button class="btn-play" id="startBtn" onclick="startGame()">شروع بازی</button>
                    <button class="btn-pause" id="pauseBtn" onclick="togglePause()" disabled>مکث</button>
                    <button class="btn-reset" id="resetBtn" onclick="resetGame()">شروع دوباره</button>
                    <button class="btn-exit" onclick="leaveGame()">ترک بازی</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/multiplayer_snake.js"></script>
    <script>
        const ROOM_CODE = "<?php echo htmlspecialchars($room_code); ?>";
        const ROOM_ID = <?php echo $room_id; ?>;
        const USER_ID = <?php echo $user_id; ?>;
    </script>
</body>
</html>
