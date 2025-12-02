#!/bin/bash

# Xóa các file lớn từ lịch sử git bằng git filter-branch

set -e

GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${BLUE}=== Xóa File Lớn Từ Lịch Sử Git ===${NC}"
echo ""

# Stash changes
git stash

echo -e "${BLUE}Stashing changes...${NC}"
echo ""

# Step 1: Remove .git_lab from history
echo -e "${BLUE}1. Removing .git_lab directory...${NC}"
FILTER_BRANCH_SQUELCH_WARNING=1 git filter-branch --force --prune-empty --tree-filter '
  rm -rf .git_lab
' -- --all

# Step 2: Remove large APK file
echo -e "${BLUE}2. Removing Ping365 APK...${NC}"
FILTER_BRANCH_SQUELCH_WARNING=1 git filter-branch --force --prune-empty --index-filter '
  git rm --cached --ignore-unmatch public/download/Ping365-v1.1.apk
' -- --all

echo ""
echo -e "${BLUE}Cleaning up...${NC}"

# Clean up references
rm -rf .git/refs/original/
git reflog expire --expire=now --all
git gc --aggressive --prune=now --force

echo -e "${GREEN}✓ History cleaned${NC}"
echo ""

# Try pushing
echo -e "${BLUE}Pushing to GitHub...${NC}"
git push -u origin main --force

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}=== ✓✓✓ SUCCESS! ===${NC}"
    echo -e "${GREEN}✓ Code successfully pushed!${NC}"
    echo -e "${GREEN}✓ Visit: https://github.com/dungla2011/laravel2026${NC}"
    echo ""
else
    echo ""
    echo -e "${RED}✗ Push failed${NC}"
    exit 1
fi
