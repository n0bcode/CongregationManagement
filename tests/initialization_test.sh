#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # No Color

FAIL=0

echo "Starting Comprehensive Project Initialization Verification..."
echo "-----------------------------------------------------------"

# 1. Check Project Structure
if [ -f "managing-congregation/artisan" ]; then
    echo -e "${GREEN}PASS:${NC} Laravel project structure found"
else
    echo -e "${RED}FAIL:${NC} Laravel project not found at managing-congregation/"
    FAIL=1
fi

# 2. Check App Running (Port 8000)
if curl -s --head --request GET http://localhost:8000 | grep "200 OK" > /dev/null; then
    echo -e "${GREEN}PASS:${NC} App is running on port 8000"
else
    echo -e "${RED}FAIL:${NC} App is not responding on port 8000"
    FAIL=1
fi

# 3. Check Mailpit (Port 8025)
if curl -s --head --request GET http://localhost:8025 | grep "200 OK" > /dev/null; then
    echo -e "${GREEN}PASS:${NC} Mailpit is running on port 8025"
else
    echo -e "${RED}FAIL:${NC} Mailpit is not responding on port 8025"
    FAIL=1
fi

# 4. Check Database Connection via Artisan
# We use docker exec to run this inside the container to ensure it tests the container's connection
if docker compose -f managing-congregation/compose.yaml exec -T laravel.test php artisan db:monitor > /dev/null 2>&1; then
     echo -e "${GREEN}PASS:${NC} Database connection successful"
else
     # Try a fallback check if db:monitor isn't available or fails
     if docker compose -f managing-congregation/compose.yaml exec -T laravel.test php artisan migrate:status > /dev/null 2>&1; then
        echo -e "${GREEN}PASS:${NC} Database connection successful (verified via migrate:status)"
     else
        echo -e "${RED}FAIL:${NC} Database connection failed"
        FAIL=1
     fi
fi

# 5. Check Breeze Installation (Controller Check)
if [ -f "managing-congregation/app/Http/Controllers/Auth/AuthenticatedSessionController.php" ]; then
    echo -e "${GREEN}PASS:${NC} Breeze (Auth Controllers) found"
else
    echo -e "${RED}FAIL:${NC} Breeze Auth Controllers missing"
    FAIL=1
fi

# 6. Check Frontend Assets (Manifest)
if [ -f "managing-congregation/public/build/manifest.json" ]; then
    echo -e "${GREEN}PASS:${NC} Frontend assets compiled (manifest.json found)"
else
    echo -e "${RED}FAIL:${NC} Frontend assets not compiled (manifest.json missing)"
    FAIL=1
fi

echo "-----------------------------------------------------------"
if [ $FAIL -eq 0 ]; then
    echo -e "${GREEN}ALL CHECKS PASSED${NC}"
    exit 0
else
    echo -e "${RED}SOME CHECKS FAILED${NC}"
    exit 1
fi
