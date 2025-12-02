#!/bin/bash

# Clean git history from large files
# This script removes large files that exceed GitHub limits

GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}=== Git History Cleanup ===${NC}"
echo ""

# Check if BFG is installed
if ! command -v bfg &> /dev/null; then
    echo -e "${YELLOW}⚠ BFG Repo-Cleaner not found${NC}"
    echo -e "${BLUE}Installing via Chocolatey...${NC}"
    choco install bfg -y
    if [ $? -ne 0 ]; then
        echo -e "${RED}✗ Failed to install BFG${NC}"
        echo -e "${YELLOW}Please install manually: choco install bfg${NC}"
        exit 1
    fi
fi

echo -e "${BLUE}✓ BFG found${NC}"
echo ""

# Backup original repo
echo -e "${BLUE}Backing up original repository...${NC}"
BACKUP_DIR=".git.backup.$(date +%s)"
cp -r .git "$BACKUP_DIR"
echo -e "${GREEN}✓ Backup created: $BACKUP_DIR${NC}"
echo ""

# Remove large files
echo -e "${BLUE}Removing large APK files from history...${NC}"
bfg --delete-files "Ping365-v1.1.apk" --no-blob-protection

echo -e "${BLUE}Removing .git_lab directory from history...${NC}"
bfg --delete-folders ".git_lab" --no-blob-protection

# Garbage collection
echo ""
echo -e "${BLUE}Running garbage collection...${NC}"
git reflog expire --expire=now --all
git gc --prune=now --aggressive --force

echo ""
echo -e "${GREEN}=== ✓ Git history cleaned ===${NC}"
echo ""
echo -e "${BLUE}Next steps:${NC}"
echo -e "${BLUE}1. Review changes: git log --oneline${NC}"
echo -e "${BLUE}2. Force push: git push origin main --force${NC}"
echo ""
echo -e "${YELLOW}Note: If anything goes wrong, restore from backup:${NC}"
echo -e "${YELLOW}rm -rf .git && mv $BACKUP_DIR .git${NC}"
echo ""
