# 🚀 Cursor Remote Development Setup
## Domain: barimanager.com | IP: 139.99.33.181

### ✅ Cursor Remote Development Configuration

#### সার্ভার Information:
- **Domain**: barimanager.com
- **IP**: 139.99.33.181
- **cPanel Username**: barimanager
- **cPanel Password**: *euSlN(C71+2LRz9
- **SSH Access**: Available

### 🔧 Cursor Remote Development Setup

#### ১. Cursor এ Remote Connection Setup

**Option A: SSH Connection (Recommended)**

1. **Cursor এ Remote SSH Setup:**
   ```
   Host: 139.99.33.181
   Username: barimanager
   Port: 22
   ```

2. **SSH Key Setup (যদি SSH key থাকে):**
   ```bash
   # Local machine এ SSH key generate করুন
   ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
   
   # Public key কপি করুন
   cat ~/.ssh/id_rsa.pub
   
   # সার্ভারে SSH key যোগ করুন
   ssh barimanager@139.99.33.181
   mkdir -p ~/.ssh
   echo "your_public_key_here" >> ~/.ssh/authorized_keys
   chmod 600 ~/.ssh/authorized_keys
   ```

3. **Cursor এ Remote Folder Open:**
   ```
   Remote Path: /home/barimanager/public_html
   ```

**Option B: SFTP Connection**

1. **Cursor এ SFTP Setup:**
   ```
   Protocol: SFTP
   Host: 139.99.33.181
   Username: barimanager
   Password: *euSlN(C71+2LRz9
   Port: 22
   Remote Path: /home/barimanager/public_html
   ```

#### ২. Cursor Remote Development Workflow

**Cursor এ Project Open করার পর:**

1. **Terminal Setup:**
   ```bash
   # Cursor এর integrated terminal এ
   cd /home/barimanager/public_html
   
   # Check current setup
   ls -la
   php -v
   composer --version
   ```

2. **Development Environment Setup:**
   ```bash
   # Install dependencies
   composer install --dev
   npm install
   
   # Build assets
   npm run dev
   
   # Clear caches
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

3. **Development .env Setup:**
   ```bash
   # Copy development environment
   cp dev-env.txt .env
   
   # Generate app key
   php artisan key:generate
   
   # Update database credentials
   nano .env
   ```

#### ৩. Cursor Remote Development Features

**Cursor এ Available Features:**

1. **Real-time File Editing:**
   - Direct file editing on server
   - Auto-save functionality
   - Syntax highlighting

2. **Integrated Terminal:**
   ```bash
   # Laravel commands
   php artisan migrate
   php artisan make:controller NewController
   php artisan make:model NewModel
   
   # Git commands
   git add .
   git commit -m "Update"
   git push origin main
   ```

3. **Debugging:**
   ```bash
   # Watch logs in real-time
   tail -f storage/logs/laravel.log
   
   # Check errors
   tail -f storage/logs/error.log
   ```

4. **Database Management:**
   ```bash
   # Access database
   php artisan tinker
   
   # Run migrations
   php artisan migrate
   
   # Seed database
   php artisan db:seed
   ```

#### ৪. Cursor Remote Development Best Practices

**File Management:**
```bash
# Always backup before major changes
cp -r /home/barimanager/public_html /home/barimanager/backup/$(date +%Y%m%d_%H%M%S)

# Use Git for version control
git add .
git commit -m "Development update"
git push origin main
```

**Development Workflow:**
```bash
# Daily start
cd /home/barimanager/public_html
git pull origin main
composer install
npm run dev
php artisan config:clear

# During development
# Make changes in Cursor
# Test changes
# Check logs

# Daily end
git add .
git commit -m "Daily update"
git push origin main
```

#### ৫. Cursor Remote Development Commands

**Quick Commands for Cursor Terminal:**

```bash
# Development commands
alias dev-start='cd /home/barimanager/public_html && git pull origin main && composer install && npm run dev && php artisan config:clear'
alias dev-logs='tail -f storage/logs/laravel.log'
alias dev-clear='php artisan config:clear && php artisan route:clear && php artisan view:clear'
alias dev-backup='cp -r /home/barimanager/public_html /home/barimanager/backup/$(date +%Y%m%d_%H%M%S)'

# Add to ~/.bashrc
echo "alias dev-start='cd /home/barimanager/public_html && git pull origin main && composer install && npm run dev && php artisan config:clear'" >> ~/.bashrc
echo "alias dev-logs='tail -f storage/logs/laravel.log'" >> ~/.bashrc
echo "alias dev-clear='php artisan config:clear && php artisan route:clear && php artisan view:clear'" >> ~/.bashrc
echo "alias dev-backup='cp -r /home/barimanager/public_html /home/barimanager/backup/\$(date +%Y%m%d_%H%M%S)'" >> ~/.bashrc
source ~/.bashrc
```

#### ৬. Cursor Remote Development Security

**Security Best Practices:**

1. **SSH Key Authentication:**
   ```bash
   # Generate SSH key pair
   ssh-keygen -t rsa -b 4096
   
   # Copy public key to server
   ssh-copy-id barimanager@139.99.33.181
   ```

2. **File Permissions:**
   ```bash
   # Set correct permissions
   chmod -R 755 /home/barimanager/public_html
   chmod -R 775 /home/barimanager/public_html/storage
   chmod -R 775 /home/barimanager/public_html/bootstrap/cache
   chmod 644 /home/barimanager/public_html/.env
   ```

3. **Backup Strategy:**
   ```bash
   # Create backup script
   cat > /home/barimanager/backup/cursor_backup.sh << 'EOF'
   #!/bin/bash
   DATE=$(date +%Y%m%d_%H%M%S)
   PROJECT_DIR="/home/barimanager/public_html"
   BACKUP_DIR="/home/barimanager/backup"
   
   # Create backup
   tar -czf $BACKUP_DIR/cursor_dev_$DATE.tar.gz $PROJECT_DIR
   
   # Keep only last 7 days
   find $BACKUP_DIR -name "cursor_dev_*.tar.gz" -mtime +7 -delete
   
   echo "Cursor development backup completed: $DATE"
   EOF
   
   chmod +x /home/barimanager/backup/cursor_backup.sh
   ```

#### ৭. Cursor Remote Development Monitoring

**Real-time Monitoring in Cursor:**

```bash
# Watch logs in Cursor terminal
tail -f storage/logs/laravel.log

# Monitor file changes
watch -n 1 'ls -la storage/logs/'

# Monitor database
watch -n 5 'mysql -u barimanager -p -e "SHOW PROCESSLIST;" barimanager_hrms_dev'
```

**Performance Monitoring:**
```bash
# Check disk usage
df -h
du -sh /home/barimanager/public_html/

# Check memory usage
free -h

# Check PHP memory
php -i | grep memory_limit
```

#### ৮. Cursor Remote Development Troubleshooting

**Common Issues:**

**Connection Issues:**
```bash
# Test SSH connection
ssh barimanager@139.99.33.181

# Check SSH service
sudo systemctl status ssh

# Check firewall
sudo ufw status
```

**File Permission Issues:**
```bash
# Fix permissions
chmod -R 755 /home/barimanager/public_html
chmod -R 775 /home/barimanager/public_html/storage
chmod -R 775 /home/barimanager/public_html/bootstrap/cache

# Check ownership
ls -la /home/barimanager/public_html/
```

**Development Issues:**
```bash
# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Rebuild assets
npm run build

# Check logs
tail -f storage/logs/laravel.log
```

### ✅ Cursor Remote Development Checklist

- [ ] SSH connection established
- [ ] Cursor connected to remote server
- [ ] Project files accessible in Cursor
- [ ] Development environment configured
- [ ] Git repository set up
- [ ] Backup system configured
- [ ] Monitoring tools set up
- [ ] Security measures implemented

### 🚀 Cursor Remote Development Workflow

1. **Start Development Session:**
   ```bash
   # In Cursor terminal
   dev-start
   dev-logs
   ```

2. **During Development:**
   - Edit files directly in Cursor
   - Use integrated terminal for commands
   - Monitor logs in real-time
   - Test changes immediately

3. **End Development Session:**
   ```bash
   # In Cursor terminal
   git add .
   git commit -m "Development update"
   git push origin main
   dev-backup
   ```

🎉 **Cursor Remote Development Setup Complete!**

এই setup দিয়ে আপনি Cursor দিয়ে remote development করতে পারবেন। কোন সমস্যা হলে আমাকে জানান! 
