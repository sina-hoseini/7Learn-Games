# 📤 خطوات Push کردن به GitHub

## مرحله 1️⃣: GitHub Repository ایجاد‌کن

1. **github.com** را باز کن
2. وارد اکانتت شو (یا ثبت‌نام کن)
3. **New Repository** کلیک کن

```
Repository name: 7Learn-Games
Description: Interactive Online Games Platform
Public / Private: Public (بهتر است)
✅ Add a README file (NO - ما قبلاً داریم)
✅ Add .gitignore (NO - ما قبلاً داریم)
```

4. **Create Repository** کلیک کن

---

## مرحله 2️⃣: Git را نصب کن

### Windows
```bash
# https://git-scm.com/download/win
# دانلود و نصب

# بعد، Cmd یا PowerShell را باز کن و:
git --version
```

### macOS
```bash
brew install git
# یا از: https://git-scm.com/download/mac
```

### Linux
```bash
sudo apt-get install git
```

---

## مرحله 3️⃣: Git کنفیگ کن

اول بار برای اولین بار:

```bash
git config --global user.name "نام کاملت"
git config --global user.email "ایمیلت@example.com"
```

---

## مرحله 4️⃣: Project را Initialize کن

```bash
# به فولدر games برو
cd C:\xampp\htdocs\7Learn.php\games

# یا اگر سسته‌تر است:
cd /Applications/XAMPP/xamppfiles/htdocs/7Learn.php/games

# Git initialize کن
git init

# تمامی فایل‌ها رو add کن
git add .

# اولین commit
git commit -m "🎮 Initial commit - 7Learn Games Platform"

# branch رو rename کن (به main)
git branch -M main
```

---

## مرحله 5️⃣: Remote کن

```bash
# URL رو اضافه کن (جایگزین yourusername)
git remote add origin https://github.com/yourusername/7Learn-Games.git

# یا SSH (اگر SSH کیلید ساختی):
git remote add origin git@github.com:yourusername/7Learn-Games.git

# چک کن:
git remote -v
```

---

## مرحله 6️⃣: Push کن

```bash
# اولین push:
git push -u origin main

# بعدش:
git push
```

### اگر error دادOkay:

#### HTTPS Token (اسان‌تر)
```
GitHub > Settings > Developer settings > Personal access tokens
> Generate new token
> Scopes: repo, user
> Token رو کپی کن (یک بار فقط نمایش می‌دهند!)

# Terminal میخواهد pass:
Username: yourusername
Password: (token رو paste کن)
```

#### SSH Key (بهتر، یک‌بار تنظیم)
```bash
# کلید درست کن:
ssh-keygen -t ed25519 -C "ایمیلت@example.com"
# Enter Enter Enter

# Public key رو copy کن:
cat ~/.ssh/id_ed25519.pub

# GitHub > Settings > SSH and GPG keys
# New SSH key
# عنوان: My Computer
# Key: (paste کن)

# اکنون push خودکار کار می‌کند!
git push
```

---

## مرحله 7️⃣: Deploy کن

### گزینه A: Railway (⭐ توصیه شده)

```bash
# railway.app را باز کن
# GitHub میکند login

# New project
# Deploy from GitHub repo
# Select: 7Learn-Games

# Railway خودکار:
# - PHP Detect می‌کند
# - MySQL database درست می‌کند
# - Compose می‌شود
# - Deploy می‌شود

# نتیجه: https://your-app.railway.app
```

### گزینه B: Render.com

```bash
# render.com را باز کن
# GitHub Login

# New Web Service
# Connect Repository
# Select: 7Learn-Games

# تنظیمات:
# Build Command: (خالی)
# Start Command: php -S 0.0.0.0:$PORT

# خودکار deploy
```

### گزینه C: Heroku (رایگان!)

```bash
# heroku.com را باز کن - WAIT!
# Heroku دیگر رایگان نیست (2022 از)
# Railway یا Render بهتر است
```

---

## مرحله 8️⃣: در آینده

هر بار که تغییری می‌دهی:

```bash
# تمام تغییرات رو stage کن
git add .

# Commit کن
git commit -m "📝 توصیف تغییر"

# Push کن
git push

# Railway/Render خودکار deploy می‌کند!
```

---

## 🎯 مفید Commands

```bash
# Status چک کن
git status

# آخرین commits
git log --oneline

# تغییرات را ببین
git diff

# تغییر رو undo کن
git checkout -- filename

# آخرین commit رو undo کن
git reset --soft HEAD~1

# Branch جدید سازید
git checkout -b feature/awesome
git push -u origin feature/awesome

# Branch حذف کن
git branch -d feature/awesome
```

---

## 🆘 Troubleshooting

| مشکل | حل |
|------|-----|
| `fatal: not a git repository` | `git init` رو اول اجرا کن |
| `Permission denied (publickey)` | SSH key بساز یا HTTPS استفاده کن |
| `Your branch is ahead of 'origin/main'` | `git push` کن |
| `Merge conflict` | `git pull` کن و conflict حل کن |

---

## ✅ تمام!

اکنون:
- ✅ GitHub Repository ایجاد شده
- ✅ Project Push شده
- ✅ Deploy شده
- ✅ آنلاین Live!

**آدرس:** https://your-app.railway.app 🚀

---

**سوالی؟** GitHub Issues رو باز کن! 💬
