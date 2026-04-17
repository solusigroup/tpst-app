#!/bin/bash

# Configuration
APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PHP_BIN="php"
COMPOSER_BIN="composer"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to handle errors
cleanup() {
    if [ $? -ne 0 ]; then
        echo -e "\n${RED}❌ Deployment failed! Attempting to bring the application back online...${NC}"
        $PHP_BIN artisan up || true
        echo -e "${YELLOW}⚠️  Please check the error messages above.${NC}"
    fi
}

# Trap any exit signal to run cleanup
trap cleanup EXIT

set -e

echo -e "${GREEN}🚀 Bismillahirahmanirrrahiim Starting Robust Deployment...${NC}"

# 1. Dependency Checks
echo -e "${YELLOW}🔍 Checking dependencies...${NC}"
if ! command -v $PHP_BIN &> /dev/null; then
    echo -e "${RED}Error: PHP is not installed or not in PATH.${NC}"
    exit 1
fi

if ! command -v $COMPOSER_BIN &> /dev/null; then
    echo -e "${RED}Error: Composer is not installed or not in PATH.${NC}"
    exit 1
fi

# 2. Pull latest changes
echo -e "${YELLOW}📥 Pulling latest changes from Git...${NC}"
git pull origin main

# 3. Install/Update Composer dependencies (while site is still live)
echo -e "${YELLOW}📦 Installing Composer dependencies...${NC}"
export COMPOSER_ALLOW_SUPERUSER=1
$COMPOSER_BIN install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 4. Enter Maintenance Mode (Right before destructive/heavy operations)
echo -e "${YELLOW}🚧 Entering Maintenance Mode...${NC}"
$PHP_BIN artisan down --refresh=15 --retry=60 || true

# 5. Run database migrations
echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
$PHP_BIN artisan migrate --force

# 6. Clear and Cache everything
echo -e "${YELLOW}⚡ Optimizing application...${NC}"
$PHP_BIN artisan optimize
$PHP_BIN artisan view:cache
$PHP_BIN artisan event:cache

# 7. Ensure storage link exists
echo -e "${YELLOW}🔗 Ensuring storage link...${NC}"
$PHP_BIN artisan storage:link || true

# 8. Exit Maintenance Mode
echo -e "${GREEN}✅ Application is live!${NC}"
$PHP_BIN artisan up

echo -e "${GREEN}🚀 Alhamdulillah Deployment finished successfully!${NC}"
