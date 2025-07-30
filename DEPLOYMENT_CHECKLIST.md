# 🚀 HRMS System - Live Deployment Checklist
## Domain: barimanager.com

### ✅ Pre-Deployment Checklist

#### সার্ভার প্রস্তুতি
- [ ] PHP 8.1+ ইনস্টল করা আছে
- [ ] MySQL 8.0+ ইনস্টল করা আছে
- [ ] Apache/Nginx ইনস্টল করা আছে
- [ ] Composer ইনস্টল করা আছে
- [ ] Node.js & NPM ইনস্টল করা আছে
- [ ] SSL সার্টিফিকেট প্রস্তুত আছে

#### প্রজেক্ট প্রস্তুতি
- [ ] সব ফাইল আপলোড করা হয়েছে
- [ ] .env ফাইল তৈরি করা হয়েছে
- [ ] ডাটাবেস credentials সেট করা হয়েছে
- [ ] Email settings কনফিগার করা হয়েছে

### 🔧 Deployment Steps

#### ১. সার্ভারে SSH করুন
```bash
ssh root@your-server-ip
```

#### ২. প্রজেক্ট ডিরেক্টরি তৈরি করুন
```bash
mkdir -p /var/www/barimanager.com
cd /var/www/barimanager.com
```

#### ৩. ফাইল আপলোড করুন
```bash
# Git থেকে ক্লোন করুন (যদি repository আছে)
git clone your-repository-url .

# অথবা FTP দিয়ে আপলোড করুন
```

#### ৪. Dependencies ইনস্টল করুন
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

#### ৫. Environment সেটআপ করুন
```bash
cp env-example.txt .env
# .env ফাইল এডিট করুন
nano .env
```

#### ৬. Laravel সেটআপ করুন
```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

#### ৭. পারমিশন সেট করুন
```bash
chown -R www-data:www-data /var/www/barimanager.com
chmod -R 755 /var/www/barimanager.com
chmod -R 775 /var/www/barimanager.com/storage
chmod -R 775 /var/www/barimanager.com/bootstrap/cache
```

#### ৮. Apache কনফিগারেশন
```bash
# Virtual host ফাইল তৈরি করুন
nano /etc/apache2/sites-available/barimanager.com.conf
```

#### ৯. SSL সার্টিফিকেট
```bash
certbot --apache -d barimanager.com -d www.barimanager.com
```

#### ১০. Cron job সেট করুন
```bash
crontab -e
# যোগ করুন: * * * * * cd /var/www/barimanager.com && php artisan schedule:run >> /dev/null 2>&1
```

### 🧪 Post-Deployment Testing

#### ফাংশনালিটি টেস্ট
- [ ] Homepage লোড হচ্ছে
- [ ] Admin login কাজ করছে
- [ ] User registration কাজ করছে
- [ ] File upload কাজ করছে
- [ ] Email sending কাজ করছে
- [ ] Database operations কাজ করছে

#### সিকিউরিটি টেস্ট
- [ ] HTTPS redirect কাজ করছে
- [ ] .env ফাইল সুরক্ষিত আছে
- [ ] Debug mode বন্ধ আছে
- [ ] Error messages hide করা আছে

#### পারফরম্যান্স টেস্ট
- [ ] Page load time < 3 seconds
- [ ] Database queries optimized
- [ ] Images optimized
- [ ] CSS/JS minified

### 📊 Monitoring Setup

#### Log Monitoring
```bash
# Laravel logs
tail -f /var/www/barimanager.com/storage/logs/laravel.log

# Apache logs
tail -f /var/log/apache2/barimanager_error.log
```

#### Backup Setup
```bash
# Daily backup script
nano /backup/hrms/backup.sh
chmod +x /backup/hrms/backup.sh

# Add to crontab
crontab -e
# যোগ করুন: 0 2 * * * /backup/hrms/backup.sh
```

### 🔧 Troubleshooting

#### Common Issues

**500 Error**
```bash
# Check logs
tail -f storage/logs/laravel.log

# Check permissions
ls -la storage/
ls -la bootstrap/cache/
```

**Database Connection Error**
```bash
# Test connection
php artisan tinker
DB::connection()->getPdo();
```

**File Upload Issues**
```bash
# Check storage link
php artisan storage:link

# Check permissions
chmod -R 775 storage/
```

**SSL Issues**
```bash
# Check SSL certificate
certbot certificates

# Renew if needed
certbot renew
```

### 📞 Support Information

#### Important Files
- Project Directory: `/var/www/barimanager.com`
- Logs: `/var/www/barimanager.com/storage/logs/`
- Backups: `/backup/hrms/`
- Apache Config: `/etc/apache2/sites-available/barimanager.com.conf`

#### Database Credentials
- Database: `barimanager_hrms`
- Username: `hrms_user`
- Password: (check .env file)

#### Admin Access
- URL: `https://barimanager.com/admin`
- Default admin credentials: (check database seeder)

### ✅ Final Checklist

- [ ] Website accessible at https://barimanager.com
- [ ] SSL certificate working
- [ ] All functionality tested
- [ ] Backup system configured
- [ ] Monitoring set up
- [ ] Documentation updated
- [ ] Team notified

🎉 **Deployment Complete!** 
