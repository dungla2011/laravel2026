#!/bin/bash

# Màu cho output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

REPO_NAME="laravel2026_2"
USERNAME=$(gh api user --jq '.login' 2>/dev/null || echo "unknown")

echo -e "${BLUE}=== GitHub Repository Auto Setup ===${NC}"
echo ""

# Kiểm tra gh CLI
if ! command -v gh &> /dev/null; then
    echo -e "${RED}✗ GitHub CLI (gh) không được cài đặt${NC}"
    exit 1
fi

# Kiểm tra auth
if ! gh auth status &> /dev/null; then
    echo -e "${RED}✗ Chưa login GitHub${NC}"
    exit 1
fi

echo -e "${BLUE}GitHub User: $USERNAME${NC}"
echo ""

# Kiểm tra repo có tồn tại không
echo -e "${BLUE}Checking repository...${NC}"
if gh repo view "$USERNAME/$REPO_NAME" &> /dev/null; then
    echo -e "${YELLOW}⚠ Repo $REPO_NAME đã tồn tại, sẽ xóa để tạo mới${NC}"
    echo ""
    
    # Xóa repo cũ
    echo -e "${BLUE}Deleting old repository...${NC}"
    gh repo delete "$USERNAME/$REPO_NAME" --yes
    
    sleep 2
fi

# Tạo repo mới (không push ngay)
echo -e "${BLUE}Creating new repository $REPO_NAME...${NC}"
gh repo create "$REPO_NAME" --public --source=. --remote=origin

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Created repository $USERNAME/$REPO_NAME${NC}"
    echo -e "  https://github.com/$USERNAME/$REPO_NAME"
    
    # Thêm remote
    echo ""
    echo -e "${BLUE}Setting up git remote...${NC}"
    REPO_URL="https://github.com/$USERNAME/$REPO_NAME.git"
    
    # Xóa remote cũ nếu tồn tại
    git remote remove origin 2>/dev/null
    
    # Thêm remote mới
    git remote add origin "$REPO_URL"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Remote added: $REPO_URL${NC}"
        
        # Cố gắng push
        echo ""
        echo -e "${BLUE}Pushing code to GitHub...${NC}"
        git push -u origin main 2>&1 | tee /tmp/push_output.log
        
        if grep -q "GH001: Large files detected" /tmp/push_output.log; then
            echo ""
            echo -e "${RED}✗ Push blocked: Large files in git history${NC}"
            echo -e "${YELLOW}⚠ Requirement: Clean git history first${NC}"
            echo ""
            echo -e "${BLUE}Solution: Run one of these:${NC}"
            echo -e "${BLUE}1. Install BFG: choco install bfg${NC}"
            echo -e "${BLUE}   Then: bfg --delete-files 'Ping365-v1.1.apk' --delete-folders '.git_lab'${NC}"
            echo -e "${BLUE}2. Or use Git LFS for large files${NC}"
            echo -e "${BLUE}3. Or delete repo and start fresh${NC}"
            exit 1
        else
            echo ""
            echo -e "${GREEN}=== ✓ Thành công! ===${NC}"
            echo -e "${GREEN}✓ Repository: https://github.com/$USERNAME/$REPO_NAME${NC}"
            echo -e "${GREEN}✓ GitHub Actions: https://github.com/$USERNAME/$REPO_NAME/actions${NC}"
            echo ""
            echo -e "${BLUE}PHPUnit CI/CD đã được setup!${NC}"
            echo -e "${BLUE}Tests sẽ chạy tự động khi push code.${NC}"
        fi
    else
        echo -e "${RED}✗ Unable to add remote 'origin'${NC}"
        echo -e "${YELLOW}Repository created but not connected${NC}"
        exit 1
    fi
else
    echo -e "${RED}✗ Tạo repository thất bại${NC}"
    exit 1
fi
