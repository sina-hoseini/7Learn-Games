// Multiplayer Snake Game
class MultiplayerSnakeGame {
    constructor(playerId, playerNumber) {
        this.playerId = playerId;
        this.playerNumber = playerNumber;
        this.canvas = null;
        this.ctx = null;
        this.gridSize = 20;
        this.speed = 8;
        this.score = 0;
        this.level = 1;
        this.isPaused = false;
        this.isGameOver = false;

        this.snake = [
            { x: 10 + (playerNumber * 5), y: 10 + (playerNumber * 5) }
        ];
        this.direction = { x: 1, y: 0 };
        this.nextDirection = { x: 1, y: 0 };
        this.food = null;

        this.gameLoopInterval = null;
    }

    initialize(canvasElement) {
        this.canvas = canvasElement;
        this.ctx = this.canvas.getContext('2d');
        this.food = this.generateFood();
        this.setupEventListeners();
        this.draw();
    }

    setupEventListeners() {
        document.addEventListener('keydown', (e) => this.handleKeyPress(e));
    }

    handleKeyPress(e) {
        if (e.key === ' ') {
            e.preventDefault();
            return;
        }

        const key = e.key.toUpperCase();
        const arrowMap = {
            'ARROWUP': { x: 0, y: -1 },
            'ARROWDOWN': { x: 0, y: 1 },
            'ARROWLEFT': { x: -1, y: 0 },
            'ARROWRIGHT': { x: 1, y: 0 }
        };

        if (arrowMap[key]) {
            e.preventDefault();
            this.setDirection(arrowMap[key]);
        }

        // WASD Support
        if (key === 'W') this.setDirection({ x: 0, y: -1 });
        if (key === 'S') this.setDirection({ x: 0, y: 1 });
        if (key === 'A') this.setDirection({ x: -1, y: 0 });
        if (key === 'D') this.setDirection({ x: 1, y: 0 });
    }

    setDirection(newDir) {
        if (this.direction.x === -newDir.x && this.direction.y === -newDir.y) {
            return;
        }
        this.nextDirection = newDir;
    }

    update() {
        if (this.isPaused) return;

        this.direction = this.nextDirection;

        const head = { ...this.snake[0] };
        head.x += this.direction.x;
        head.y += this.direction.y;

        head.x = (head.x + this.canvas.width / this.gridSize) % (this.canvas.width / this.gridSize);
        head.y = (head.y + this.canvas.height / this.gridSize) % (this.canvas.height / this.gridSize);

        for (let segment of this.snake) {
            if (head.x === segment.x && head.y === segment.y) {
                this.isGameOver = true;
                return;
            }
        }

        this.snake.unshift(head);

        if (head.x === this.food.x && head.y === this.food.y) {
            this.score += 10 * this.level;
            this.food = this.generateFood();
            
            if (this.score % 100 === 0) {
                this.level++;
                this.speed += 2;
            }
        } else {
            this.snake.pop();
        }
    }

    generateFood() {
        let food;
        let valid = false;

        while (!valid) {
            food = {
                x: Math.floor(Math.random() * (this.canvas.width / this.gridSize)),
                y: Math.floor(Math.random() * (this.canvas.height / this.gridSize))
            };

            valid = !this.snake.some(s => s.x === food.x && s.y === food.y);
        }

        return food;
    }

    draw() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

