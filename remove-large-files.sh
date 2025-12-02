#!/bin/bash

# Remove large files from git history using filter-branch
# This is more straightforward than BFG for this use case

GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}=== Removing Large Files from Git History ===${NC}"
echo ""

# Backup
BACKUP_DATE=$(date +%Y%m%d_%H%M%S)
echo -e "${BLUE}Creating backup: .git.backup.$BACKUP_DATE${NC}"
cp -r .git ".git.backup.$BACKUP_DATE"
echo -e "${GREEN}✓ Backup created${NC}"
echo ""

# Use git filter-branch to remove large files
echo -e "${BLUE}Removing large files from history...${NC}"

git filter-branch --force --index-filter \
  'git rm --cached --ignore-unmatch public/download/Ping365-v1.1.apk .git_lab' \
  --prune-empty --tag-name-filter cat -- --all 2>&1 | head -20

echo ""
echo -e "${BLUE}Garbage collection...${NC}"
rm -rf .git/refs/original/
git reflog expire --expire=now --all
git gc --aggressive --prune=now

echo ""
echo -e "${GREEN}=== ✓ History cleaned ===${NC}"
echo ""
echo -e "${BLUE}Now attempting push...${NC}"
git push -u origin main --force

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}=== ✓✓✓ SUCCESS! ===${NC}"
    echo -e "${GREEN}✓ Code pushed to https://github.com/dungla2011/laravel2026${NC}"
    echo -e "${GREEN}✓ GitHub Actions workflows active${NC}"
    echo ""
    echo -e "${BLUE}Next: Check GitHub Actions tab to see CI/CD in action${NC}"
else
    echo ""
    echo -e "${RED}✗ Push failed${NC}"
    echo -e "${YELLOW}To restore from backup:${NC}"
    echo -e "${YELLOW}rm -rf .git && mv .git.backup.$BACKUP_DATE .git${NC}"
    exit 1
fi
