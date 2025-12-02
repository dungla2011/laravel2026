#!/bin/bash

# Màu cho output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

REPO_NAME="laravel2026"

echo -e "${BLUE}=== GitHub Repository Setup ===${NC}"
echo ""

# Kiểm tra gh CLI
if ! command -v gh &> /dev/null; then
    echo -e "${RED}✗ GitHub CLI (gh) không được cài đặt${NC}"
    echo "Cài đặt từ: https://cli.github.com/"
    exit 1
fi

# Kiểm tra authentication
if ! gh auth status &> /dev/null; then
    echo -e "${RED}✗ Chưa login GitHub${NC}"
    echo "Chạy: gh auth login"
    exit 1
fi

# Lấy GitHub username
USERNAME=$(gh api user --jq '.login')
echo -e "${BLUE}GitHub User: $USERNAME${NC}"
echo ""

# Kiểm tra repo đã tồn tại chưa
echo -e "${BLUE}Checking if repo exists...${NC}"
if gh repo view "$USERNAME/$REPO_NAME" &> /dev/null; then
    echo -e "${RED}✗ Repo $REPO_NAME đã tồn tại!${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Repo chưa tồn tại, sẽ tạo mới${NC}"
echo ""

# Tạo repo công khai
echo -e "${BLUE}Creating repository...${NC}"
gh repo create "$REPO_NAME" --public --source=. --remote=origin --push

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}=== Thành công! ===${NC}"
    echo -e "${GREEN}✓ Repo created: https://github.com/$USERNAME/$REPO_NAME${NC}"
    echo -e "${GREEN}✓ Code pushed to main branch${NC}"
    echo ""
    echo -e "${BLUE}Next steps:${NC}"
    echo "1. Check GitHub Actions: https://github.com/$USERNAME/$REPO_NAME/actions"
    echo "2. View workflow: https://github.com/$USERNAME/$REPO_NAME/blob/main/.github/workflows/phpunit.yml"
else
    echo -e "${RED}✗ Failed to create repository${NC}"
    exit 1
fi
