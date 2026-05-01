# Law Connectors - Production Server Setup

## 📋 Server Requirements
- Ubuntu 20.04+ or similar Linux
- Apache 2.4+ with mod_proxy enabled
- Python 3.8+
- PHP 7.4+
- MySQL/MariaDB

## 🚀 One-Click Deployment

### 1. Upload Files to Server
```bash
# On your local machine
scp -r /path/to/law/* user@72.61.170.123:/var/www/law/
```

### 2. SSH into Server and Run Setup
```bash
ssh user@72.61.170.123
cd /var/www/law
sudo bash setup.sh
```

That's it! 🎉 Everything will be configured automatically.

## 📁 Files to Upload
Upload ONLY these files to `/var/www/law`:
- ✅ app.py
- ✅ requirements.txt
- ✅ .env (with your API key)
- ✅ chat.php, index.php, login.php, signup.php
- ✅ lib/ (database functions)
- ✅ admin/ (admin panel)
- ✅ student/ (student panel)
- ✅ expert/ (expert panel)
- ✅ includes/ (includes)
- ✅ assets/ (CSS, JS, images)
- ✅ templates/ (Flask HTML)
- ✅ static/ (Flask static files)
- ✅ lawai.service
- ✅ apache-config.conf
- ✅ setup.sh

## ⚠️ Files NOT to Upload (Development Only)
- ❌ .venv/ (virtual environment)
- ❌ .git/ (git repository)
- ❌ DEPLOYMENT.md
- ❌ QUICKSTART.md
- ❌ nginx-config.conf (we use Apache)
- ❌ deploy.sh (use setup.sh instead)
- ❌ __pycache__/ (Python cache)
- ❌ *.pyc files
- ❌ .vscode/ (VS Code settings)

## 🔒 Security Checklist Before Upload
- [ ] .env has real OPENROUTER_API_KEY
- [ ] Database credentials in .env are secure
- [ ] No hardcoded passwords in code
- [ ] SSL certificate ready on server

## ✅ After Setup
Your site will be live at: https://test.1xclube.org