        // Draw gradient background
        const gradient = this.ctx.createLinearGradient(0, 0, this.canvas.width, this.canvas.height);
        gradient.addColorStop(0, '#0f0c29');
        gradient.addColorStop(0.5, '#302b63');
        gradient.addColorStop(1, '#24243e');
        this.ctx.fillStyle = gradient;
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);

        // Draw subtle grid
        this.ctx.strokeStyle = 'rgba(100, 150, 200, 0.1)';
        this.ctx.lineWidth = 0.5;
        for (let i = 0; i <= this.canvas.width; i += this.gridSize) {
            this.ctx.beginPath();
            this.ctx.moveTo(i, 0);
            this.ctx.lineTo(i, this.canvas.height);
            this.ctx.stroke();
        }
        for (let i = 0; i <= this.canvas.height; i += this.gridSize) {
            this.ctx.beginPath();
            this.ctx.moveTo(0, i);
            this.ctx.lineTo(this.canvas.width, i);
            this.ctx.stroke();
        }

        // Draw food with glow
        this.drawFoodWithGlow();

        // Draw snake with gradient and shadow
        this.drawSnakeWithEffects();
    }

    drawFoodWithGlow() {
        const x = this.food.x * this.gridSize;
        const y = this.food.y * this.gridSize;

        // Glow effect
        const glowGrad = this.ctx.createRadialGradient(
            x + this.gridSize / 2, y + this.gridSize / 2, 0,
            x + this.gridSize / 2, y + this.gridSize / 2, this.gridSize
        );
        glowGrad.addColorStop(0, 'rgba(255, 107, 107, 0.8)');
        glowGrad.addColorStop(1, 'rgba(255, 107, 107, 0)');
        this.ctx.fillStyle = glowGrad;
        this.ctx.fillRect(x - this.gridSize / 2, y - this.gridSize / 2, this.gridSize * 2, this.gridSize * 2);

        // Food body
        this.ctx.fillStyle = '#FF6B6B';
        this.ctx.beginPath();
        this.ctx.arc(x + this.gridSize / 2, y + this.gridSize / 2, this.gridSize / 2 - 2, 0, Math.PI * 2);
        this.ctx.fill();

        // Food shine
        this.ctx.fillStyle = 'rgba(255, 255, 255, 0.6)';
        this.ctx.beginPath();
        this.ctx.arc(x + this.gridSize / 2 - 3, y + this.gridSize / 2 - 3, this.gridSize / 6, 0, Math.PI * 2);
        this.ctx.fill();
    }

    drawSnakeWithEffects() {
        this.snake.forEach((segment, index) => {
            const x = segment.x * this.gridSize;
            const y = segment.y * this.gridSize;

            if (index === 0) {
                // Head with glow
                const headGlow = this.ctx.createRadialGradient(
                    x + this.gridSize / 2, y + this.gridSize / 2, 0,
                    x + this.gridSize / 2, y + this.gridSize / 2, this.gridSize
                );
                headGlow.addColorStop(0, 'rgba(46, 204, 113, 0.6)');
                headGlow.addColorStop(1, 'rgba(46, 204, 113, 0)');
                this.ctx.fillStyle = headGlow;
                this.ctx.fillRect(x - this.gridSize / 2, y - this.gridSize / 2, this.gridSize * 2, this.gridSize * 2);

                // Head gradient
                const headGrad = this.ctx.createLinearGradient(x, y, x + this.gridSize, y + this.gridSize);
                headGrad.addColorStop(0, '#2ecc71');
                headGrad.addColorStop(1, '#27ae60');
                this.ctx.fillStyle = headGrad;
                this.ctx.fillRect(x + 1, y + 1, this.gridSize - 2, this.gridSize - 2);

                // Eyes
                this.ctx.fillStyle = '#fff';
                const eyeSize = 3;
                const eyeOffsets = [
                    { dx: 5, dy: 5 },
                    { dx: this.gridSize - 8, dy: 5 }
                ];
                
                eyeOffsets.forEach(offset => {
                    this.ctx.beginPath();
                    this.ctx.arc(x + offset.dx, y + offset.dy, eyeSize, 0, Math.PI * 2);
                    this.ctx.fill();
                });

                // Pupils
                this.ctx.fillStyle = '#000';
                eyeOffsets.forEach(offset => {
                    this.ctx.beginPath();
                    this.ctx.arc(x + offset.dx + 1, y + offset.dy + 1, 1.5, 0, Math.PI * 2);
                    this.ctx.fill();
                });
            } else {
                // Body segments with gradient
                const segmentGrad = this.ctx.createLinearGradient(x, y, x + this.gridSize, y + this.gridSize);
                segmentGrad.addColorStop(0, index < 3 ? '#27ae60' : '#229954');
                segmentGrad.addColorStop(1, '#1e8449');
                this.ctx.fillStyle = segmentGrad;
                this.ctx.fillRect(x + 1, y + 1, this.gridSize - 2, this.gridSize - 2);

                // Segment border
                this.ctx.strokeStyle = 'rgba(255, 255, 255, 0.2)';
                this.ctx.lineWidth = 0.5;
                this.ctx.strokeRect(x + 1, y + 1, this.gridSize - 2, this.gridSize - 2);
            }
        });
    }

    getGameState() {
        return {
            snake: this.snake,
            direction: this.direction,
            food: this.food,
            score: this.score,
            level: this.level,
            isGameOver: this.isGameOver,
            length: this.snake.length
        };
    }
}

