# 🚀 HRMS System - Live Development Guide
## Domain: barimanager.com (Development on Live Server)

### ✅ Live Development Setup

#### সার্ভার Requirements:
- [ ] SSH access enabled
- [ ] Git repository set up
- [ ] Development tools installed
- [ ] Backup system configured

### 🔧 Live Development Configuration

#### ১. Development Environment Setup

**cPanel → Terminal বা SSH:**
```bash
# SSH to server
ssh username@barimanager.com

# Go to project directory
cd public_html

# Check current setup
ls -la
php -v
composer --version
```

#### ২. Development Mode কনফিগারেশন

**File Manager → .env ফাইল এডিট করুন:**

```env
# Development Configuration for Live Server
APP_NAME="HRMS System (Development)"
APP_ENV=local
APP_KEY=your_app_key_here
APP_DEBUG=true
APP_URL=https://barimanager.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_username_barimanager_hrms
DB_USERNAME=your_username_hrms_user
DB_PASSWORD=your_database_password

# Cache and Session
BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Email Configuration (Development)
MAIL_MAILER=log
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="dev@barimanager.com"
MAIL_FROM_NAME="HRMS Development"

# Development Settings
APP_DEBUG=true
LOG_LEVEL=debug
CACHE_DRIVER=file
SESSION_DRIVER=file

# HRMS Development Settings
BKASH_APP_KEY=your_bkash_app_key
BKASH_APP_SECRET=your_bkash_app_secret
BKASH_USERNAME=your_bkash_username
BKASH_PASSWORD=your_bkash_password
BKASH_SANDBOX=true

# File upload settings
MAX_FILE_SIZE=10240
ALLOWED_FILE_TYPES=jpg,jpeg,png,pdf,doc,docx
```

#### ৩. Development Tools Setup

**SSH Terminal এ:**
```bash
# Install development tools
composer install --dev

# Install NPM dependencies
npm install

# Build development assets
npm run dev

# Clear all caches for development
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Enable development mode
php artisan config:cache
```

#### ৪. Git Repository Setup

**SSH Terminal এ:**
```bash
# Initialize Git repository
git init

# Add remote repository (if you have one)
git remote add origin your-git-repository-url

# Create .gitignore for live development
cat > .gitignore << 'EOF'
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
docker-compose.override.yml
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
/.idea
/.vscode
EOF

# Add and commit files
git add .
git commit -m "Initial commit for live development"
```

#### ৫. Development Workflow

**Daily Development Process:**

```bash
# 1. Start development session
cd public_html

# 2. Pull latest changes (if using Git)
git pull origin main

# 3. Install any new dependencies
composer install
npm install

# 4. Run migrations if needed
php artisan migrate

# 5. Clear caches for fresh development
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 6. Build assets
npm run dev

# 7. Start development
# Make your changes...
# Test your changes...

# 8. Commit changes
git add .
git commit -m "Development update"
git push origin main
```

#### ৬. Development Database Management

**cPanel → phpMyAdmin:**
1. Create development database: `barimanager_hrms_dev`
2. Import production data for testing
3. Use separate database for development

**Or via SSH:**
```bash
# Create development database
mysql -u root -p -e "CREATE DATABASE barimanager_hrms_dev;"

# Import production data
mysqldump -u username -p barimanager_hrms | mysql -u username -p barimanager_hrms_dev
```

#### ৭. Development Security

**File Permissions:**
```bash
# Set development permissions
chmod -R 755 public_html
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Secure sensitive files
chmod 644 .env
chmod 644 .gitignore
```

**Development .htaccess:**
```apache
# public_html/.htaccess for development
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Development: Allow access to storage/logs
    RewriteCond %{REQUEST_URI} ^/storage/logs/
    RewriteRule ^(.*)$ - [F,L]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Handle Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
</IfModule>

# Development: Show errors
php_flag display_errors on
php_value error_reporting E_ALL
```

#### ৮. Development Monitoring

**Log Monitoring:**
```bash
# Watch Laravel logs
tail -f storage/logs/laravel.log

# Watch error logs
tail -f storage/logs/error.log

# Watch access logs
tail -f storage/logs/access.log
```

**cPanel Logs:**
- **Error Logs**: cPanel → Error Logs
- **Access Logs**: cPanel → Raw Access Logs

#### ৯. Development Backup Strategy

**Daily Development Backup:**
```bash
# Create backup script
cat > /home/username/backup/dev_backup.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
PROJECT_DIR="/home/username/public_html"
BACKUP_DIR="/home/username/backup/dev"

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u username -p barimanager_hrms_dev > $BACKUP_DIR/dev_db_$DATE.sql

# Files backup (excluding vendor and node_modules)
tar --exclude='vendor' --exclude='node_modules' --exclude='storage/logs' \
    -czf $BACKUP_DIR/dev_files_$DATE.tar.gz $PROJECT_DIR

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Development backup completed: $DATE"
EOF

chmod +x /home/username/backup/dev_backup.sh
```

#### ১০. Development Testing

**Testing Checklist:**
- [ ] Homepage loads correctly
- [ ] Admin panel accessible
- [ ] User registration works
- [ ] File uploads work
- [ ] Database operations work
- [ ] Error logs are accessible
- [ ] Development tools work

**Testing Commands:**
```bash
# Test database connection
php artisan tinker
DB::connection()->getPdo();

# Test email configuration
php artisan tinker
Mail::raw('Test email', function($message) {
    $message->to('test@example.com')->subject('Test');
});

# Test file uploads
php artisan storage:link
ls -la public/storage
```

### 🔧 Development Troubleshooting

#### Common Development Issues:

**500 Error in Development:**
```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check permissions
ls -la storage/
ls -la bootstrap/cache/
```

**Database Issues:**
```bash
# Test connection
php artisan tinker
DB::connection()->getPdo();

# Reset database
php artisan migrate:fresh --seed
```

**File Upload Issues:**
```bash
# Check storage link
php artisan storage:link

# Check permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### 📊 Development Monitoring

#### Real-time Monitoring:
```bash
# Watch logs in real-time
tail -f storage/logs/laravel.log

# Monitor file changes
watch -n 1 'ls -la storage/logs/'

# Monitor database
watch -n 5 'mysql -u username -p -e "SHOW PROCESSLIST;" barimanager_hrms_dev'
```

#### Performance Monitoring:
```bash
# Check PHP memory usage
php -i | grep memory_limit

# Check disk usage
df -h
du -sh public_html/

# Check database size
mysql -u username -p -e "SELECT table_schema AS 'Database', ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)' FROM information_schema.tables WHERE table_schema = 'barimanager_hrms_dev';"
```

### ✅ Development Checklist

- [ ] Development environment configured
- [ ] Debug mode enabled
- [ ] Logs accessible
- [ ] Database backup system set up
- [ ] Git repository configured
- [ ] Development tools installed
- [ ] File permissions set correctly
- [ ] Testing procedures established
- [ ] Monitoring system configured

### 🚀 Development Workflow Summary

1. **Daily Start:**
   ```bash
   cd public_html
   git pull origin main
   composer install
   npm run dev
   php artisan config:clear
   ```

2. **During Development:**
   - Make changes
   - Test changes
   - Check logs
   - Commit changes

3. **Daily End:**
   ```bash
   git add .
   git commit -m "Daily development update"
   git push origin main
   /home/username/backup/dev_backup.sh
   ```

🎉 **Live Development Setup Complete!**

এই setup দিয়ে আপনি live server থেকেই development করতে পারবেন। কোন সমস্যা হলে আমাকে জানান! 
