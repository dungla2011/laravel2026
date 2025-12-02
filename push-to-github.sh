#!/bin/bash

# Màu cho output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

REPO_NAME="laravel2026"
USERNAME="dungla2011"

echo -e "${BLUE}=== Push to GitHub ===${NC}"
echo ""

# Kiểm tra .git tồn tại
if [ ! -d ".git" ]; then
    echo -e "${BLUE}Initializing git repository...${NC}"
    git init
    git branch -M main
fi

# Kiểm tra remote origin
if git remote get-url origin &> /dev/null; then
    echo -e "${BLUE}Updating remote origin...${NC}"
    git remote set-url origin "https://github.com/$USERNAME/$REPO_NAME.git"
else
    echo -e "${BLUE}Adding remote origin...${NC}"
    git remote add origin "https://github.com/$USERNAME/$REPO_NAME.git"
fi

echo -e "${GREEN}✓ Remote: https://github.com/$USERNAME/$REPO_NAME${NC}"
echo ""

# Add all files
echo -e "${BLUE}Adding files...${NC}"
git add .

# Commit
echo -e "${BLUE}Committing changes...${NC}"
git commit -m "Push: Laravel 2026 CI/CD setup with PHPUnit and GitHub Actions"

# Push to main
echo -e "${BLUE}Pushing to GitHub...${NC}"
git push -u origin main

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}=== Thành công! ===${NC}"
    echo -e "${GREEN}✓ Code pushed to: https://github.com/$USERNAME/$REPO_NAME${NC}"
    echo -e "${GREEN}✓ CI/CD workflow: https://github.com/$USERNAME/$REPO_NAME/actions${NC}"
    echo ""
    echo -e "${BLUE}PHPUnit tests sẽ chạy tự động khi push!${NC}"
else
    echo -e "${RED}✗ Failed to push${NC}"
    exit 1
fi
