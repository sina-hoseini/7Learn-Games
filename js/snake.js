// Snake Game
class SnakeGame {
    constructor() {
        this.canvas = document.getElementById('gameCanvas');
        this.ctx = this.canvas.getContext('2d');
        this.gridSize = 20;
        this.speed = 8;
        this.score = 0;
        this.bestScore = localStorage.getItem('snakeBestScore') || 0;
        this.level = 1;
        this.isPaused = false;
        this.isGameOver = false;

        this.snake = [
            { x: 10, y: 10 }
        ];
        this.direction = { x: 1, y: 0 };
        this.nextDirection = { x: 1, y: 0 };
        this.food = this.generateFood();

        this.gameLoopInterval = null;
        this.setupEventListeners();
        this.updateDisplay();
    }

    setupEventListeners() {
        document.getElementById('startBtn').addEventListener('click', () => this.startGame());
        document.getElementById('pauseBtn').addEventListener('click', () => this.togglePause());
        document.getElementById('resetBtn').addEventListener('click', () => this.resetGame());

        document.addEventListener('keydown', (e) => this.handleKeyPress(e));
    }

    handleKeyPress(e) {
        if (e.key === ' ') {
            e.preventDefault();
            this.togglePause();
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
        // Prevent reversing into itself
        if (this.direction.x === -newDir.x && this.direction.y === -newDir.y) {
            return;
        }
        this.nextDirection = newDir;
    }

    startGame() {
        if (!this.gameLoopInterval) {
            this.isGameOver = false;
            this.isPaused = false;
            document.getElementById('startBtn').disabled = true;
            document.getElementById('pauseBtn').disabled = false;
            this.gameLoopInterval = setInterval(() => this.update(), 1000 / this.speed);
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

        this.direction = this.nextDirection;

        const head = { ...this.snake[0] };
        head.x += this.direction.x;
        head.y += this.direction.y;

        // Wrap around
        head.x = (head.x + this.canvas.width / this.gridSize) % (this.canvas.width / this.gridSize);
        head.y = (head.y + this.canvas.height / this.gridSize) % (this.canvas.height / this.gridSize);

        // Check collision with self
        for (let segment of this.snake) {
            if (head.x === segment.x && head.y === segment.y) {
                this.endGame();
                return;
            }
        }

        this.snake.unshift(head);

        // Check food collision
        if (head.x === this.food.x && head.y === this.food.y) {
            this.score += 10;
            this.food = this.generateFood();
            
            if (this.score % 50 === 0) {
                this.level++;
                this.speed += 2;
                clearInterval(this.gameLoopInterval);
                this.gameLoopInterval = setInterval(() => this.update(), 1000 / this.speed);
            }
        } else {
            this.snake.pop();
        }

        this.updateDisplay();
        this.draw();
    }

    draw() {
        // Draw gradient background
        const gradient = this.ctx.createLinearGradient(0, 0, this.canvas.width, this.canvas.height);
        gradient.addColorStop(0, '#000033');
        gradient.addColorStop(0.5, '#003366');
        gradient.addColorStop(1, '#000011');
        this.ctx.fillStyle = gradient;
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);

        // Draw animated grid
        this.ctx.strokeStyle = 'rgba(100, 150, 255, 0.15)';
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

        // Draw food with animation
        this.drawFoodWithPulse();

        // Draw snake with advanced graphics
        this.drawSnakeAdvanced();
    }

    drawFoodWithPulse() {
        const x = this.food.x * this.gridSize + this.gridSize / 2;
        const y = this.food.y * this.gridSize + this.gridSize / 2;

        // Pulsing glow effect
        const pulse = Math.sin(Date.now() / 100) * 0.5 + 0.5;
        const glowRadius = this.gridSize / 2 + pulse * 5;

        // Outer glow
        const glowGrad = this.ctx.createRadialGradient(x, y, 0, x, y, glowRadius * 2);
        glowGrad.addColorStop(0, `rgba(255, 100, 100, ${0.6 * pulse})`);
        glowGrad.addColorStop(1, 'rgba(255, 100, 100, 0)');
        this.ctx.fillStyle = glowGrad;
        this.ctx.fillRect(x - glowRadius * 2, y - glowRadius * 2, glowRadius * 4, glowRadius * 4);

        // Food body with gradient
        const foodGrad = this.ctx.createRadialGradient(x - 2, y - 2, 0, x, y, this.gridSize / 2);
        foodGrad.addColorStop(0, '#FF6B6B');
        foodGrad.addColorStop(1, '#FF3333');
        this.ctx.fillStyle = foodGrad;
        this.ctx.beginPath();
        this.ctx.arc(x, y, this.gridSize / 2 - 2, 0, Math.PI * 2);
        this.ctx.fill();

        // Shine effect
        this.ctx.fillStyle = 'rgba(255, 255, 255, 0.7)';
        this.ctx.beginPath();
        this.ctx.arc(x - 3, y - 3, this.gridSize / 5, 0, Math.PI * 2);
        this.ctx.fill();
    }

    drawSnakeAdvanced() {
        this.snake.forEach((segment, index) => {
            const x = segment.x * this.gridSize;
            const y = segment.y * this.gridSize;

            if (index === 0) {
                // Head with advanced gradient
                const headGlow = this.ctx.createRadialGradient(
                    x + this.gridSize / 2, y + this.gridSize / 2, 0,
                    x + this.gridSize / 2, y + this.gridSize / 2, this.gridSize / 2 + 5
                );
                headGlow.addColorStop(0, 'rgba(76, 255, 0, 0.5)');
                headGlow.addColorStop(1, 'rgba(76, 255, 0, 0)');
                this.ctx.fillStyle = headGlow;
                this.ctx.fillRect(x - 5, y - 5, this.gridSize + 10, this.gridSize + 10);

                // Head gradient
                const headGrad = this.ctx.createLinearGradient(x, y, x + this.gridSize, y + this.gridSize);
                headGrad.addColorStop(0, '#00FF00');
                headGrad.addColorStop(0.5, '#00DD00');
                headGrad.addColorStop(1, '#00BB00');
                this.ctx.fillStyle = headGrad;
                this.ctx.fillRect(x + 1, y + 1, this.gridSize - 2, this.gridSize - 2);

                // Head border
                this.ctx.strokeStyle = '#00FF00';
                this.ctx.lineWidth = 2;
                this.ctx.strokeRect(x + 1, y + 1, this.gridSize - 2, this.gridSize - 2);

                // Eyes
                this.drawSnakeEyes(x, y);
            } else {
                // Body segment gradient
                const segmentGrad = this.ctx.createLinearGradient(x, y, x + this.gridSize, y + this.gridSize);
                segmentGrad.addColorStop(0, '#00DD00');
                segmentGrad.addColorStop(1, '#009900');
                this.ctx.fillStyle = segmentGrad;
                this.ctx.fillRect(x + 1, y + 1, this.gridSize - 2, this.gridSize - 2);

                // Segment border (gradually fade)
                const alpha = Math.max(0, 1 - index * 0.1);
                this.ctx.strokeStyle = `rgba(0, 255, 0, ${alpha * 0.5})`;
                this.ctx.lineWidth = 1;
                this.ctx.strokeRect(x + 1, y + 1, this.gridSize - 2, this.gridSize - 2);
            }
        });
    }

    drawSnakeEyes(x, y) {
        // Eyes based on direction
        this.ctx.fillStyle = '#fff';
        this.ctx.strokeStyle = '#000';
        this.ctx.lineWidth = 1;

        const eyeSize = 2.5;
        const eyeOffset = 6;

        // Calculate eye positions based on direction
        let eyes = [];
        if (this.direction.x === 1) { // Moving right
            eyes = [
                { x: x + eyeOffset + 2, y: y + eyeOffset - 2 },
                { x: x + eyeOffset + 2, y: y + eyeOffset + 5 }
            ];
        } else if (this.direction.x === -1) { // Moving left
            eyes = [
                { x: x + 2, y: y + eyeOffset - 2 },
                { x: x + 2, y: y + eyeOffset + 5 }
            ];
        } else if (this.direction.y === -1) { // Moving up
            eyes = [
                { x: x + eyeOffset - 2, y: y + 2 },
                { x: x + eyeOffset + 5, y: y + 2 }
            ];
        } else if (this.direction.y === 1) { // Moving down
            eyes = [
                { x: x + eyeOffset - 2, y: y + eyeOffset + 2 },
                { x: x + eyeOffset + 5, y: y + eyeOffset + 2 }
            ];
        }

        eyes.forEach(eye => {
            this.ctx.beginPath();
            this.ctx.arc(eye.x, eye.y, eyeSize, 0, Math.PI * 2);
            this.ctx.fill();
            this.ctx.stroke();
        });
    }

    generateFood() {
        let food;
        let isOnSnake = true;
        while (isOnSnake) {
            food = {
                x: Math.floor(Math.random() * (this.canvas.width / this.gridSize)),
                y: Math.floor(Math.random() * (this.canvas.height / this.gridSize))
            };
            isOnSnake = this.snake.some(s => s.x === food.x && s.y === food.y);
        }
        return food;
    }

    updateDisplay() {
        document.getElementById('score').textContent = this.score;
        document.getElementById('level').textContent = this.level;
        if (this.score > this.bestScore) {
            this.bestScore = this.score;
            localStorage.setItem('snakeBestScore', this.bestScore);
        }
        document.getElementById('bestScore').textContent = this.bestScore;
    }

    endGame() {
        clearInterval(this.gameLoopInterval);
        this.gameLoopInterval = null;
        this.isGameOver = true;
        document.getElementById('startBtn').disabled = false;
        document.getElementById('pauseBtn').disabled = true;

        // Save score
        this.saveScore();

        alert(`بازی تمام شد!\n\nامتیاز: ${this.score}\nبیشترین: ${this.bestScore}`);
    }

    resetGame() {
        clearInterval(this.gameLoopInterval);
        this.gameLoopInterval = null;
        this.score = 0;
        this.level = 1;
        this.speed = 8;
        this.snake = [{ x: 10, y: 10 }];
        this.direction = { x: 1, y: 0 };
        this.nextDirection = { x: 1, y: 0 };
        this.food = this.generateFood();
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
                game: 'snake',
                score: this.score
            })
        });
    }
}

// Start game
window.addEventListener('load', () => {
    new SnakeGame();
});
