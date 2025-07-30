#!/bin/bash

# HRMS System Deployment Script
# Domain: barimanager.com

echo "🚀 Starting HRMS System Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    print_error "Please run as root (use sudo)"
    exit 1
fi

# Set variables
PROJECT_DIR="/var/www/barimanager.com"
DB_NAME="barimanager_hrms"
DB_USER="hrms_user"
DB_PASS=$(openssl rand -base64 32)

print_status "Setting up project directory..."

# Create project directory
mkdir -p $PROJECT_DIR
cd $PROJECT_DIR

# Copy project files (assuming files are already uploaded)
# If using git:
# git clone your-repository-url .

print_status "Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

print_status "Setting up environment file..."
if [ ! -f .env ]; then
    cp .env.example .env
    print_warning "Please edit .env file with your database credentials"
fi

print_status "Generating application key..."
php artisan key:generate --force

print_status "Setting up database..."
# Create database and user
mysql -u root -p -e "
CREATE DATABASE IF NOT EXISTS $DB_NAME;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
"

print_status "Database password: $DB_PASS"
print_warning "Please update .env file with this database password"

print_status "Running database migrations..."
php artisan migrate --force

print_status "Running seeders..."
php artisan db:seed --force

print_status "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

print_status "Creating storage link..."
php artisan storage:link

print_status "Setting file permissions..."
chown -R www-data:www-data $PROJECT_DIR
chmod -R 755 $PROJECT_DIR
chmod -R 775 $PROJECT_DIR/storage
chmod -R 775 $PROJECT_DIR/bootstrap/cache

print_status "Installing NPM dependencies..."
npm install

print_status "Building frontend assets..."
npm run build

print_status "Setting up Apache configuration..."

# Create Apache virtual host configuration
cat > /etc/apache2/sites-available/barimanager.com.conf << EOF
<VirtualHost *:80>
    ServerName barimanager.com
    ServerAlias www.barimanager.com
    DocumentRoot $PROJECT_DIR/public

    <Directory $PROJECT_DIR/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/barimanager_error.log
    CustomLog \${APACHE_LOG_DIR}/barimanager_access.log combined
</VirtualHost>
EOF

# Enable the site
a2ensite barimanager.com.conf

# Enable required Apache modules
a2enmod rewrite
a2enmod ssl

print_status "Restarting Apache..."
systemctl restart apache2

print_status "Setting up SSL certificate..."
# Install certbot if not installed
if ! command -v certbot &> /dev/null; then
    apt update
    apt install -y certbot python3-certbot-apache
fi

# Generate SSL certificate
certbot --apache -d barimanager.com -d www.barimanager.com --non-interactive --agree-tos --email your-email@example.com

print_status "Setting up cron job for Laravel scheduler..."
# Add Laravel scheduler to crontab
(crontab -l 2>/dev/null; echo "* * * * * cd $PROJECT_DIR && php artisan schedule:run >> /dev/null 2>&1") | crontab -

print_status "Creating backup directory..."
mkdir -p /backup/hrms

# Create backup script
cat > /backup/hrms/backup.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
PROJECT_DIR="/var/www/barimanager.com"
BACKUP_DIR="/backup/hrms"

# Database backup
mysqldump -u hrms_user -p barimanager_hrms > $BACKUP_DIR/db_$DATE.sql

# Files backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz $PROJECT_DIR

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
EOF

chmod +x /backup/hrms/backup.sh

# Add backup to crontab (daily at 2 AM)
(crontab -l 2>/dev/null; echo "0 2 * * * /backup/hrms/backup.sh") | crontab -

print_status "Deployment completed successfully! 🎉"

echo ""
echo "📋 Next steps:"
echo "1. Edit .env file with correct database credentials"
echo "2. Update email settings in .env"
echo "3. Test the website at https://barimanager.com"
echo "4. Set up monitoring and alerts"
echo ""
echo "🔐 Database credentials:"
echo "Database: $DB_NAME"
echo "Username: $DB_USER"
echo "Password: $DB_PASS"
echo ""
echo "📁 Project directory: $PROJECT_DIR"
echo "📊 Backup directory: /backup/hrms"
echo ""
echo "✅ Deployment completed!"
