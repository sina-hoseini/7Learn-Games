# 🚀 Quick Start Guide - راهنمای سریع

## محلی (Local Setup)

### 1️⃣ Windows + XAMPP

```bash
# فایل‌های بازی را کپی کن
# C:\xampp\htdocs\7Learn.php\games\

# XAMPP را شروع کن
# Control Panel > Start Apache + MySQL

# مرورگر را باز کن
# http://localhost/7Learn.php/games/

# PhpMyAdmin را باز کن
# http://localhost/phpmyadmin/

# Database را ایجاد کن
# 1. phpMyAdmin > جدول نو
# 2. نام: games_db
# 3. Collation: utf8mb4_unicode_ci
# 4. ایجاد کن

# SQL را import کن
# Import > فایل انتخاب: database/setup.sql
# Import کن
```

### 2️⃣ macOS + XAMPP

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/7Learn.php/games

# یا اگر MAMP استفاده می‌کنی
cd /Applications/MAMP/htdocs/7Learn.php/games

# PHP server شروع کن
php -S localhost:8000

# http://localhost:8000 را باز کن
```

### 3️⃣ Linux + LAMP

```bash
# تحت Apache
sudo cp -r games /var/www/html/

# یا تحت Nginx
sudo cp -r games /usr/share/nginx/html/

# MySQL Database
mysql -u root -p
CREATE DATABASE games_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE games_db;
source /var/www/html/games/database/setup.sql;
```

---

## آنلاین Deploy (Online)

### گزینه 1️⃣: Railway.app (⭐ توصیه شده)

```bash
# 1. Railway.app را باز کن
# https://railway.app

# 2. GitHub را connect کن
# Login with GitHub

# 3. Repo را select کن
# Select: 7Learn-Games

# 4. Database را تنظیم کن
# Add MySQL plugin

# 5. Environment variables
# DB_HOST = Railway MySQL Host
# DB_USER = Railway MySQL User
# DB_PASSWORD = Railway MySQL Password
# DB_NAME = games_db

# 6. Deploy شود
# خودکار!

# نتیجه:
# https://your-app-name.railway.app
```

### گزینه 2️⃣: Render.com

```bash
# 1. render.com را باز کن
# https://render.com

# 2. Sign up with GitHub

# 3. Create Web Service
# Connect GitHub repository

# 4. تنظیمات:
# Build Command: (خالی)
# Start Command: php -S 0.0.0.0:$PORT

# 5. Environment:
# DB_HOST, DB_USER, DB_PASSWORD, DB_NAME

# 6. Database
# MySQL یا PostgreSQL (تبدیل شما میتواند بسازید)

# Deploy
# Auto deployment on push
```

### گزینه 3️⃣: Replit.com

```bash
# 1. replit.com را باز کن
# https://replit.com

# 2. Import GitHub repo
# Click: Import from GitHub

# 3. Repository: yourusername/7Learn-Games

# 4. شروع خودکار
# PHP server automatically starts

# 5. Public URL
# https://your-repl-name.repl.co
```

---

## GitHub Setup

### ابتدا GitHub repo ایجاد کن

```bash
# 1. github.com
# New Repository
# Name: 7Learn-Games
# Description: Interactive Online Games Platform
# Public (تا دیگران بتوانند ببینند)

# 2. Local setup
git init
git add .
git commit -m "🎮 Initial commit - 7Learn Games Platform"
git branch -M main
git remote add origin https://github.com/yourusername/7Learn-Games.git
git push -u origin main

# اگر error دادOkay پیش رفت:
# GitHub > Settings > Developer settings > Personal access tokens
# Token ایجاد کن
# git push again
```

---

## ✅ Troubleshooting

### Database connection error

```php
// database/database.php را check کن
// صحیح باشد:
// - DB_HOST
// - DB_USER
// - DB_PASSWORD
// - DB_NAME

// MySQL running است؟
// XAMPP > MySQL: Start
```

### PHP version error

```bash
# PHP version check کنید
php -v

# داشته باشی: 7.4 یا بالاتر
# اگر نه، upgrade کن
```

### Files permission error (Linux/Mac)

```bash
chmod -R 755 /path/to/games
chmod -R 755 /path/to/games/database
```

### 404 Not Found

```
صحیح URL استفاده کن:
- Local: http://localhost/7Learn.php/games/
- Railway: https://app-name.railway.app
```

---

## 🎮 First Game!

1. صفحه اصلی را باز کن
2. **ثبت‌نام** کن
3. **بازی انتخاب** کن
4. **بازی شروع** کن! 🚀

---

## 📱 Multiplayer Test

```
Device 1: Browser 1
- بازی مار > اتاق جدید
- کد: ABC123

Device 2: Browser 2 (دوست)
- بازی مار > پیوستن به اتاق
- کد: ABC123

Start Game! 🎯
```

---

## 🆘 کمک آسماني

- **Issues**: https://github.com/yourusername/7Learn-Games/issues
- **Email**: your.email@example.com
- **Telegram**: @yourusername

---

**Happy Gaming! 🎮✨**