// Main multiplayer controller
class MultiplayerSnakeController {
    constructor() {
        this.roomCode = ROOM_CODE;
        this.roomId = ROOM_ID;
        this.userId = USER_ID;
        this.players = {};
        this.localPlayerNumber = null;
        this.isGameRunning = false;
        this.updateInterval = null;
        this.lastUpdate = new Date();
    }

    async initialize() {
        try {
            // Get room info
            const response = await fetch(`api/get_room_info.php?room_code=${this.roomCode}`);
            const data = await response.json();

            if (data.status !== 'success') {
                alert('خطأ: اتاق پیدا نشد');
                window.location.href = 'multiplayer.php';
                return;
            }

            // Find local player number
            const players = data.players;
            this.localPlayerNumber = null;

            for (let player of players) {
                this.players[player.player_number] = {
                    username: player.username,
                    score: player.score,
                    userId: player.user_id
                };

                if (player.user_id === this.userId) {
                    this.localPlayerNumber = player.player_number;
                }
            }

            // Create canvases for each player
            this.createGameBoards(players.length);

            // Initialize player games
            for (let i = 1; i <= players.length; i++) {
                const game = new MultiplayerSnakeGame(this.userId, i);
                const canvas = document.getElementById(`canvas-player-${i}`);
                
                if (canvas) {
                    game.initialize(canvas);
                    this.players[i].game = game;
                }
            }

            this.updatePlayerList();

            // Show waiting message if not all players
            if (players.length < 2) {
                document.getElementById('waitingSection').style.display = 'block';
                document.getElementById('gameSection').style.display = 'none';
                
                // Auto-refresh waiting section
                this.waitingInterval = setInterval(() => {
                    this.checkRoomStatus();
                }, 2000);
            } else {
                document.getElementById('waitingSection').style.display = 'none';
                document.getElementById('gameSection').style.display = 'block';
            }

        } catch (error) {
            console.error('Error initializing:', error);
        }
    }

    async checkRoomStatus() {
        try {
            const response = await fetch(`api/get_room_info.php?room_code=${this.roomCode}`);
            const data = await response.json();

            if (data.status === 'success' && data.players.length >= 2) {
                // Clear waiting interval
                if (this.waitingInterval) {
                    clearInterval(this.waitingInterval);
                }

                // Refresh page
                location.reload();
            } else {
                // Update player list
                this.updatePlayerList();
            }
        } catch (error) {
            console.error('Error checking room status:', error);
        }
    }

