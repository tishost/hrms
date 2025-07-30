# HRMS System - Production Deployment Guide

## Domain: barimanager.com

### ১. সার্ভার প্রস্তুতি

#### প্রয়োজনীয় সফটওয়্যার:
- PHP 8.1+ 
- MySQL 8.0+
- Apache/Nginx
- Composer
- Node.js & NPM

### ২. প্রজেক্ট আপলোড

#### FTP/SSH মাধ্যমে ফাইল আপলোড:
```bash
# সার্ভারে প্রজেক্ট ক্লোন করুন
git clone your-repository-url /var/www/barimanager.com

# অথবা FTP দিয়ে ফাইল আপলোড করুন
```

### ৩. কনফিগারেশন সেটআপ

#### .env ফাইল তৈরি করুন:
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
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=barimanager_hrms
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@barimanager.com"
MAIL_FROM_NAME="HRMS System"
```

### ৪. ডাটাবেস সেটআপ

```bash
# ডাটাবেস তৈরি করুন
mysql -u root -p
CREATE DATABASE barimanager_hrms;
CREATE USER 'hrms_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON barimanager_hrms.* TO 'hrms_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### ৫. Laravel ইনস্টলেশন

```bash
cd /var/www/barimanager.com

# Composer dependencies ইনস্টল করুন
composer install --optimize-autoloader --no-dev

# .env ফাইল কপি করুন
cp .env.example .env

# Application key জেনারেট করুন
php artisan key:generate

# ডাটাবেস মাইগ্রেশন রান করুন
php artisan migrate --force

# সিডার রান করুন (যদি প্রয়োজন হয়)
php artisan db:seed --force

# কনফিগারেশন ক্যাশ করুন
php artisan config:cache
php artisan route:cache
php artisan view:cache

# স্টোরেজ লিংক তৈরি করুন
php artisan storage:link
```

### ৬. ফ্রন্টএন্ড বিল্ড

```bash
# NPM dependencies ইনস্টল করুন
npm install

# প্রোডাকশন বিল্ড করুন
npm run build
```

### ৭. ফাইল পারমিশন

```bash
# স্টোরেজ এবং ক্যাশ ডিরেক্টরি পারমিশন সেট করুন
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### ৮. Apache কনফিগারেশন

#### /etc/apache2/sites-available/barimanager.com.conf:
```apache
<VirtualHost *:80>
    ServerName barimanager.com
    ServerAlias www.barimanager.com
    DocumentRoot /var/www/barimanager.com/public
    
    <Directory /var/www/barimanager.com/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/barimanager_error.log
    CustomLog ${APACHE_LOG_DIR}/barimanager_access.log combined
</VirtualHost>
```

### ৯. SSL সার্টিফিকেট (Let's Encrypt)

```bash
# Certbot ইনস্টল করুন
sudo apt install certbot python3-certbot-apache

# SSL সার্টিফিকেট জেনারেট করুন
sudo certbot --apache -d barimanager.com -d www.barimanager.com
```

### ১০. ক্রন জব সেটআপ

```bash
# Crontab এডিট করুন
crontab -e

# Laravel scheduler যোগ করুন
* * * * * cd /var/www/barimanager.com && php artisan schedule:run >> /dev/null 2>&1
```

### ১১. সিকিউরিটি চেকলিস্ট

- [ ] APP_DEBUG=false
- [ ] Strong database password
- [ ] SSL certificate installed
- [ ] File permissions set correctly
- [ ] .env file secured
- [ ] Backup system configured

### ১২. টেস্টিং

1. https://barimanager.com এ যান
2. Admin panel টেস্ট করুন
3. User registration টেস্ট করুন
4. Email functionality টেস্ট করুন
5. File upload টেস্ট করুন

### ১৩. মনিটরিং

```bash
# Laravel logs চেক করুন
tail -f /var/www/barimanager.com/storage/logs/laravel.log

# Apache logs চেক করুন
tail -f /var/log/apache2/barimanager_error.log
```

### ১৪. ব্যাকআপ স্ট্র্যাটেজি

```bash
# Database backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u hrms_user -p barimanager_hrms > /backup/db_$DATE.sql
tar -czf /backup/files_$DATE.tar.gz /var/www/barimanager.com
```

### সমস্যা সমাধান:

#### যদি 500 error আসে:
```bash
# Logs চেক করুন
tail -f storage/logs/laravel.log

# Permissions চেক করুন
ls -la storage/
ls -la bootstrap/cache/
```

#### যদি database connection error আসে:
```bash
# Database connection টেস্ট করুন
php artisan tinker
DB::connection()->getPdo();
```

#### যদি file upload কাজ না করে:
```bash
# Storage link চেক করুন
php artisan storage:link

# Permissions সেট করুন
chmod -R 775 storage/
``` 
