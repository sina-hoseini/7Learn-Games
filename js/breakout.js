// Breakout Game
class BreakoutGame {
    constructor() {
        this.canvas = document.getElementById('gameCanvas');
        this.ctx = this.canvas.getContext('2d');
        
        // Game state
        this.score = 0;
        this.lives = 3;
        this.level = 1;
        this.isPaused = false;
        this.isGameOver = false;

        // Paddle
        this.paddle = {
            x: this.canvas.width / 2 - 40,
            y: this.canvas.height - 20,
            width: 80,
            height: 15,
            speed: 7,
            dx: 0
        };

        // Ball
        this.ball = {
            x: this.canvas.width / 2,
            y: this.canvas.height - 50,
            radius: 7,
            dx: 5,
            dy: -5,
            speed: 5
        };

        // Bricks
        this.bricks = this.initializeBricks();
        this.brickCount = this.bricks.length;

        // Keyboard state
        this.keys = {};

        this.gameLoopInterval = null;
        this.setupEventListeners();
        this.updateDisplay();
    }

    setupEventListeners() {
        document.getElementById('startBtn').addEventListener('click', () => this.startGame());
        document.getElementById('pauseBtn').addEventListener('click', () => this.togglePause());
        document.getElementById('resetBtn').addEventListener('click', () => this.resetGame());

        window.addEventListener('keydown', (e) => {
            this.keys[e.key] = true;
            if (e.key === ' ') {
                e.preventDefault();
                this.togglePause();
            }
        });

        window.addEventListener('keyup', (e) => {
            this.keys[e.key] = false;
        });
    }

