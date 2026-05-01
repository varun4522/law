# 🚀 Quick Deployment Guide - Law Connectors

Your AI is not working because the Flask backend isn't deployed on your live server yet.

## ⚡ Quick Fix (5 minutes)

### Step 1: Upload Files to Your Server
Upload all files to `/var/www/law` on your server at `72.61.170.123`

**Using SCP (from your local machine):**
```bash
scp -r /path/to/law/* user@72.61.170.123:/var/www/law/
```

### Step 2: SSH into Your Server
```bash
ssh user@72.61.170.123
cd /var/www/law
```

### Step 3: Run Deployment Script
```bash
sudo bash deploy.sh
```

This will automatically:
- ✅ Install Python dependencies
- ✅ Set up virtual environment  
- ✅ Configure Apache reverse proxy
- ✅ Setup systemd service for auto-start
- ✅ Start Flask backend

### Step 4: Configure Environment
```bash
nano .env
```

Add your OpenRouter API key:
```
OPENROUTER_API_KEY=your_actual_key_here
```

### Step 5: Update Apache SSL
Edit the Apache config with your SSL certificate paths:
```bash
sudo nano /etc/apache2/sites-available/law-connectors.conf
```

Update these lines:
```
SSLCertificateFile /path/to/your/certificate.crt
SSLCertificateKeyFile /path/to/your/key.key
```

Then restart Apache:
```bash
sudo systemctl restart apache2
```

### Step 6: Verify It Works
```bash
# Check Flask is running
sudo systemctl status law-connectors

# Test API endpoint
curl http://127.0.0.1:5000/

# Check Apache
sudo apache2ctl configtest
```

## ✅ That's It!

Your Lexi AI should now be working at:
👉 **https://test.1xclube.org/chat.php**

## 📚 Files Included

- **deploy.sh** - Automated deployment script (run this!)
- **lawai.service** - Systemd service file
- **apache-config.conf** - Apache reverse proxy config
- **.env.example** - Environment variables template
- **DEPLOYMENT.md** - Detailed deployment guide

## 🆘 Still Having Issues?

### Check Flask is running
```bash
sudo systemctl status law-connectors
sudo journalctl -u law-connectors -f
```

### Check Apache reverse proxy
```bash
curl http://127.0.0.1:5000/
sudo tail -f /var/log/apache2/law-error.log
```

### Restart everything
```bash
sudo systemctl restart law-connectors
sudo systemctl restart apache2
```

## 🎯 How It Works

1. **chat.php** sends request to `https://test.1xclube.org/api`
2. **Apache** receives request and proxies it to Flask on `127.0.0.1:5000`
3. **Flask** processes the request and returns the AI response
4. **Apache** proxies response back to browser

This means your Flask app runs in the background and is always available! 🚀
