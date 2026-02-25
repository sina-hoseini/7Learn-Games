# 🤝 مشارکت در 7Learn Games

ممنونیم که علاقمند به مشارکت هستی! 🙌

## چگونه می‌تونی کمک کنی؟

### 1️⃣ Bug Report
اگر bug پیدا کردی:
- GitHub Issues را باز کن
- عنوان واضح دهید
- مراحل تکرار رو شرح دهید
- Expected vs Actual رو نوشته

**Example:**
```
Title: Snake game crashes when score exceeds 9999
Steps:
1. Open snake.php
2. Play until score > 9999
3. Game crashes
Expected: No crash
Actual: Console shows error
```

### 2️⃣ Feature Request
ایده‌ای داری؟
- Issues > New Issue > Feature request
- مفصل توضیح دهید
- چرا مفید است
- مثال‌های محتمل

### 3️⃣ Code Contribution

```bash
# Fork کن (GitHub UI)
# Clone کن
git clone https://github.com/YOUR-USERNAME/7Learn-Games.git

# Branch جدید سازید
git checkout -b feature/awesome-feature

# تغییرات کن
# Test کن

# Commit کن
git commit -m "✨ Add awesome feature"

# Push کن
git push origin feature/awesome-feature

# Pull Request باز کن
```

### 4️⃣ Documentation
- README بهتر کنید
- مستندات اضافه کنید
- توضیحات کد بنویسید
- مثال‌های بیشتر دهید

---

## 📋 Coding Guidelines

### PHP
```php
<?php
// Always use <?php tags

// Use meaningful names
$player_score = 100; // ✓ Good
$ps = 100;           // ✗ Bad

// Comment complex logic
// Check if snake hits wall
if ($head_x < 0 || $head_x >= $grid_width) {
    $game_over = true;
}

// Use type hints (PHP 7.0+)
function calculateScore(int $points, int $multiplier): int {
    return $points * $multiplier;
}
```

### JavaScript
```javascript
// Use const/let, not var
const GRID_SIZE = 20;  // ✓
var gridSize = 20;     // ✗

// Use meaningful names
const playerSnake = [];           // ✓
const s = [];                      // ✗

// Add comments
// Check if snake collides with itself
if (this.checkSelfCollision()) {
    this.endGame();
}

// Use arrow functions
array.map(item => item * 2);      // ✓
array.map(function(item) {        // ✗
    return item * 2;
});
```

### SQL
```sql
-- Use meaningful column names
SELECT username, score FROM users WHERE active = 1;

-- Use backticks for identifiers
SELECT `user_id`, `score` FROM `users`;

-- Format queries
SELECT 
    u.username,
    s.score,
    s.created_at
FROM `users` u
JOIN `scores` s ON u.id = s.user_id
WHERE s.created_at > DATE_SUB(NOW(), INTERVAL 7 DAY);
```

---

## ✅ Commit Messages

```
Format: <emoji> <type>: <message>

Types: feat, fix, docs, style, refactor, test, chore

Examples:
✨ feat: Add pause feature to snake game
🐛 fix: Prevent snake from reversing into itself
📚 docs: Add multiplayer guide
🎨 style: Improve button styling
♻️ refactor: Simplify collision detection
🧪 test: Add game state tests
🔧 chore: Update dependencies
```

---

## 🧪 Testing

قبل از submit:

```bash
# PHP Syntax check
php -l api/save_score.php

# Manual testing
# 1. Test locally
# 2. Play both games
# 3. Test multiplayer
# 4. Check database
# 5. Verify leaderboard
```

---

## 📝 Pull Request Template

```markdown
## Description
Brief explanation of what this PR does

## Type of change
- [ ] Bug fix
- [ ] New feature
- [ ] Documentation
- [ ] Performance improvement

## Testing
- [ ] Tested locally
- [ ] No console errors
- [ ] Mobile responsive
- [ ] Database working

## Screenshots (if applicable)
[Add images here]

## Additional notes
Any additional context
```

---

## 🎯 Priority Issues

Looking for something to work on? These are high-priority:

- [ ] Mobile optimization
- [ ] Performance improvements
- [ ] Documentation
- [ ] Bug fixes labeled `good first issue`
- [ ] Feature requests with ⭐ reactions

---

## 💬 Code Review

We'll review your PR with:
- Code quality
- Consistency with project
- Functionality
- Performance
- Security

Be patient and open to feedback! 😊

---

## 🚀 Release Process

We release when:
- Critical bug fixes ready
- Major features complete
- Dependencies updated
- Documentation complete

Version format: `v1.2.3` (Semantic Versioning)

---

## ❓ Questions?

- Open an Issue
- Comment on PR
- Contact maintainers

---

**Happy Contributing! 🎮✨**

شرایط:
- فارسی و انگلیسی دو زبان پذیرفته
- Respect و تعاون
- No spam یا inappropriate content

**Code of Conduct:**
- درگیری نکن
- احترام بگذار
- صادق باش
- کسی رو بهینه نکن
