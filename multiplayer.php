<?php
require_once 'database/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بازی‌های مولتی‌پلیر</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .multiplayer-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 40px;
        }

        .action-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .action-card h3 {
            margin-top: 0;
            font-size: 24px;
        }

        .action-card p {
            margin: 10px 0 20px 0;
            opacity: 0.9;
        }

        .btn-action {
            background: white;
            color: #667eea;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: transform 0.2s;
        }

        .btn-action:hover {
            transform: scale(1.05);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 30px;
            border: 1px solid #888;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            direction: rtl;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            margin: 0;
        }

        .close {
            color: #aaa;
            float: left;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .button-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-primary, .btn-secondary {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        .btn-primary {
            background-color: #667eea;
            color: white;
        }

        .btn-secondary {
            background-color: #ccc;
            color: black;
        }

        .rooms-section {
            margin-top: 40px;
        }

        .rooms-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .room-card {
            background: white;
            border: 2px solid #667eea;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .room-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transform: translateY(-5px);
        }

        .room-code {
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }

        .room-info {
            color: #666;
            margin: 8px 0;
        }

        .room-game {
            font-size: 24px;
            margin: 10px 0;
        }

        .room-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-top: 10px;
        }

        .status-waiting {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-playing {
            background-color: #d4edda;
            color: #155724;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: #667eea;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="multiplayer-container">
        <div class="game-header">
            <h1>🎮 بازی‌های مولتی‌پلیر</h1>
            <div>
                <span>خوش آمدید: <strong><?php echo htmlspecialchars($username); ?></strong></span>
                <a href="index.php" class="btn-back">بازگشت</a>
            </div>
        </div>

        <div id="alertBox"></div>

        <div class="action-buttons">
            <div class="action-card">
                <h3>🆕 ایجاد اتاق جدید</h3>
                <p>یک بازی جدید شروع کنید و دوستان را دعوت کنید</p>
                <button class="btn-action" onclick="openCreateRoomModal()">ایجاد اتاق</button>
            </div>

            <div class="action-card">
                <h3>🔗 پیوستن به اتاق</h3>
                <p>کد اتاق را وارد کنید و به یک بازی موجود بپیوندید</p>
                <button class="btn-action" onclick="openJoinRoomModal()">وارد شوید</button>
            </div>
        </div>

        <div class="rooms-section">
            <h2>اتاق‌های موجود</h2>
            <div id="roomsList" class="rooms-list">
                <div class="loading">در حال بارگذاری...</div>
            </div>
        </div>
    </div>

    <!-- Create Room Modal -->
    <div id="createRoomModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>ایجاد اتاق جدید</h2>
                <span class="close" onclick="closeCreateRoomModal()">&times;</span>
            </div>
            <form id="createRoomForm" onsubmit="createRoom(event)">
                <div class="form-group">
                    <label for="gameType">انتخاب بازی:</label>
                    <select id="gameType" required>
                        <option value="snake">🐍 بازی مار</option>
                        <option value="breakout">🧱 شکست آجر</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="maxPlayers">حداکثر بازیکنان:</label>
                    <select id="maxPlayers" required>
                        <option value="2">2 بازیکن</option>
                        <option value="3">3 بازیکن</option>
                        <option value="4">4 بازیکن</option>
                    </select>
                </div>

                <div class="button-group">
                    <button type="button" class="btn-secondary" onclick="closeCreateRoomModal()">لغو</button>
                    <button type="submit" class="btn-primary">ایجاد اتاق</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Join Room Modal -->
    <div id="joinRoomModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>پیوستن به اتاق</h2>
                <span class="close" onclick="closeJoinRoomModal()">&times;</span>
            </div>
            <form id="joinRoomForm" onsubmit="joinRoom(event)">
                <div class="form-group">
                    <label for="roomCode">کد اتاق:</label>
                    <input type="text" id="roomCode" placeholder="مثال: ABC12345" required>
                </div>

                <div class="button-group">
                    <button type="button" class="btn-secondary" onclick="closeJoinRoomModal()">لغو</button>
                    <button type="submit" class="btn-primary">پیوستن</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const API_URL = 'api/';

        // Modal functions
        function openCreateRoomModal() {
            document.getElementById('createRoomModal').style.display = 'block';
        }

        function closeCreateRoomModal() {
            document.getElementById('createRoomModal').style.display = 'none';
        }

        function openJoinRoomModal() {
            document.getElementById('joinRoomModal').style.display = 'block';
        }

        function closeJoinRoomModal() {
            document.getElementById('joinRoomModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const createModal = document.getElementById('createRoomModal');
            const joinModal = document.getElementById('joinRoomModal');
            
            if (event.target === createModal) {
                createModal.style.display = 'none';
            }
            if (event.target === joinModal) {
                joinModal.style.display = 'none';
            }
        }

        // Create room
        async function createRoom(event) {
            event.preventDefault();
            
            const gameType = document.getElementById('gameType').value;
            const maxPlayers = parseInt(document.getElementById('maxPlayers').value);

            try {
                const response = await fetch(API_URL + 'create_room.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        game_type: gameType,
                        max_players: maxPlayers
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    showAlert(`اتاق ${data.room_code} ایجاد شد!`, 'success');
                    closeCreateRoomModal();
                    
                    // Redirect to game based on type
                    if (gameType === 'snake') {
                        window.location.href = 'multiplayer_snake.php?room_code=' + data.room_code;
                    } else if (gameType === 'breakout') {
                        window.location.href = 'multiplayer_breakout.php?room_code=' + data.room_code;
                    }
                } else {
                    showAlert('خطا: ' + data.message, 'error');
                }
            } catch (error) {
                showAlert('خطا در ایجاد اتاق', 'error');
                console.error('Error:', error);
            }
        }

        // Join room
        async function joinRoom(event) {
            event.preventDefault();
            
            const roomCode = document.getElementById('roomCode').value.trim().toUpperCase();

            try {
                const response = await fetch(API_URL + 'join_room.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        room_code: roomCode
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    showAlert('به اتاق پیوستید!', 'success');
                    closeJoinRoomModal();
                    loadRooms();
                } else {
                    showAlert('خطا: ' + data.message, 'error');
                }
            } catch (error) {
                showAlert('خطا در پیوستن به اتاق', 'error');
                console.error('Error:', error);
            }
        }

        // Load rooms
        async function loadRooms() {
            try {
                const response = await fetch(API_URL + 'get_available_rooms.php');
                const data = await response.json();

                const roomsList = document.getElementById('roomsList');
                
                if (data.status === 'success' && data.rooms.length > 0) {
                    roomsList.innerHTML = data.rooms.map(room => `
                        <div class="room-card" onclick="quickJoinRoom('${room.room_code}')">
                            <div class="room-game">
                                ${room.game_type === 'snake' ? '🐍' : '🧱'}
                            </div>
                            <div class="room-code">${room.room_code}</div>
                            <div class="room-info">بازیکنان: ${room.current_players}/${room.max_players}</div>
                            <div class="room-info">ایجاد کننده: ${room.creator}</div>
                            <span class="room-status ${room.status === 'waiting' ? 'status-waiting' : 'status-playing'}">
                                ${room.status === 'waiting' ? '⏳ در انتظار' : '▶️ درحال بازی'}
                            </span>
                        </div>
                    `).join('');
                } else {
                    roomsList.innerHTML = '<div class="loading">اتاقی موجود نیست</div>';
                }
            } catch (error) {
                console.error('Error loading rooms:', error);
            }
        }

        // Quick join room
        async function quickJoinRoom(roomCode) {
            try {
                const response = await fetch(API_URL + 'join_room.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        room_code: roomCode
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    // Get room info to determine which game to load
                    const roomInfo = await fetch(API_URL + 'get_room_info.php?room_code=' + roomCode);
                    const roomData = await roomInfo.json();
                    
                    if (roomData.status === 'success') {
                        const gameType = roomData.room.game_type;
                        if (gameType === 'snake') {
                            window.location.href = 'multiplayer_snake.php?room_code=' + roomCode;
                        } else if (gameType === 'breakout') {
                            window.location.href = 'multiplayer_breakout.php?room_code=' + roomCode;
                        }
                    }
                } else {
                    showAlert('خطا: ' + data.message, 'error');
                }
            } catch (error) {
                showAlert('خطا در پیوستن', 'error');
                console.error('Error:', error);
            }
        }

        // Show alert
        function showAlert(message, type) {
            const alertBox = document.getElementById('alertBox');
            alertBox.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
            setTimeout(() => {
                alertBox.innerHTML = '';
            }, 5000);
        }

        // Load rooms on page load
        loadRooms();

        // Refresh rooms every 3 seconds
        setInterval(loadRooms, 3000);
    </script>
</body>
</html>
