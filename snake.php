<?php
require_once 'database/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بازی مار</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .game-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.2) 100%);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .game-header h1 {
            font-size: 32px;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .stat-box {
            background: rgba(46, 204, 113, 0.1);
            border: 1px solid rgba(46, 204, 113, 0.3);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            color: white;
            min-width: 100px;
        }

        .stat-box small {
            display: block;
            opacity: 0.8;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .stat-box .value {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #2ecc71;
        }

        .game-board {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 15px;
            border: 1px solid rgba(46, 204, 113, 0.2);
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #gameCanvas {
            max-width: 100%;
        }

        .game-controls {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .controls-info {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .controls-info p {
            margin: 0 0 15px 0;
            font-weight: bold;
            color: #333;
        }

        .controls-info ul {
            list-style: none;
            padding: 0;
            text-align: right;
        }

        .controls-info li {
            margin: 8px 0;
            color: #555;
        }

        .controls-buttons {
            display: flex;
            gap: 10px;
            flex-direction: column;
            justify-content: center;
        }

        .controls-buttons button {
            padding: 15px 20px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .controls-buttons button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .btn-primary {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #FFD93D 0%, #F1C40F 100%);
            color: #333;
        }

        .btn-danger {
            background: linear-gradient(135deg, #FF6B6B 0%, #FF3333 100%);
            color: white;
        }

        .btn-primary:disabled, .btn-secondary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .game-rules {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .game-rules h3 {
            color: #2ecc71;
            margin-bottom: 15px;
        }

        .game-rules ul {
            list-style: none;
            padding: 0;
            text-align: right;
        }

        .game-rules li {
            margin: 8px 0;
            color: #555;
            padding-right: 20px;
        }

        @media (max-width: 768px) {
            .game-header {
                flex-direction: column;
                text-align: center;
            }

            .game-header h1 {
                width: 100%;
            }

            .game-controls {
                grid-template-columns: 1fr;
            }

            .stats-row {
                grid-template-columns: repeat(3, 1fr);
            }

            #gameCanvas {
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="game-container">
        <div class="game-header">
            <h1>🐍 بازی مار</h1>
            <div class="stats-row">
                <div class="stat-box">
                    <small>امتیاز</small>
                    <span class="value" id="score">0</span>
                </div>
                <div class="stat-box">
                    <small>بهترین</small>
                    <span class="value" id="bestScore">0</span>
                </div>
                <div class="stat-box">
                    <small>سطح</small>
                    <span class="value" id="level">1</span>
                </div>
            </div>
            <a href="index.php" class="btn-back">🏠 بازگشت</a>
        </div>

        <div class="game-board">
            <canvas id="gameCanvas" width="400" height="400"></canvas>
        </div>

        <div class="game-controls">
            <div class="controls-info">
                <p>🎮 کنترل ها:</p>
                <ul>
                    <li>⬅️ ➡️ ⬆️ ⬇️ یا Arrow Keys</li>
                    <li>WASD برای حرکت متبادل</li>
                    <li>Space برای شروع/مکث</li>
                </ul>
            </div>
            <div class="controls-buttons">
                <button id="startBtn" class="btn-primary">▶️ شروع بازی</button>
                <button id="pauseBtn" class="btn-secondary" disabled>⏸️ مکث</button>
                <button id="resetBtn" class="btn-danger">🔄 شروع دوباره</button>
            </div>
        </div>

        <div class="game-rules">
            <h3>📋 قوانین بازی:</h3>
            <ul>
                <li>✅ غذا (قرمز) را بخور و بزرگتر شو</li>
                <li>❌ به دیوارها و خودت برخورد نکن وگرنه بازی تمام میشود</li>
                <li>⚡ هر 50 امتیاز یک سطح بالاتر برو - سرعت افزایش می‌یابد</li>
                <li>🏆 بهترین امتیازت ذخیره می‌شود - سعی کن رکورد بزنی!</li>
            </ul>
        </div>
    </div>

    <script src="js/snake.js"></script>
</body>
</html>