    createGameBoards(playerCount) {
        const wrapper = document.getElementById('gamesWrapper');
        wrapper.innerHTML = '';

        for (let i = 1; i <= playerCount; i++) {
            const board = document.createElement('div');
            board.className = 'game-board';
            board.innerHTML = `
                <div class="game-title">🐍 بازیکن ${i}</div>
                <canvas id="canvas-player-${i}" width="400" height="400"></canvas>
                <div class="player-info">
                    <div class="player-stat">
                        <small>امتیاز</small>
                        <strong id="score-player-${i}">0</strong>
                    </div>
                    <div class="player-stat">
                        <small>طول مار</small>
                        <strong id="length-player-${i}">1</strong>
                    </div>
                    <div class="player-stat">
                        <small>سطح</small>
                        <strong id="level-player-${i}">1</strong>
                    </div>
                    <div class="player-stat">
                        <small>وضعیت</small>
                        <strong id="status-player-${i}">در حال بازی</strong>
                    </div>
                </div>
            `;
            wrapper.appendChild(board);
        }
    }

    updatePlayerList() {
        const playerList = document.getElementById('playerList');
        playerList.innerHTML = '';

        for (let [playerNum, player] of Object.entries(this.players)) {
            const badge = document.createElement('div');
            badge.className = 'player-badge';
            badge.textContent = `${playerNum}. ${player.username}`;
            playerList.appendChild(badge);
        }
    }

    async startGame() {
        if (this.isGameRunning) return;

        this.isGameRunning = true;
        document.getElementById('startBtn').disabled = true;
        document.getElementById('pauseBtn').disabled = false;

        // Start game loop for all players
        const gameLoop = setInterval(() => {
            for (let [playerNum, player] of Object.entries(this.players)) {
                if (player.game) {
                    player.game.update();
                    player.game.draw();

                    // Update UI
                    const scoreEl = document.getElementById(`score-player-${playerNum}`);
                    const levelEl = document.getElementById(`level-player-${playerNum}`);
                    const lengthEl = document.getElementById(`length-player-${playerNum}`);
                    const statusEl = document.getElementById(`status-player-${playerNum}`);

                    if (scoreEl) scoreEl.textContent = player.game.score;
                    if (levelEl) levelEl.textContent = player.game.level;
                    if (lengthEl) lengthEl.textContent = player.game.snake.length;
                    
                    if (statusEl) {
                        if (player.game.isGameOver) {
                            statusEl.textContent = '❌ تمام شد';
                        } else if (player.game.isPaused) {
                            statusEl.textContent = '⏸️ متوقف';
                        } else {
                            statusEl.textContent = '▶️ در حال بازی';
                        }
                    }
                }
            }
        }, 1000 / 8); // 8 FPS

        this.gameLoopInterval = gameLoop;

        // Start updates to server
        this.startSyncLoop();
    }

