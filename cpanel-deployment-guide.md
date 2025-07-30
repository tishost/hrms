# 🚀 HRMS System - cPanel Deployment Guide
## Domain: barimanager.com

### ✅ cPanel সার্ভার প্রস্তুতি

#### cPanel Requirements:
- [ ] PHP 8.1+ enabled
- [ ] MySQL 8.0+ available
- [ ] SSL certificate configured
- [ ] Domain pointing to cPanel

### 🔧 cPanel Deployment Steps

#### ১. cPanel এ লগইন করুন
- cPanel URL: `https://barimanager.com/cpanel`
- Username: আপনার cPanel username
- Password: আপনার cPanel password

#### ২. File Manager এ যান
- cPanel → File Manager
- Document Root: `public_html` (অথবা আপনার domain folder)

#### ৩. ফাইল আপলোড করুন

**Option A: File Manager দিয়ে**
1. File Manager → Upload
2. সব Laravel ফাইল আপলোড করুন
3. `.env.example` কে `.env` এ rename করুন

**Option B: FTP দিয়ে**
```bash
# FTP credentials
Host: barimanager.com
Username: আপনার cPanel username
Password: আপনার cPanel password
Port: 21

# ফাইল আপলোড করুন public_html ফোল্ডারে
```

#### ৪. Database সেটআপ করুন

**cPanel → MySQL Databases:**
1. Create Database: `barimanager_hrms`
2. Create User: `hrms_user`
3. Add User to Database with ALL PRIVILEGES
4. Note down database credentials

#### ৫. .env ফাইল কনফিগার করুন

File Manager → `.env` ফাইল এডিট করুন:

```env
APP_NAME="HRMS System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://barimanager.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_cpanel_username_barimanager_hrms
DB_USERNAME=your_cpanel_username_hrms_user
DB_PASSWORD=your_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=barimanager.com
MAIL_PORT=587
MAIL_USERNAME=your_email@barimanager.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@barimanager.com"
MAIL_FROM_NAME="HRMS System"
```

#### ৬. SSH Access (যদি available হয়)

**cPanel → Terminal:**
```bash
cd public_html

# Composer dependencies ইনস্টল করুন
composer install --optimize-autoloader --no-dev

# Laravel সেটআপ করুন
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link

# পারমিশন সেট করুন
chmod -R 755 .
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

#### ৭. যদি SSH না থাকে (cPanel Cron Jobs ব্যবহার করুন)

**cPanel → Cron Jobs:**

**Laravel Scheduler (প্রতি মিনিটে):**
```bash
* * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```

**Database Backup (প্রতিদিন 2 AM এ):**
```bash
0 2 * * * mysqldump -u username_dbname -p'password' username_dbname > /home/username/backup/db_$(date +\%Y\%m\%d).sql
```

#### ৮. .htaccess ফাইল চেক করুন

File Manager → `public_html/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

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
```

#### ৯. SSL সার্টিফিকেট

**cPanel → SSL/TLS:**
1. Install SSL Certificate
2. Force HTTPS Redirect
3. Update .env file with HTTPS URL

#### ১০. Email Configuration

**cPanel → Email Accounts:**
1. Create email account: `noreply@barimanager.com`
2. Update .env file with email credentials

### 🧪 Testing Checklist

#### ফাংশনালিটি টেস্ট:
- [ ] https://barimanager.com লোড হচ্ছে
- [ ] Admin login কাজ করছে
- [ ] User registration কাজ করছে
- [ ] File upload কাজ করছে
- [ ] Email sending কাজ করছে

#### সিকিউরিটি টেস্ট:
- [ ] HTTPS redirect কাজ করছে
- [ ] .env ফাইল সুরক্ষিত আছে
- [ ] Debug mode বন্ধ আছে

### 🔧 cPanel Troubleshooting

#### Common Issues:

**500 Error:**
1. Check error logs: cPanel → Error Logs
2. Check file permissions
3. Check .htaccess file

**Database Connection Error:**
1. Verify database credentials in .env
2. Check database user privileges
3. Test connection via phpMyAdmin

**File Upload Issues:**
1. Check storage directory permissions
2. Run: `php artisan storage:link`
3. Check upload_max_filesize in PHP settings

**SSL Issues:**
1. Check SSL certificate status
2. Force HTTPS in .htaccess
3. Update APP_URL in .env

### 📊 cPanel Monitoring

#### Logs Access:
- **Error Logs**: cPanel → Error Logs
- **Access Logs**: cPanel → Raw Access Logs
- **Laravel Logs**: File Manager → storage/logs/laravel.log

#### Backup Strategy:
1. **Database Backup**: cPanel → phpMyAdmin → Export
2. **Files Backup**: cPanel → File Manager → Select All → Compress
3. **Automated Backup**: cPanel → Cron Jobs

### 🎯 cPanel Optimization

#### Performance:
1. Enable OPcache in PHP settings
2. Enable Gzip compression
3. Optimize images
4. Minify CSS/JS

#### Security:
1. Enable mod_security
2. Set proper file permissions
3. Use strong passwords
4. Enable two-factor authentication

### ✅ Final cPanel Checklist

- [ ] All files uploaded to public_html
- [ ] .env file configured correctly
- [ ] Database created and configured
- [ ] SSL certificate installed
- [ ] Email accounts set up
- [ ] Cron jobs configured
- [ ] File permissions set correctly
- [ ] Website accessible at https://barimanager.com
- [ ] All functionality tested
- [ ] Backup system configured

🎉 **cPanel Deployment Complete!** 
