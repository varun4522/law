#!/bin/bash

# Law Connectors - Production Server Setup
# Run this on your live server after uploading files

set -e

echo "=================================================="
echo "🚀 Law Connectors - Production Server Setup"
echo "=================================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root
if [[ $EUID -ne 0 ]]; then
   echo -e "${RED}❌ This script must be run as root${NC}"
   echo "Run: sudo bash setup.sh"
   exit 1
fi

WEB_DIR="/var/www/law"
echo -e "${GREEN}✅ Running as root${NC}"

# 1. Update system
echo -e "${YELLOW}📦 Updating system packages...${NC}"
apt-get update -qq
apt-get upgrade -y -qq

# 2. Install dependencies
echo -e "${YELLOW}📦 Installing dependencies...${NC}"
apt-get install -y -qq \
    python3 python3-pip python3-venv \
    apache2 apache2-utils \
    php php-mysql php-cli php-curl \
    mysql-client \
    wget curl git

# 3. Enable Apache modules
echo -e "${YELLOW}🔧 Enabling Apache modules...${NC}"
a2enmod proxy proxy_http rewrite ssl 2>/dev/null || true

# 4. Create web directory if not exists
echo -e "${YELLOW}📁 Setting up web directory...${NC}"
mkdir -p $WEB_DIR
cd $WEB_DIR

# 5. Setup Python virtual environment
echo -e "${YELLOW}🐍 Setting up Python environment...${NC}"
python3 -m venv venv --quiet
source venv/bin/activate

# Upgrade pip
pip install --upgrade pip --quiet 2>/dev/null

# Install requirements
if [ -f "requirements.txt" ]; then
    echo -e "${YELLOW}📚 Installing Python packages...${NC}"
    pip install -r requirements.txt --quiet
else
    echo -e "${RED}⚠️  requirements.txt not found!${NC}"
    exit 1
fi

# 6. Setup systemd service
echo -e "${YELLOW}⚙️  Setting up systemd service...${NC}"
if [ -f "lawai.service" ]; then
    cp lawai.service /etc/systemd/system/law-connectors.service
    
    # Update the service file to use correct path
    sed -i "s|WorkingDirectory=.*|WorkingDirectory=$WEB_DIR|g" /etc/systemd/system/law-connectors.service
    sed -i "s|ExecStart=.*|ExecStart=$WEB_DIR/venv/bin/python $WEB_DIR/app.py|g" /etc/systemd/system/law-connectors.service
    
    systemctl daemon-reload
    systemctl enable law-connectors
    echo -e "${GREEN}✅ Systemd service configured${NC}"
else
    echo -e "${RED}⚠️  lawai.service not found!${NC}"
fi

# 7. Setup Apache configuration
echo -e "${YELLOW}🌐 Setting up Apache configuration...${NC}"
if [ -f "apache-config.conf" ]; then
    cp apache-config.conf /etc/apache2/sites-available/law-connectors.conf
    
    # Disable default site and enable law-connectors
    a2dissite 000-default.conf 2>/dev/null || true
    a2dissite default-ssl.conf 2>/dev/null || true
    a2ensite law-connectors.conf
    
    # Test Apache config
    if apache2ctl configtest 2>&1 | grep -q "Syntax OK"; then
        echo -e "${GREEN}✅ Apache configuration valid${NC}"
    else
        echo -e "${RED}⚠️  Apache configuration has errors!${NC}"
        apache2ctl configtest
    fi
else
    echo -e "${RED}⚠️  apache-config.conf not found!${NC}"
fi

# 8. Set file permissions
echo -e "${YELLOW}🔐 Setting file permissions...${NC}"
chown -R www-data:www-data $WEB_DIR
chmod -R 755 $WEB_DIR
chmod 600 $WEB_DIR/.env 2>/dev/null || true
chmod +x $WEB_DIR/*.sh 2>/dev/null || true

# 9. Create necessary directories
echo -e "${YELLOW}📁 Creating required directories...${NC}"
mkdir -p $WEB_DIR/logs
mkdir -p $WEB_DIR/uploads
chown -R www-data:www-data $WEB_DIR/logs $WEB_DIR/uploads
chmod -R 755 $WEB_DIR/logs $WEB_DIR/uploads

# 10. Start services
echo -e "${YELLOW}🚀 Starting services...${NC}"

systemctl restart law-connectors
sleep 2

systemctl restart apache2
sleep 2

# 11. Verify setup
echo ""
echo -e "${YELLOW}✅ Verifying setup...${NC}"

# Check Flask service
if systemctl is-active --quiet law-connectors; then
    echo -e "${GREEN}✅ Flask service is running${NC}"
else
    echo -e "${RED}❌ Flask service is NOT running${NC}"
    echo "Check logs: sudo journalctl -u law-connectors -n 20"
fi

# Check Apache
if systemctl is-active --quiet apache2; then
    echo -e "${GREEN}✅ Apache is running${NC}"
else
    echo -e "${RED}❌ Apache is NOT running${NC}"
fi

# Check Python packages
echo -e "${GREEN}✅ Python packages installed:${NC}"
pip list --format=columns | grep -E "Flask|openai|python-dotenv|flask-cors" || true

# 12. Final instructions
echo ""
echo "=================================================="
echo -e "${GREEN}✅ SETUP COMPLETE!${NC}"
echo "=================================================="
echo ""
echo "📋 Next Steps:"
echo "1. Update SSL certificate in Apache config:"
echo "   sudo nano /etc/apache2/sites-available/law-connectors.conf"
echo "2. Check Flask is running:"
echo "   sudo systemctl status law-connectors"
echo "3. View Flask logs:"
echo "   sudo journalctl -u law-connectors -f"
echo "4. Visit your site:"
echo "   https://test.1xclube.org"
echo ""
echo "🆘 Troubleshooting:"
echo "   Flask logs: sudo journalctl -u law-connectors -n 50"
echo "   Apache logs: sudo tail -f /var/log/apache2/law-error.log"
echo "   Restart Flask: sudo systemctl restart law-connectors"
echo "   Restart Apache: sudo systemctl restart apache2"
echo ""
