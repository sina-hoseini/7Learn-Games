<?php
require_once 'database/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $email = trim($_POST['email'] ?? '');

    if ($action === 'login') {
        if (empty($username) || empty($password)) {
            $error = 'لطفا نام کاربری و رمز را وارد کنید';
        } else {
            $result = $connection->query("SELECT * FROM users WHERE username = '$username'");
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'رمز اشتباه است';
                }
            } else {
                $error = 'کاربری با این نام وجود ندارد';
            }
        }
    } elseif ($action === 'register') {
        if (empty($username) || empty($password) || empty($email)) {
            $error = 'لطفا تمام فیلدها را پر کنید';
        } elseif (strlen($password) < 6) {
            $error = 'رمز باید حداقل 6 کاراکتر باشد';
        } else {
            $check = $connection->query("SELECT id FROM users WHERE username = '$username'");
            if ($check->num_rows > 0) {
                $error = 'این نام کاربری قبلا ثبت‌شده است';
            } else {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                if ($connection->query("INSERT INTO users (username, password, email) VALUES ('$username', '$hashed', '$email')")) {
                    $success = 'حساب با موفقیت ایجاد شد. لطفا وارد شوید.';
                } else {
                    $error = 'خطای پایگاه داده';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود / ثبت‌نام</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <h1>🎮 بازی‌های آنلاین</h1>
            
            <div class="auth-tabs">
                <button class="tab-btn active" onclick="showTab('login')">ورود</button>
                <button class="tab-btn" onclick="showTab('register')">ثبت‌نام</button>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <!-- Login Form -->
            <form id="login" class="auth-form active" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label>نام کاربری:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>رمز عبور:</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn-primary">ورود</button>
            </form>

            <!-- Register Form -->
            <form id="register" class="auth-form" method="POST">
                <input type="hidden" name="action" value="register">
                <div class="form-group">
                    <label>نام کاربری:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>ایمیل:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>رمز عبور:</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn-primary">ثبت‌نام</button>
            </form>

            <p class="back-link"><a href="index.php">بازگشت</a></p>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
