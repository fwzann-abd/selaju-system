#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}=== LMS Melesat API Testing ===${NC}\n"

# Check if server is running
echo -e "${YELLOW}1. Checking server status...${NC}"
curl -s http://localhost:8000 > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Server is running${NC}\n"
else
    echo -e "${RED}✗ Server is not running. Please start it with: php artisan serve${NC}\n"
    exit 1
fi

# Get total classrooms count
echo -e "${YELLOW}2. Testing API: GET /api/lms/classrooms (without auth)${NC}"
echo "Expected: 401 Unauthorized"
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/api/lms/classrooms)
if [ "$RESPONSE" = "401" ]; then
    echo -e "${GREEN}✓ API requires authentication (Status: $RESPONSE)${NC}\n"
else
    echo -e "${RED}✗ Unexpected status: $RESPONSE${NC}\n"
fi

# Check routes
echo -e "${YELLOW}3. Checking registered routes...${NC}"
php artisan route:list --path=lms | head -20
echo -e ""

echo -e "${GREEN}=== Setup Complete ===${NC}"
echo ""
echo "Next steps:"
echo "1. Make sure frontend is built: npm run build ✓"
echo "2. Start the server: php artisan serve"
echo "3. Visit the page: http://localhost:8000/admin/lms/classrooms"
echo "4. Login with your super_admin account"
echo ""
echo "NOTE: The API requires authenticated super_admin, so test from within the dashboard."
