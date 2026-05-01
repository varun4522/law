# Law Connectors - Live Server Deployment Guide

## 📋 Prerequisites
- Linux server (Ubuntu 20.04 LTS or later recommended)
- Node.js / PHP support for web files
- Nginx or Apache web server
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

### 5. Setup Nginx Reverse Proxy
```bash
# Copy Nginx configuration
sudo cp nginx-config.conf /etc/nginx/sites-available/law-connectors

# Edit and update SSL certificate paths
sudo nano /etc/nginx/sites-available/law-connectors

# Enable the site
sudo ln -s /etc/nginx/sites-available/law-connectors /etc/nginx/sites-enabled/

# Test Nginx config
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
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
```

### API returns 502 Bad Gateway
- Check if Flask is running: `sudo systemctl status law-connectors`
- Check Nginx logs: `sudo tail -f /var/log/nginx/error.log`

### Database connection fails
- Verify 72.61.170.123 is accessible: `ping 72.61.170.123`
- Check credentials in .env

### Reverse proxy not working
- Test Nginx syntax: `sudo nginx -t`
- Check Nginx logs: `sudo tail -f /var/log/nginx/access.log`

## 📝 Key Changes Made

1. **chat.php**: Changed iframe from `http://127.0.0.1:5000/` → `https://test.1xclube.org/api`
2. **app.py**: Changed binding from `localhost` → `0.0.0.0` and debug mode off
3. **lib/db.php**: Changed DB_HOST from `localhost` → `72.61.170.123`
4. Created **lawai.service** for systemd auto-start
5. Created **nginx-config.conf** for reverse proxy setup
6. Created **.env.example** for environment variables

## 🎉 Done!
Your Law Connectors app is now ready to run on your live server at `https://test.1xclube.org`
