<?php
require_once 'database/database.php';

$isLoggedIn = isset($_SESSION['user_id']);
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎮 مرکز بازی‌های آنلاین</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        header {
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
            border-radius: 15px !important;
        }

        header h1 {
            color: #667eea !important;
            font-size: 32px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: white;
            font-size: 28px;
            margin-bottom: 30px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .game-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .game-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .game-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        }

        .game-icon {
            font-size: 80px;
            margin-bottom: 15px;
            animation: bounce 3s infinite;
        }

        .game-card h3 {
            color: #333;
            font-size: 24px;
            margin-bottom: 12px;
        }

        .game-card p {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .game-stats {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #667eea;
            font-weight: bold;
        }

        .game-card .btn-play {
            display: inline-block;
            width: calc(50% - 6px);
            margin-right: 12px;
            padding: 12px 15px !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .game-card .btn-play:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .game-card .btn-leaderboard {
            display: inline-block;
            width: calc(50% - 6px);
            padding: 12px 15px !important;
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%) !important;
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
        }

        .game-card .btn-leaderboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(243, 156, 18, 0.5);
        }

        .user-stats {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 40px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
        }

        .stat-card h4 {
            color: #667eea;
            margin-bottom: 12px;
            font-size: 16px;
        }

        .stat-card p {
            font-size: 32px;
            font-weight: bold;
            color: #2ecc71;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        footer {
            text-align: center;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 40px;
            padding: 20px;
        }

        .multiplayer-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 40px;
            text-align: center;
        }

        .multiplayer-section a {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 18px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(46, 204, 113, 0.3);
        }

        .multiplayer-section a:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(46, 204, 113, 0.5);
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
            }

            header h1 {
                margin-bottom: 15px;
            }

            .games-grid {
                grid-template-columns: 1fr;
            }

            .game-card .btn-play,
            .game-card .btn-leaderboard {
                width: 100% !important;
                margin: 8px 0 !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🎮 مرکز بازی‌های آنلاین</h1>
            <div class="user-info">
                <?php if ($isLoggedIn): ?>
                    <span>خوش آمدید: <strong><?php echo htmlspecialchars($username); ?></strong></span>
                    <a href="api/logout.php" class="btn-logout">🚪 خروج</a>
                <?php else: ?>
                    <a href="auth.php" class="btn-login">🔑 ورود / ثبت‌نام</a>
                <?php endif; ?>
            </div>
        </header>

        <main>
            <section class="games-section">
                <h2>🎯 بازی‌های موجود</h2>
                
                <div class="games-grid">
                    <!-- Game 1: Snake -->
                    <div class="game-card">
                        <div class="game-icon">🐍</div>
                        <h3>بازی مار</h3>
                        <p>بازی کلاسیک و جذاب مار! غذا را بخور، بزرگتر شو و دشمنان آن را کنار بزن!</p>
                        <div class="game-stats">
                            سطح سختی: ⭐⭐
                        </div>
                        <?php if ($isLoggedIn): ?>
                            <a href="snake.php" class="btn-play">▶️ شروع</a>
                            <a href="leaderboard.php?game=snake" class="btn-leaderboard">🏆 رتبه‌بندی</a>
                        <?php else: ?>
                            <a href="auth.php" class="btn-play">🔑 وارد شوید</a>
                        <?php endif; ?>
                    </div>

                    <!-- Game 2: Brick Breaker -->
                    <div class="game-card">
                        <div class="game-icon">🧱</div>
                        <h3>شکست آجر</h3>
                        <p>توپ کنترل‌شونده را حرکت دهید و تمام آجرهای رنگ‌پریز را بشکنید!</p>
                        <div class="game-stats">
                            سطح سختی: ⭐⭐⭐
                        </div>
                        <?php if ($isLoggedIn): ?>
                            <a href="breakout.php" class="btn-play">▶️ شروع</a>
                            <a href="leaderboard.php?game=breakout" class="btn-leaderboard">🏆 رتبه‌بندی</a>
                        <?php else: ?>
                            <a href="auth.php" class="btn-play">🔑 وارد شوید</a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <?php if ($isLoggedIn): ?>
            <!-- Multiplayer Section -->
            <section class="multiplayer-section">
                <h2 style="margin-bottom: 20px;">👥 بازی‌های مولتی‌پلیر</h2>
                <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 20px; font-size: 16px;">
                    با دوستانت آنلاین بازی کن! یک اتاق ایجاد کن یا به یک اتاق موجود بپیوند
                </p>
                <a href="multiplayer.php">👥 وارد بخش مولتی‌پلیر</a>
            </section>

            <!-- User Stats Section -->
            <section class="user-stats">
                <h2>📊 آمار شما</h2>
                <div class="stats-grid" id="userStats">
                    <div class="stat-card">
                        <h4>🐍 بیشترین امتیاز - مار</h4>
                        <p id="snakeScore">0</p>
                    </div>
                    <div class="stat-card">
                        <h4>🧱 بیشترین امتیاز - آجر</h4>
                        <p id="breakoutScore">0</p>
                    </div>
                </div>
            </section>
            <?php endif; ?>
        </main>

        <footer>
            <p>© 2026 مرکز بازی‌های آنلاین 🎮 | ✨ سازنده: <?php echo htmlspecialchars($username ?: 'مهمان'); ?></p>
        </footer>
    </div>

    <script>
        <?php if ($isLoggedIn): ?>
        fetch('api/get_user_stats.php')
            .then(r => r.json())
            .then(data => {
                document.getElementById('snakeScore').textContent = (data.snake || 0).toLocaleString();
                document.getElementById('breakoutScore').textContent = (data.breakout || 0).toLocaleString();
            });
        <?php endif; ?>
    </script>
</body>
</html>
