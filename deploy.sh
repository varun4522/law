#!/bin/bash

# Law Connectors Deployment Script
# Run this on your live server with SSH

set -e

echo "🚀 Law Connectors Deployment Script"
echo "===================================="

# Check if running as root
if [[ $EUID -ne 0 ]]; then
   echo "❌ This script must be run as root (use: sudo bash deploy.sh)"
   exit 1
fi

# Variables
WEB_DIR="/var/www/law"
PYTHON_VER=$(python3 --version)

echo "✅ Running as root"
echo "✅ Python version: $PYTHON_VER"

# 1. Install dependencies
echo ""
echo "📦 Installing system dependencies..."
apt-get update -qq
apt-get install -y -qq python3-pip python3-venv apache2 apache2-utils libapache2-mod-proxy-html libxml2-dev

# 2. Enable Apache modules
echo "🔧 Enabling Apache modules..."
a2enmod proxy 2>/dev/null || true
a2enmod proxy_http 2>/dev/null || true
a2enmod rewrite 2>/dev/null || true
a2enmod ssl 2>/dev/null || true

# 3. Create web directory
echo "📁 Creating web directory..."
mkdir -p $WEB_DIR

# 4. Setup Python virtual environment
echo "🐍 Setting up Python environment..."
cd $WEB_DIR
python3 -m venv venv --quiet
source venv/bin/activate
pip install --upgrade pip --quiet
pip install -r requirements.txt --quiet

# 5. Setup systemd service
echo "⚙️  Setting up systemd service..."
cp lawai.service /etc/systemd/system/law-connectors.service
systemctl daemon-reload

# 6. Setup Apache configuration
echo "🌐 Setting up Apache configuration..."
cp apache-config.conf /etc/apache2/sites-available/law-connectors.conf
a2dissite 000-default.conf 2>/dev/null || true
a2ensite law-connectors.conf

# 7. Test Apache config
echo "✔️  Testing Apache configuration..."
apache2ctl configtest

# 8. Set file permissions
echo "🔐 Setting file permissions..."
chown -R www-data:www-data $WEB_DIR
chmod -R 755 $WEB_DIR

# 9. Start services
echo "🚀 Starting services..."
systemctl enable law-connectors
systemctl start law-connectors
systemctl restart apache2

# 10. Verify
echo ""
echo "✅ Deployment Complete!"
echo ""
echo "📋 Next Steps:"
echo "1. Edit /var/www/law/.env with your OpenRouter API key"
echo "2. Update SSL certificate paths in /etc/apache2/sites-available/law-connectors.conf"
echo "3. Verify Flask is running: sudo systemctl status law-connectors"
echo "4. Check API: curl http://127.0.0.1:5000/"
echo "5. Visit: https://test.1xclube.org/chat.php"
echo ""
echo "🆘 View logs with:"
echo "   sudo journalctl -u law-connectors -f"
echo "   sudo tail -f /var/log/apache2/law-error.log"
