# 🚀 Cursor SFTP Connection Setup (No SSH)
## Domain: barimanager.com | IP: 139.99.33.181

### ✅ SFTP Connection Setup (SSH ছাড়া)

#### সার্ভার Information:
- **Domain**: barimanager.com
- **IP**: 139.99.33.181
- **cPanel Username**: barimanager
- **cPanel Password**: *euSlN(C71+2LRz9
- **Connection Type**: SFTP (No SSH required)

### 🔧 Cursor SFTP Connection Steps

#### ১. Cursor এ SFTP Connection Setup

**Cursor এ Connection করার Steps:**

1. **Cursor Open করুন**
2. **File → Open Folder**
3. **"Connect to SFTP" ক্লিক করুন**
4. **Connection details দিন:**

```
Protocol: SFTP
Host: 139.99.33.181
Username: barimanager
Password: *euSlN(C71+2LRz9
Port: 22
Remote Path: /home/barimanager/public_html
```

#### ২. Alternative Connection Methods

**Method 1: Cursor Remote-SSH Extension**
1. Install "Remote - SSH" extension in Cursor
2. Go to File → Open Folder
3. Click "Connect to SSH Host"
4. Enter: `barimanager@139.99.33.181`
5. Select "Linux" when prompted
6. Enter password: `*euSlN(C71+2LRz9`

**Method 2: Cursor SFTP Extension**
1. Install "SFTP" extension in Cursor
2. Create SFTP configuration:
   ```json
   {
     "name": "HRMS Development",
     "host": "139.99.33.181",
     "username": "barimanager",
     "password": "*euSlN(C71+2LRz9",
     "port": 22,
     "remotePath": "/home/barimanager/public_html",
     "uploadOnSave": true,
     "syncMode": "update"
   }
   ```

#### ৩. SFTP Connection Troubleshooting

**If SFTP connection fails:**

1. **Check cPanel FTP Settings:**
   - Go to cPanel → FTP Accounts
   - Create new FTP account if needed
   - Use FTP credentials instead of cPanel

2. **Alternative FTP Connection:**
   ```
   Protocol: FTP
   Host: 139.99.33.181
   Username: barimanager
   Password: *euSlN(C71+2LRz9
   Port: 21
   Remote Path: /public_html
   ```

3. **Use FileZilla as Alternative:**
   - Download FileZilla
   - Connect using SFTP
   - Edit files locally and upload

#### ৪. Local Development + Upload Workflow

**Since direct SFTP editing might be slow:**

1. **Local Development Setup:**
   ```bash
   # Copy project to local machine
   # Edit files locally
   # Upload changes via SFTP
   ```

2. **Sync Script for Local Development:**
   ```bash
   # Create sync script
   cat > sync-to-server.sh << 'EOF'
   #!/bin/bash
   echo "Syncing files to server..."
   
   # Upload changed files
   rsync -avz --exclude='vendor' --exclude='node_modules' \
     ./hrms/ barimanager@139.99.33.181:/home/barimanager/public_html/
   
   echo "Sync completed!"
   EOF
   
   chmod +x sync-to-server.sh
   ```

#### ৫. cPanel File Manager Alternative

**If Cursor SFTP doesn't work:**

1. **Use cPanel File Manager:**
   - Go to https://barimanager.com/cpanel
   - Login with barimanager / *euSlN(C71+2LRz9
   - Go to File Manager
   - Navigate to public_html
   - Edit files directly

2. **cPanel Terminal (if available):**
   - Go to cPanel → Terminal
   - Run commands directly on server
   - Edit files with nano or vim

#### ৬. Development Workflow Options

**Option A: Local Development + Upload**
```bash
# 1. Develop locally
# 2. Upload via SFTP/FTP
# 3. Test on server
# 4. Repeat
```

**Option B: cPanel File Manager**
```bash
# 1. Edit files in cPanel File Manager
# 2. Use cPanel Terminal for commands
# 3. Test directly on server
```

**Option C: Hybrid Approach**
```bash
# 1. Use Cursor for local development
# 2. Use cPanel for server management
# 3. Sync important files manually
```

#### ৭. Quick Setup Commands

**Server-side setup (via cPanel Terminal):**
```bash
# Go to project directory
cd /home/barimanager/public_html

# Install dependencies
composer install --dev

# Build assets
npm install
npm run dev

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Set permissions
chmod -R 755 .
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

#### ৮. Development Commands (cPanel Terminal)

**Daily Development Commands:**
```bash
# Start development session
cd /home/barimanager/public_html
git pull origin main
composer install
npm run dev
php artisan config:clear

# Watch logs
tail -f storage/logs/laravel.log

# Clear caches
php artisan config:clear && php artisan route:clear && php artisan view:clear

# Run migrations
php artisan migrate

# Test changes
php artisan test
```

#### ৯. File Upload Methods

**Method 1: cPanel File Manager**
1. Go to cPanel → File Manager
2. Navigate to public_html
3. Upload files or edit directly

**Method 2: FTP Client**
1. Use FileZilla or WinSCP
2. Connect via SFTP
3. Upload files manually

**Method 3: Git + Server Pull**
```bash
# On server
cd /home/barimanager/public_html
git pull origin main
composer install
npm run dev
```

#### ১০. Development Environment Setup

**cPanel Terminal এ:**
```bash
# Create development environment
cp dev-env.txt .env
php artisan key:generate

# Update .env for development
sed -i 's/APP_ENV=production/APP_ENV=local/g' .env
sed -i 's/APP_DEBUG=false/APP_DEBUG=true/g' .env
sed -i 's/LOG_LEVEL=error/LOG_LEVEL=debug/g' .env

# Create development database
mysql -u barimanager -p -e "CREATE DATABASE IF NOT EXISTS barimanager_hrms_dev;"

# Run migrations
php artisan migrate --force
php artisan db:seed --force
```

### ✅ SFTP Connection Checklist

- [ ] cPanel access confirmed
- [ ] FTP/SFTP credentials verified
- [ ] File Manager accessible
- [ ] Terminal access available
- [ ] Development environment configured
- [ ] Backup system set up
- [ ] Monitoring tools configured

### 🚀 Recommended Workflow

**Best Approach for Your Setup:**

1. **Use cPanel File Manager for quick edits**
2. **Use cPanel Terminal for commands**
3. **Use local development for major changes**
4. **Upload via FTP/SFTP when needed**

**Daily Workflow:**
```bash
# 1. Check cPanel Terminal
cd /home/barimanager/public_html
git pull origin main

# 2. Make changes via File Manager or local development

# 3. Run commands in Terminal
composer install
npm run dev
php artisan config:clear

# 4. Test at https://barimanager.com

# 5. Commit changes
git add .
git commit -m "Update"
git push origin main
```

🎉 **SFTP Development Setup Complete!**

SSH না থাকলেও আপনি cPanel File Manager এবং Terminal দিয়ে development করতে পারবেন। কোন সমস্যা হলে আমাকে জানান! 
