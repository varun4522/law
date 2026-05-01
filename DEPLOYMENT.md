# Law Connectors - Live Server Deployment Guide

## 📋 Prerequisites
- Linux server (Ubuntu 20.04 LTS or later recommended)
- Node.js / PHP support for web files
- Apache web server with mod_proxy enabled
- Python 3.8+
- SSH access to server

## 🚀 Deployment Steps

### 1. Upload Files to Server
```bash
# Upload all files to /var/www/law
scp -r law/ user@72.61.170.123:/var/www/law
```

### 2. Set Permissions
```bash
sudo chown -R www-data:www-data /var/www/law
sudo chmod -R 755 /var/www/law
sudo chmod -R 775 /var/www/law/{lib,student,admin}
```

### 3. Create Python Virtual Environment
```bash
cd /var/www/law
python3 -m venv venv
source venv/bin/activate
pip install --upgrade pip
pip install -r requirements.txt
```

### 4. Configure Environment Variables
```bash
# Copy example and edit with your API key
cp .env.example .env
nano .env

# Update with your OpenRouter API key
OPENROUTER_API_KEY=your_actual_key_here
```

### 5. Setup Apache Reverse Proxy
```bash
# Enable required Apache modules
sudo a2enmod proxy
sudo a2enmod proxy_http
sudo a2enmod rewrite
sudo a2enmod ssl

# Copy Apache configuration
sudo cp apache-config.conf /etc/apache2/sites-available/law-connectors.conf

# Edit and update SSL certificate paths
sudo nano /etc/apache2/sites-available/law-connectors.conf
# Update: SSLCertificateFile and SSLCertificateKeyFile with your paths

# Disable default site and enable law-connectors
sudo a2dissite 000-default.conf
sudo a2ensite law-connectors.conf

# Test Apache config
sudo apache2ctl configtest
# Should output: Syntax OK

# Restart Apache
sudo systemctl restart apache2
```

### 6. Setup Systemd Service for Flask Auto-Start
```bash
# Copy service file
sudo cp lawai.service /etc/systemd/system/law-connectors.service

# Edit if needed
sudo nano /etc/systemd/system/law-connectors.service

# Enable and start the service
sudo systemctl daemon-reload
sudo systemctl enable law-connectors
sudo systemctl start law-connectors

# Check status
sudo systemctl status law-connectors
```

### 7. Configure Database
```bash
# If database needs setup
mysql -h 72.61.170.123 -u your_db_user -p < lib/law.sql
```

### 8. Verify Deployment

✅ **Check Flask Backend**
```bash
curl http://127.0.0.1:5000/
```

✅ **Check PHP Pages**
```bash
curl https://test.1xclube.org/index.php
curl https://test.1xclube.org/chat.php
```

✅ **Check Service Status**
```bash
sudo systemctl status law-connectors
```

## 🔄 Keeping Flask App Running Always

The Flask backend will now:
- ✅ Auto-start on server boot
- ✅ Auto-restart if it crashes
- ✅ Run in background as a service
- ✅ Log errors to systemd journal

### View Flask Logs
```bash
sudo journalctl -u law-connectors -f
```

### Restart Flask Service
```bash
sudo systemctl restart law-connectors
```

### Stop Flask Service
```bash
sudo systemctl stop law-connectors
```

## 📁 Server Directory Structure
```
/var/www/law/
├── app.py                 # Flask backend
├── .env                   # Environment variables (create from .env.example)
├── requirements.txt       # Python dependencies
├── lawai.service         # Systemd service file
├── chat.php              # Chat page (updated to use /api)
├── index.php             # Landing page
├── login.php             # Login page
├── lib/
│   ├── db.php           # Database config (updated for 72.61.170.123)
│   └── ...
├── student/
├── admin/
├── assets/
└── venv/                 # Python virtual environment
```

## 🔒 Security Checklist

- [ ] .env file is in root (not accessible from web)
- [ ] SSL certificate is installed (HTTPS working)
- [ ] Database password is secure and in .env
- [ ] API key is secure and in .env
- [ ] Web server runs as www-data user (non-root)
- [ ] File permissions are correct (755 for dirs, 644 for files)

## 🆘 Troubleshooting

### Flask app won't start
```bash
# Check for errors
sudo journalctl -u law-connectors -n 20
# Check Python version
python3 --version
# Check if port 5000 is in use
sudo ss -tulpn | grep 5000
```

### API returns 404 Not Found
- Check if Flask is running: `sudo systemctl status law-connectors`
- Check Apache logs: `sudo tail -f /var/log/apache2/law-error.log`
- Check Flask port: `curl http://127.0.0.1:5000/`

### Database connection fails
- Verify 72.61.170.123 is accessible: `ping 72.61.170.123`
- Check credentials in .env

### Reverse proxy not working
- Test Apache syntax: `sudo apache2ctl configtest`
- Check Apache modules: `sudo apache2ctl -M | grep proxy`
- Check Apache logs: `sudo tail -f /var/log/apache2/law-error.log`

## 📝 Key Changes Made

1. **chat.php**: Changed iframe from `http://127.0.0.1:5000/` → `https://test.1xclube.org/api`
2. **app.py**: Changed binding from `localhost` → `0.0.0.0` and debug mode off
3. **lib/db.php**: Changed DB_HOST from `localhost` → `72.61.170.123`
4. Created **lawai.service** for systemd auto-start
5. Created **apache-config.conf** for Apache reverse proxy setup
6. Created **.env.example** for environment variables

## ⚡ Quick Deployment Commands (SSH)

Copy and paste these commands in order to deploy:

```bash
# 1. Connect to your server
ssh user@72.61.170.123

# 2. Create web directory
sudo mkdir -p /var/www/law
cd /var/www/law

# 3. Upload files (from your local machine)
# Ctrl+D to exit SSH, then:
scp -r /path/to/law/* user@72.61.170.123:/var/www/law/

# 4. Back in SSH:
cd /var/www/law

# 5. Set permissions
sudo chown -R www-data:www-data /var/www/law
sudo chmod -R 755 /var/www/law

# 6. Setup Python environment
python3 -m venv venv
source venv/bin/activate
pip install --upgrade pip
pip install -r requirements.txt

# 7. Create .env file
cp .env.example .env
nano .env
# Edit and add your OPENROUTER_API_KEY

# 8. Setup systemd service
sudo cp lawai.service /etc/systemd/system/law-connectors.service
sudo systemctl daemon-reload
sudo systemctl enable law-connectors
sudo systemctl start law-connectors

# 9. Setup Apache
sudo a2enmod proxy proxy_http rewrite ssl
sudo cp apache-config.conf /etc/apache2/sites-available/law-connectors.conf
sudo nano /etc/apache2/sites-available/law-connectors.conf
# Update SSL certificate paths
sudo a2dissite 000-default.conf
sudo a2ensite law-connectors.conf
sudo apache2ctl configtest
sudo systemctl restart apache2

# 10. Verify everything is working
sudo systemctl status law-connectors
curl http://127.0.0.1:5000/
```
