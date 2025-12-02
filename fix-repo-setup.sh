#!/bin/bash

# Fix repository setup - delete old and start fresh
# This script cleans up failed repository attempts and tries again

GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}=== Repository Recovery ===${NC}"
echo ""

# Get username
USERNAME=$(gh api user --jq '.login' 2>/dev/null || echo "unknown")
REPO_NAME="laravel2026"

echo -e "${BLUE}GitHub User: $USERNAME${NC}"
echo ""

# Check for failed repos
echo -e "${BLUE}Checking for failed repository attempts...${NC}"
for REPO in laravel2026_1 laravel2026_2 laravel2026_temp; do
    if gh repo view "$USERNAME/$REPO" &> /dev/null; then
        echo -e "${YELLOW}Found: $REPO${NC}"
        echo -e "${BLUE}Deleting $REPO...${NC}"
        gh repo delete "$USERNAME/$REPO" --yes
        sleep 1
        echo -e "${GREEN}✓ Deleted${NC}"
    fi
done

echo ""

# Clean up local git
echo -e "${BLUE}Cleaning up local git configuration...${NC}"
git remote remove origin 2>/dev/null
echo -e "${GREEN}✓ Removed old remote${NC}"

echo ""

# Create final repository
echo -e "${BLUE}Creating final repository: $REPO_NAME...${NC}"
gh repo create "$REPO_NAME" --public --source=. --remote=origin

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Repository created: $USERNAME/$REPO_NAME${NC}"
    
    # Set up remote
    git remote set-url origin "https://github.com/$USERNAME/$REPO_NAME.git"
    
    echo ""
    echo -e "${YELLOW}⚠ Repository created but NOT pushed yet${NC}"
    echo -e "${YELLOW}Large files detected - need cleanup before push${NC}"
    echo ""
    echo -e "${BLUE}Run cleanup script:${NC}"
    echo -e "${GREEN}bash clean-git-history.sh${NC}"
    echo ""
    echo -e "${BLUE}Then push:${NC}"
    echo -e "${GREEN}git push -u origin main --force${NC}"
    
else
    echo -e "${RED}✗ Failed to create repository${NC}"
    exit 1
fi
