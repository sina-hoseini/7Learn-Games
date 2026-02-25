#!/bin/bash
# Setup script for 7Learn Games

echo "🎮 7Learn Games - Setup Script"
echo "=============================="
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Check PHP
echo "${YELLOW}Checking PHP...${NC}"
if command -v php &> /dev/null
then
    PHP_VERSION=$(php -v | head -n 1)
    echo "${GREEN}✓ PHP installed: $PHP_VERSION${NC}"
else
    echo "${RED}✗ PHP not found. Please install PHP 7.4+${NC}"
    exit 1
fi

# Check MySQL
echo ""
echo "${YELLOW}Checking MySQL...${NC}"
if command -v mysql &> /dev/null
then
    echo "${GREEN}✓ MySQL installed${NC}"
else
    echo "${YELLOW}⚠ MySQL not found in PATH (OK if using remote DB)${NC}"
fi

# Check Git
echo ""
echo "${YELLOW}Checking Git...${NC}"
if command -v git &> /dev/null
then
    GIT_VERSION=$(git --version)
    echo "${GREEN}✓ Git installed: $GIT_VERSION${NC}"
else
    echo "${YELLOW}⚠ Git not found (Optional for setup)${NC}"
fi

# Create .env
echo ""
echo "${YELLOW}Creating .env file...${NC}"
if [ ! -f .env ]; then
    cp .env.example .env
    echo "${GREEN}✓ .env created from .env.example${NC}"
    echo "  Please edit .env with your database credentials"
else
    echo "${GREEN}✓ .env already exists${NC}"
fi

# Create logs directory
echo ""
echo "${YELLOW}Creating directories...${NC}"
mkdir -p logs
mkdir -p cache
chmod 755 logs cache
echo "${GREEN}✓ Directories created${NC}"

# Start PHP server
echo ""
echo "${YELLOW}Starting PHP development server...${NC}"
echo "${GREEN}✓ PHP server ready!${NC}"
echo ""
echo "=============================="
echo "Setup complete! 🎉"
echo "=============================="
echo ""
echo "Next steps:"
echo "1. Edit .env with your database settings"
echo "2. Run: php -S localhost:8000"
echo "3. Open: http://localhost:8000"
echo ""
echo "For more info, see DEPLOY_GUIDE.md"
