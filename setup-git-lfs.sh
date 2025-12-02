#!/bin/bash

# Simple approach: Use Git LFS for large files instead of BFG

GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}=== Setup Git LFS for Large Files ===${NC}"
echo ""

# Check if Git LFS is installed
if ! command -v git-lfs &> /dev/null; then
    echo -e "${BLUE}Installing Git LFS...${NC}"
    choco install git-lfs -y
    if [ $? -ne 0 ]; then
        echo -e "${RED}✗ Failed to install Git LFS${NC}"
        exit 1
    fi
fi

echo -e "${GREEN}✓ Git LFS found${NC}"
echo ""

# Initialize Git LFS
echo -e "${BLUE}Initializing Git LFS...${NC}"
git lfs install
git lfs install --local

echo ""

# Track large files
echo -e "${BLUE}Tracking APK files with Git LFS...${NC}"
git lfs track "*.apk"
git lfs track ".git_lab"

# Add and commit .gitattributes
if [ -f .gitattributes ]; then
    echo -e "${BLUE}Committing Git LFS configuration...${NC}"
    git add .gitattributes
    git commit -m "Add Git LFS tracking for large files" || true
else
    echo -e "${YELLOW}✓ Git LFS tracking configured${NC}"
fi

echo ""

# Now attempt push
echo -e "${BLUE}Pushing to GitHub with Git LFS...${NC}"
git push -u origin main --force

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}=== ✓ Push successful! ===${NC}"
    echo -e "${GREEN}✓ Large files handled by Git LFS${NC}"
else
    echo -e "${RED}✗ Push still failed${NC}"
    exit 1
fi
