# cPanel Database Upload Guide for HRMS

## 📋 Prerequisites
- cPanel access (barimanager.com)
- Database credentials ready
- SQL dump file (if available)

## 🗄️ Step 1: Create Database in cPanel

### 1.1 Access cPanel
- Login to cPanel: `http://139.99.33.181:2083`
- Username: `barimanager`
- Password: `*euSlN(C71+2LRz9`

### 1.2 Create MySQL Database
1. **Find "MySQL Databases"** in cPanel
2. **Create Database:**
   - Database Name: `barimanager_hrms` (cPanel prefix + hrms)
   - Click "Create Database"

3. **Create Database User:**
   - Username: `barimanager_hrms_user`
   - Password: `StrongPassword123!`
   - Click "Create User"

4. **Add User to Database:**
   - Select database: `barimanager_hrms`
   - Select user: `barimanager_hrms_user`
   - Privileges: **ALL PRIVILEGES**
   - Click "Add"

## 🗄️ Step 2: Database Structure Setup

### 2.1 Using phpMyAdmin
1. **Access phpMyAdmin:**
   - In cPanel, click "phpMyAdmin"
   - Select your database: `barimanager_hrms`

2. **Import SQL (if you have dump):**
   - Click "Import" tab
   - Choose SQL file
   - Click "Go"

### 2.2 Using Laravel Migration (Recommended)
```bash
# Connect to server via SSH/Terminal
ssh barimanager@139.99.33.181

# Navigate to project directory
cd public_html

# Run migrations
php artisan migrate --force

# Run seeders
php artisan db:seed --force
```

## ⚙️ Step 3: Configure .env File

### 3.1 Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=barimanager_hrms
DB_USERNAME=barimanager_hrms_user
DB_PASSWORD=StrongPassword123!
```

### 3.2 Complete .env Template
```env
APP_NAME="HRMS"
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://barimanager.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=barimanager_hrms
DB_USERNAME=barimanager_hrms_user
DB_PASSWORD=StrongPassword123!

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mail.barimanager.com
MAIL_PORT=587
MAIL_USERNAME=noreply@barimanager.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@barimanager.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

## 🚀 Step 4: Laravel Commands

### 4.1 Generate App Key
```bash
php artisan key:generate
```

### 4.2 Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 4.3 Run Migrations & Seeders
```bash
# Run all migrations
php artisan migrate --force

# Run seeders
php artisan db:seed --force

# Or run specific seeder
php artisan db:seed --class=AdminUserSeeder
```

## 📊 Step 5: Verify Database

### 5.1 Check Tables
```sql
-- In phpMyAdmin or MySQL console
SHOW TABLES;

-- Check users table
SELECT COUNT(*) FROM users;

-- Check owners table
SELECT COUNT(*) FROM owners;
```

### 5.2 Laravel Tinker Check
```bash
php artisan tinker

# Check database connection
DB::connection()->getPdo();

# Check tables
DB::select('SHOW TABLES');

# Check admin user
\App\Models\User::where('email', 'admin@barimanager.com')->first();
```

## 🔧 Step 6: Troubleshooting

### 6.1 Common Issues
1. **Database Connection Error:**
   - Check .env file
   - Verify database credentials
   - Ensure database exists

2. **Migration Errors:**
   - Check table prefixes
   - Verify MySQL version compatibility
   - Check file permissions

3. **Seeder Errors:**
   - Check if tables exist
   - Verify foreign key constraints
   - Check data integrity

### 6.2 Useful Commands
```bash
# Check migration status
php artisan migrate:status

# Reset database
php artisan migrate:fresh --seed

# Check environment
php artisan env

# Check Laravel version
php artisan --version
```

## 📝 Step 7: Database Backup

### 7.1 Create Backup
```bash
# Export database
mysqldump -u barimanager_hrms_user -p barimanager_hrms > hrms_backup.sql

# Or via phpMyAdmin
# Export > Custom > Select all tables > Go
```

### 7.2 Restore Backup
```bash
# Import backup
mysql -u barimanager_hrms_user -p barimanager_hrms < hrms_backup.sql

# Or via phpMyAdmin
# Import > Choose file > Go
```

## ✅ Step 8: Final Checklist

- [ ] Database created in cPanel
- [ ] Database user created and assigned
- [ ] .env file configured
- [ ] App key generated
- [ ] Migrations run successfully
- [ ] Seeders run successfully
- [ ] Database connection verified
- [ ] Admin user created
- [ ] Backup created

## 🎯 Quick Setup Script

Create this script for automated setup:

```bash
#!/bin/bash
# cpanel-database-setup.sh

echo "Setting up HRMS database in cPanel..."

# Generate app key
php artisan key:generate

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Run migrations
php artisan migrate --force

# Run seeders
php artisan db:seed --force

echo "Database setup completed!"
```

## 📞 Support

If you encounter issues:
1. Check cPanel error logs
2. Verify database credentials
3. Test connection via phpMyAdmin
4. Check Laravel logs: `storage/logs/laravel.log`

---

**Note:** Always backup your database before making changes! 