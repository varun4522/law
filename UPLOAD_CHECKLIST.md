# 📋 UPLOAD CHECKLIST - What to Upload to Server

## 🚀 Quick Copy-Paste Command

```bash
# Run this from your local machine to upload everything:
scp -r app.py chat.php index.php login.php signup.php .env requirements.txt setup.sh lawai.service apache-config.conf admin/ student/ expert/ lib/ includes/ assets/ templates/ static/ user@72.61.170.123:/var/www/law/
```

## ✅ Files & Folders to Upload

### Root Level Files (Required)
- [x] `app.py` - Flask backend
- [x] `chat.php` - Chat page
- [x] `index.php` - Landing page
- [x] `login.php` - Login page
- [x] `signup.php` - Signup page
- [x] `.env` - **MUST UPDATE WITH YOUR API KEY**
- [x] `requirements.txt` - Python packages
- [x] `setup.sh` - Automated server setup
- [x] `lawai.service` - Auto-start service
- [x] `apache-config.conf` - Web server config

### Folders (Required)
- [x] `admin/` - Admin panel
- [x] `student/` - Student panel
- [x] `expert/` - Expert panel
- [x] `lib/` - PHP libraries
- [x] `includes/` - PHP includes
- [x] `assets/` - CSS, JS, images
- [x] `templates/` - Flask HTML
- [x] `static/` - Flask static files

### Documentation (Optional)
- [ ] `README.md` - Deployment guide
- [ ] `SERVER_SETUP.md` - Server setup info

## ❌ Files & Folders to SKIP (Don't Upload)

- [ ] `.venv/` - Virtual environment
- [ ] `.git/` - Git repository
- [ ] `DEPLOYMENT.md` - Old guide
- [ ] `QUICKSTART.md` - Old guide
- [ ] `nginx-config.conf` - We use Apache
- [ ] `deploy.sh` - Old script
- [ ] `__pycache__/` - Python cache
- [ ] `.vscode/` - VS Code settings
- [ ] `*.pyc` - Python compiled files

## 🔧 Pre-Upload Checklist

Before uploading, complete these:

### 1. Update .env File
```bash
# Open .env and update:
OPENROUTER_API_KEY=your_actual_api_key_here
```

### 2. Verify Database Config
In `lib/db.php`, check if database host is correct:
```php
define('DB_HOST', '72.61.170.123'); // ✅ Should be your database server
```

### 3. Check chat.php
The file now auto-detects localhost vs server, so no changes needed!

## 📤 Upload Methods

### Method 1: SCP (Recommended)
```bash
scp -r /path/to/law/* user@72.61.170.123:/var/www/law/
```

### Method 2: SFTP
```bash
sftp user@72.61.170.123
cd /var/www/law
put -r app.py chat.php index.php ...
```

### Method 3: FTP (Manual)
- Connect to server via FTP client
- Upload files to `/var/www/law/`

## ✅ After Upload

1. SSH into server:
```bash
ssh user@72.61.170.123
cd /var/www/law
```

2. Run setup:
```bash
sudo bash setup.sh
```

3. Update SSL cert:
```bash
sudo nano /etc/apache2/sites-available/law-connectors.conf
# Update certificate paths
sudo systemctl restart apache2
```

4. Verify:
```bash
sudo systemctl status law-connectors
sudo systemctl status apache2
```

## 🎉 Done!

Your site is now live at:
- https://test.1xclube.org/
- https://test.1xclube.org/chat.php

Flask backend runs automatically in the background! 🚀
