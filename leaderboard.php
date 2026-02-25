<?php
require_once 'database/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$game = $_GET['game'] ?? 'snake';
$gameTitle = $game === 'snake' ? 'بازی مار' : 'شکست آجر';
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول رتبه‌بندی - <?php echo htmlspecialchars($gameTitle); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .leaderboard-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .leaderboard-container h1 {
            color: #667eea;
            text-align: center;
            margin-bottom: 30px;
            font-size: 32px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .game-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .game-tabs a {
            padding: 12px 24px;
            border: 2px solid #667eea;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            background: white;
            color: #667eea;
        }

        .game-tabs a:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .game-tabs a.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
        }

        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .leaderboard-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
        }

        .leaderboard-table th {
            padding: 18px;
            text-align: right;
            border: none;
            font-size: 16px;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .leaderboard-table tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .leaderboard-table tbody tr:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        }

        .leaderboard-table td {
            padding: 16px 18px;
            text-align: right;
            color: #333;
        }

        .rank-medal {
            display: inline-block;
            font-size: 24px;
            margin-left: 10px;
            animation: pulse 2s infinite;
        }

        .rank-number {
            color: #667eea;
            font-weight: bold;
            font-size: 18px;
        }

        .username {
            font-weight: 500;
            color: #333;
        }

        .score {
            font-weight: bold;
            color: #2ecc71;
            font-size: 18px;
        }

        .back-link {
            text-align: center;
            margin-top: 30px;
        }

        .back-link a {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .back-link a:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .empty-message {
            text-align: center;
            padding: 40px 20px;
            color: #999;
            font-size: 18px;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        @media (max-width: 600px) {
            .leaderboard-container {
                padding: 20px;
            }

            .leaderboard-table th,
            .leaderboard-table td {
                padding: 12px 10px;
                font-size: 14px;
            }

            .game-tabs {
                flex-direction: column;
            }

            .game-tabs a {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="leaderboard-container">
        <h1>🏆 جدول رتبه‌بندی</h1>

        <div class="game-tabs">
            <a href="leaderboard.php?game=snake" class="<?php echo $game === 'snake' ? 'active' : ''; ?>">🐍 بازی مار</a>
            <a href="leaderboard.php?game=breakout" class="<?php echo $game === 'breakout' ? 'active' : ''; ?>">🧱 شکست آجر</a>
        </div>

        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th style="width: 30%;">رتبه</th>
                    <th style="width: 40%;">نام کاربری</th>
                    <th style="width: 30%;">امتیاز</th>
                </tr>
            </thead>
            <tbody id="leaderboardBody">
                <tr>
                    <td colspan="3" class="empty-message">⏳ در حال بارگذاری...</td>
                </tr>
            </tbody>
        </table>

        <p class="back-link">
            <a href="index.php">🏠 بازگشت به صفحه اصلی</a>
        </p>
    </div>

    <script>
        const game = '<?php echo htmlspecialchars($game); ?>';
        
        fetch(`api/get_leaderboard.php?game=${game}`)
            .then(r => r.json())
            .then(data => {
                const tbody = document.getElementById('leaderboardBody');
                tbody.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="empty-message">📊 هیچ امتیازی برای این بازی وجود ندارد</td></tr>';
                    return;
                }

                const medals = ['🥇', '🥈', '🥉'];

                data.forEach((entry, index) => {
                    const medal = medals[index] || '📍';
                    const isTopThree = index < 3;
                    const row = document.createElement('tr');
                    if (isTopThree) {
                        row.style.background = 'linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%)';
                    }
                    
                    row.innerHTML = `
                        <td>
                            <span class="rank-medal">${medal}</span>
                            <span class="rank-number">#${entry.rank}</span>
                        </td>
                        <td><span class="username">${escapeHtml(entry.username)}</span></td>
                        <td><span class="score">${entry.score.toLocaleString()}</span></td>
                    `;
                    tbody.appendChild(row);
                });
            });

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    </script>
</body>
</html>
