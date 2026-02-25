@echo off
REM Setup script for 7Learn Games - Windows Version

echo.
echo 🎮 7Learn Games - Setup Script (Windows)
echo =========================================
echo.

REM Check PHP
echo Checking PHP...
php -v >nul 2>&1
if %errorlevel% neq 0 (
    color 0c
    echo ✗ PHP not found. Please install PHP 7.4+
    color 07
    pause
    exit /b 1
)
php -v | findstr /R "^PHP"
echo ✓ PHP found

REM Check Git
echo.
echo Checking Git...
git --version >nul 2>&1
if %errorlevel% equ 0 (
    git --version
    echo ✓ Git found
) else (
    echo ⚠ Git not found (Optional for setup)
)

REM Create .env
echo.
echo Creating .env file...
if not exist .env (
    copy .env.example .env >nul
    echo ✓ .env created from .env.example
    echo   Please edit .env with your database credentials
) else (
    echo ✓ .env already exists
)

REM Create directories
echo.
echo Creating directories...
if not exist logs mkdir logs
if not exist cache mkdir cache
echo ✓ Directories created

REM Done
echo.
echo =========================================
echo Setup complete! 🎉
echo =========================================
echo.
echo Next steps:
echo 1. Edit .env with your database settings
echo 2. Run: php -S localhost:8000
echo 3. Open: http://localhost:8000
echo.
echo For more info, see DEPLOY_GUIDE.md
echo.
pause
