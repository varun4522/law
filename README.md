# 🚀 Law Connectors - Live Server Deployment

## ✅ Complete! Your code is ready for production server.

### 📦 What You Need to Upload

Copy ONLY these folders/files to your server at `/var/www/law`:

```
✅ app.py                          # Flask backend
✅ chat.php                        # Chat page (auto-detects localhost/server)
✅ index.php                       # Landing page
✅ login.php                       # Login page
✅ signup.php                      # Signup page
✅ .env                            # Environment variables (ADD YOUR API KEY!)
✅ requirements.txt                # Python dependencies
✅ setup.sh                        # Automated setup script
✅ lawai.service                   # Systemd service file
✅ apache-config.conf              # Apache reverse proxy config
✅ admin/                          # Admin panel files
✅ student/                        # Student panel files
✅ expert/                         # Expert panel files
✅ lib/                            # PHP libraries
✅ includes/                       # PHP includes
✅ assets/                         # CSS, JS, images
✅ templates/                      # Flask HTML templates
✅ static/                         # Flask static files
```

### ❌ DO NOT Upload These (Development/Temporary Only)

```
❌ .venv/                          # Virtual environment (created on server)
❌ .git/                           # Git repository
❌ DEPLOYMENT.md                   # Old deployment guide
❌ QUICKSTART.md                   # Development guide
❌ nginx-config.conf               # We use Apache, not Nginx
❌ deploy.sh                       # Old deploy script (use setup.sh)
❌ __pycache__/                    # Python cache
❌ *.pyc                           # Compiled Python files
❌ .vscode/                        # VS Code settings
```

## 🎯 3-Step Server Deployment

### Step 1: Upload Files
```bash
# From your local machine
scp -r /path/to/law/* user@72.61.170.123:/var/www/law/

# OR use SFTP/FTP to upload files manually
```

### Step 2: SSH and Run Setup
```bash
# Connect to server
ssh user@72.61.170.123

# Go to web directory
cd /var/www/law

# Run setup script
sudo bash setup.sh
```

### Step 3: Update SSL Certificate
```bash
# Edit Apache config
sudo nano /etc/apache2/sites-available/law-connectors.conf

# Update these lines with your certificate paths:
# SSLCertificateFile /etc/ssl/certs/your-certificate.crt
# SSLCertificateKeyFile /etc/ssl/private/your-key.key

# Then restart Apache
sudo systemctl restart apache2
```

## ✅ Verify Everything Works

```bash
# Check Flask is running
sudo systemctl status law-connectors

# Check Apache
sudo systemctl status apache2

# View Flask logs
sudo journalctl -u law-connectors -f

# Test API endpoint
curl http://127.0.0.1:5000/

# Test in browser
# https://test.1xclube.org/chat.php
```

## 🔒 Important Security Checklist

Before uploading, make sure:

- [ ] **Update .env file** with your real OPENROUTER_API_KEY
- [ ] Database credentials are correct in .env
- [ ] No hardcoded passwords in any PHP files
- [ ] File permissions are correct (chmod 600 .env)
- [ ] SSL certificate is ready on server
- [ ] Database tables created (run lib/law.sql if needed)

## 📊 Server Requirements

- Ubuntu 20.04+ (or CentOS/RHEL equivalent)
- Apache 2.4+ with mod_proxy
- Python 3.8+
- PHP 7.4+
- MySQL 5.7+ or MariaDB 10.3+

## 🆘 If Something Goes Wrong

### Flask not starting?
```bash
sudo journalctl -u law-connectors -n 50
sudo systemctl restart law-connectors
```

### Apache showing 502 Bad Gateway?
```bash
# Check if Flask is running
curl http://127.0.0.1:5000/

# Check Apache error log
sudo tail -f /var/log/apache2/law-error.log

# Restart Apache
sudo systemctl restart apache2
```

### Chat not loading?
```bash
# Check browser console for errors
# Check if /api endpoint is working
curl https://test.1xclube.org/api

# Check Flask logs
sudo journalctl -u law-connectors -f
```

### Database connection failed?
```bash
# Verify database host in .env
# Check if database server is accessible
mysql -h 72.61.170.123 -u user -p -e "SELECT 1"
```

## 📝 Auto-Start Features

After setup.sh runs:
- ✅ Flask auto-starts on server reboot
- ✅ Flask auto-restarts if it crashes
- ✅ Apache reverse-proxies /api to Flask
- ✅ HTTPS is enabled (with your certificate)
- ✅ Chat interface loads from iframe

## 🎉 You're All Set!

Your site will be live at:
```
https://test.1xclube.org/
https://test.1xclube.org/chat.php  (Chat with Lexi AI)
https://test.1xclube.org/login.php (Login)
```

The Flask backend will run in the background on port 5000 and be accessible via the Apache reverse proxy at `/api`.

**Questions?** Check the logs with:
```bash
sudo journalctl -u law-connectors -f
```