    initializeBricks() {
        const bricks = [];
        const rows = 3 + this.level;
        const cols = 8;
        const brickWidth = (this.canvas.width - 20) / cols;
        const brickHeight = 20;
        const startY = 30;

        const colors = ['#FF6B6B', '#4ECDC4', '#FFE66D', '#95E1D3', '#F38181'];

        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                bricks.push({
                    x: 10 + c * brickWidth,
                    y: startY + r * (brickHeight + 5),
                    width: brickWidth - 2,
                    height: brickHeight,
                    active: true,
                    color: colors[r % colors.length]
                });
            }
        }
        return bricks;
    }

    startGame() {
        if (!this.gameLoopInterval) {
            this.isGameOver = false;
            this.isPaused = false;
            document.getElementById('startBtn').disabled = true;
            document.getElementById('pauseBtn').disabled = false;
            this.gameLoopInterval = setInterval(() => this.update(), 1000 / 60);
        }
    }

    togglePause() {
        if (this.gameLoopInterval) {
            this.isPaused = !this.isPaused;
            document.getElementById('pauseBtn').textContent = this.isPaused ? 'ادامه' : 'مکث';
        }
    }

    update() {
        if (this.isPaused) return;

        // Move paddle
        if (this.keys['ArrowLeft'] || this.keys['a'] || this.keys['A']) {
            this.paddle.x -= this.paddle.speed;
        }
        if (this.keys['ArrowRight'] || this.keys['d'] || this.keys['D']) {
            this.paddle.x += this.paddle.speed;
        }

        this.paddle.x = Math.max(0, Math.min(this.canvas.width - this.paddle.width, this.paddle.x));

        // Move ball
        this.ball.x += this.ball.dx;
        this.ball.y += this.ball.dy;

        // Wall collisions
        if (this.ball.x - this.ball.radius < 0 || this.ball.x + this.ball.radius > this.canvas.width) {
            this.ball.dx *= -1;
            this.ball.x = Math.max(this.ball.radius, Math.min(this.canvas.width - this.ball.radius, this.ball.x));
        }

        if (this.ball.y - this.ball.radius < 0) {
            this.ball.dy *= -1;
            this.ball.y = this.ball.radius;
        }

        // Paddle collision
        if (this.ball.y + this.ball.radius > this.paddle.y &&
            this.ball.y - this.ball.radius < this.paddle.y + this.paddle.height &&
            this.ball.x > this.paddle.x &&
            this.ball.x < this.paddle.x + this.paddle.width) {
            
            this.ball.dy *= -1;
            const hitPos = (this.ball.x - this.paddle.x) / this.paddle.width;
            this.ball.dx = (hitPos - 0.5) * 8;
            this.ball.y = this.paddle.y - this.ball.radius;
        }

        // Ball out of bounds
        if (this.ball.y - this.ball.radius > this.canvas.height) {
            this.lives--;
            if (this.lives <= 0) {
                this.endGame();
            } else {
                this.resetBall();
            }
        }

        // Brick collisions
        for (let brick of this.bricks) {
            if (!brick.active) continue;

            if (this.ball.x > brick.x &&
                this.ball.x < brick.x + brick.width &&
                this.ball.y > brick.y &&
                this.ball.y < brick.y + brick.height) {
                
                brick.active = false;
                this.brickCount--;
                this.score += 10;
                this.ball.dy *= -1;

                if (this.brickCount === 0) {
                    this.nextLevel();
                }
            }
        }

        this.updateDisplay();
        this.draw();
    }

    draw() {
        // Draw gradient background
        const gradient = this.ctx.createLinearGradient(0, 0, this.canvas.width, this.canvas.height);
        gradient.addColorStop(0, '#001a33');
        gradient.addColorStop(0.5, '#0d0221');
        gradient.addColorStop(1, '#0a0e27');
        this.ctx.fillStyle = gradient;
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);

        // Draw grid background
        this.ctx.strokeStyle = 'rgba(78, 205, 196, 0.05)';
        this.ctx.lineWidth = 1;
        const gridSize = 30;
        for (let i = 0; i <= this.canvas.width; i += gridSize) {
            this.ctx.beginPath();
            this.ctx.moveTo(i, 0);
            this.ctx.lineTo(i, this.canvas.height);
            this.ctx.stroke();
        }
        for (let i = 0; i <= this.canvas.height; i += gridSize) {
            this.ctx.beginPath();
            this.ctx.moveTo(0, i);
            this.ctx.lineTo(this.canvas.width, i);
            this.ctx.stroke();
        }

        // Draw paddle with gradient
        this.drawPaddleAdvanced();

        // Draw ball with glow
        this.drawBallAdvanced();

        // Draw bricks with effects
        this.drawBricksAdvanced();
    }

    drawPaddleAdvanced() {
        // Paddle glow
        const paddleGlow = this.ctx.createLinearGradient(
            this.paddle.x, this.paddle.y,
            this.paddle.x, this.paddle.y + this.paddle.height
        );
        paddleGlow.addColorStop(0, 'rgba(78, 205, 196, 0.5)');
        paddleGlow.addColorStop(1, 'rgba(78, 205, 196, 0.1)');
        this.ctx.shadowColor = '#4ECDC4';
        this.ctx.shadowBlur = 15;
        this.ctx.fillStyle = paddleGlow;
        this.ctx.fillRect(this.paddle.x - 5, this.paddle.y - 5, this.paddle.width + 10, this.paddle.height + 5);

        // Paddle body with gradient
        const paddle = this.ctx.createLinearGradient(
            this.paddle.x, this.paddle.y,
            this.paddle.x, this.paddle.y + this.paddle.height
        );
        paddle.addColorStop(0, '#4ECDC4');
        paddle.addColorStop(1, '#38a3a1');
        this.ctx.fillStyle = paddle;
        this.ctx.fillRect(this.paddle.x, this.paddle.y, this.paddle.width, this.paddle.height);

        // Paddle border
        this.ctx.strokeStyle = '#4ECDC4';
        this.ctx.lineWidth = 2;
        this.ctx.strokeRect(this.paddle.x, this.paddle.y, this.paddle.width, this.paddle.height);
    }

    drawBallAdvanced() {
        // Ball glow (pulsing)
        const pulse = Math.sin(Date.now() / 100) * 0.3 + 0.7;
        const glowGrad = this.ctx.createRadialGradient(
            this.ball.x, this.ball.y, 0,
            this.ball.x, this.ball.y, this.ball.radius * 2
        );
        glowGrad.addColorStop(0, `rgba(255, 107, 107, ${0.6 * pulse})`);
        glowGrad.addColorStop(1, 'rgba(255, 107, 107, 0)');
        this.ctx.fillStyle = glowGrad;
        this.ctx.beginPath();
        this.ctx.arc(this.ball.x, this.ball.y, this.ball.radius * 2, 0, Math.PI * 2);
        this.ctx.fill();

        // Ball body with gradient
        const ballGrad = this.ctx.createRadialGradient(
            this.ball.x - 2, this.ball.y - 2, 0,
            this.ball.x, this.ball.y, this.ball.radius
        );
        ballGrad.addColorStop(0, '#FF8787');
        ballGrad.addColorStop(1, '#FF3333');
        this.ctx.fillStyle = ballGrad;
        this.ctx.beginPath();
        this.ctx.arc(this.ball.x, this.ball.y, this.ball.radius, 0, Math.PI * 2);
        this.ctx.fill();

        // Ball shine
        this.ctx.fillStyle = 'rgba(255, 255, 255, 0.5)';
        this.ctx.beginPath();
        this.ctx.arc(this.ball.x - 2, this.ball.y - 2, this.ball.radius / 3, 0, Math.PI * 2);
        this.ctx.fill();
    }

    drawBricksAdvanced() {
        this.bricks.forEach(brick => {
            if (brick.active) {
                // Brick glow
                const brickGlow = this.ctx.createLinearGradient(
                    brick.x, brick.y,
                    brick.x + brick.width, brick.y + brick.height
                );
                brickGlow.addColorStop(0, brick.color);
                brickGlow.addColorStop(1, brick.color);
                this.ctx.shadowColor = brick.color;
                this.ctx.shadowBlur = 10;
                this.ctx.fillStyle = brickGlow;
                this.ctx.fillRect(brick.x - 2, brick.y - 2, brick.width + 4, brick.height + 4);

                // Brick body with gradient
                const brickGrad = this.ctx.createLinearGradient(
                    brick.x, brick.y,
                    brick.x, brick.y + brick.height
                );
                brickGrad.addColorStop(0, this.lightenColor(brick.color, 0.2));
                brickGrad.addColorStop(1, brick.color);
                this.ctx.fillStyle = brickGrad;
                this.ctx.fillRect(brick.x, brick.y, brick.width, brick.height);

                // Brick border
                this.ctx.strokeStyle = this.lightenColor(brick.color, 0.3);
                this.ctx.lineWidth = 1.5;
                this.ctx.strokeRect(brick.x, brick.y, brick.width, brick.height);

                // Brick shine
                this.ctx.fillStyle = 'rgba(255, 255, 255, 0.3)';
                this.ctx.fillRect(brick.x + 2, brick.y + 2, brick.width - 4, 3);
            }
        });

        this.ctx.shadowBlur = 0;
    }

    lightenColor(color, percent) {
        const num = parseInt(color.replace("#", ""), 16);
        const amt = Math.round(2.55 * percent);
        const R = (num >> 16) + amt;
        const G = (num >> 8 & 0x00FF) + amt;
        const B = (num & 0x0000FF) + amt;
        return "#" + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
            (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
            (B < 255 ? B < 1 ? 0 : B : 255))
            .toString(16).slice(1);
    }

    resetBall() {
        this.ball.x = this.canvas.width / 2;
        this.ball.y = this.canvas.height - 50;
        this.ball.dx = (Math.random() > 0.5 ? 1 : -1) * 5;
        this.ball.dy = -5;
    }

    nextLevel() {
        this.level++;
        this.bricks = this.initializeBricks();
        this.brickCount = this.bricks.length;
        this.ball.speed += 1;
        this.resetBall();
        alert(`تمام شد!\n\nسطح ${this.level} شروع می‌شود...`);
    }

    updateDisplay() {
        document.getElementById('score').textContent = this.score;
        document.getElementById('lives').textContent = this.lives;
        document.getElementById('level').textContent = this.level;
    }

    endGame() {
        clearInterval(this.gameLoopInterval);
        this.gameLoopInterval = null;
        this.isGameOver = true;
        document.getElementById('startBtn').disabled = false;
        document.getElementById('pauseBtn').disabled = true;

        // Save score
        this.saveScore();

        alert(`بازی تمام شد!\n\nامتیاز نهایی: ${this.score}\nسطح رسیده: ${this.level}`);
    }

    resetGame() {
        clearInterval(this.gameLoopInterval);
        this.gameLoopInterval = null;
        this.score = 0;
        this.lives = 3;
        this.level = 1;
        this.bricks = this.initializeBricks();
        this.brickCount = this.bricks.length;
        this.resetBall();
        this.isPaused = false;
        this.isGameOver = false;
        document.getElementById('startBtn').disabled = false;
        document.getElementById('pauseBtn').disabled = true;
        document.getElementById('pauseBtn').textContent = 'مکث';
        this.updateDisplay();
        this.draw();
    }

    saveScore() {
        fetch('api/save_score.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                game: 'breakout',
                score: this.score
            })
        });
    }
}

// Start game
window.addEventListener('load', () => {
    new BreakoutGame();
});