    async startSyncLoop() {
        this.updateInterval = setInterval(async () => {
            if (!this.isGameRunning || this.localPlayerNumber === null) return;

            const localPlayer = this.players[this.localPlayerNumber];
            if (!localPlayer || !localPlayer.game) return;

            try {
                // Send local game state
                const gameData = localPlayer.game.getGameState();
                
                const updateResponse = await fetch('api/update_game_state.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        room_code: this.roomCode,
                        game_data: gameData,
                        score: localPlayer.game.score
                    })
                });

                if (!updateResponse.ok) {
                    console.error('Failed to update game state:', updateResponse.status);
                }

                // Get updates from other players
                const now = new Date();
                const sinceTime = new Date(now.getTime() - 2000); // Last 2 seconds
                const phpDate = sinceTime.getFullYear() + '-' + 
                               String(sinceTime.getMonth() + 1).padStart(2, '0') + '-' + 
                               String(sinceTime.getDate()).padStart(2, '0') + ' ' +
                               String(sinceTime.getHours()).padStart(2, '0') + ':' +
                               String(sinceTime.getMinutes()).padStart(2, '0') + ':' +
                               String(sinceTime.getSeconds()).padStart(2, '0');
                
                const response = await fetch(`api/get_game_updates.php?room_code=${encodeURIComponent(this.roomCode)}&since=${encodeURIComponent(phpDate)}`);
                const data = await response.json();

                if (data.status === 'success') {
                    // Update player states from database
                    for (let state of data.player_states) {
                        if (state.player_number !== this.localPlayerNumber && this.players[state.player_number]) {
                            const player = this.players[state.player_number];
                            player.score = state.score;
                            
                            if (state.game_state && this.players[state.player_number].game) {
                                const game = this.players[state.player_number].game;
                                const newState = state.game_state;
                                
                                if (newState.snake && Array.isArray(newState.snake)) {
                                    game.snake = newState.snake;
                                }
                                if (newState.score !== undefined) {
                                    game.score = newState.score;
                                }
                                if (newState.level !== undefined) {
                                    game.level = newState.level;
                                }
                                if (newState.isGameOver !== undefined) {
                                    game.isGameOver = newState.isGameOver;
                                }
                                if (newState.food && newState.food.x !== undefined) {
                                    game.food = newState.food;
                                }
                                if (newState.direction) {
                                    game.direction = newState.direction;
                                }
                            }
                        }
                    }
                }

                this.lastUpdate = new Date();
            } catch (error) {
                console.error('Error syncing:', error);
            }
        }, 150); // Sync every 150ms for real-time feel
    }

    togglePause() {
        for (let [playerNum, player] of Object.entries(this.players)) {
            if (player.game) {
                player.game.isPaused = !player.game.isPaused;
            }
        }
        
        const pauseBtn = document.getElementById('pauseBtn');
        pauseBtn.textContent = this.players[this.localPlayerNumber]?.game?.isPaused ? 'ادامه' : 'مکث';
    }

    resetGame() {
        if (this.gameLoopInterval) {
            clearInterval(this.gameLoopInterval);
        }
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
        }
        if (this.waitingInterval) {
            clearInterval(this.waitingInterval);
        }

        this.isGameRunning = false;
        document.getElementById('startBtn').disabled = false;
        document.getElementById('pauseBtn').disabled = true;
        document.getElementById('pauseBtn').textContent = 'مکث';

        // Reinitialize games
        for (let [playerNum, player] of Object.entries(this.players)) {
            if (player.game) {
                player.game = new MultiplayerSnakeGame(this.userId, playerNum);
                const canvas = document.getElementById(`canvas-player-${playerNum}`);
                if (canvas) {
                    player.game.initialize(canvas);
                }
            }
        }
    }

    leaveGame() {
        if (confirm('آیا واقعاً می‌خواهید بازی را ترک کنید؟')) {
            window.location.href = 'multiplayer.php';
        }
    }

    exportLeaderboard() {
        const scores = [];
        for (let [playerNum, player] of Object.entries(this.players)) {
            if (player.game) {
                scores.push({
                    rank: playerNum,
                    username: player.username,
                    score: player.game.score,
                    length: player.game.snake.length
                });
            }
        }

        scores.sort((a, b) => b.score - a.score);

        const tbody = document.getElementById('leaderboardBody');
        tbody.innerHTML = scores.map((score, index) => `
            <tr>
                <td>${index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : (index + 1)}</td>
                <td>${score.username}</td>
                <td>${score.score}</td>
                <td>${score.length}</td>
            </tr>
        `).join('');
    }
}

// Initialize
let controller;

document.addEventListener('DOMContentLoaded', async () => {
    controller = new MultiplayerSnakeController();
    await controller.initialize();
});

// Global functions for buttons
function startGame() {
    if (controller) {
        controller.startGame();
    }
}

function togglePause() {
    if (controller) {
        controller.togglePause();
    }
}

function resetGame() {
    if (controller) {
        controller.resetGame();
    }
}

function leaveGame() {
    if (controller) {
        controller.leaveGame();
    }
}

// Refresh leaderboard
setInterval(() => {
    if (controller && controller.isGameRunning) {
        controller.exportLeaderboard();
    }
}, 1000);
