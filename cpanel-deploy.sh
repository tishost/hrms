#!/bin/bash

# HRMS System - cPanel Deployment Script
# Domain: barimanager.com

echo "🚀 Starting HRMS System cPanel Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
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

print_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "Please run this script from the Laravel project root directory"
    exit 1
fi

print_step "1. Checking cPanel environment..."

# Check PHP version
PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
if [[ $(echo "$PHP_VERSION >= 8.1" | bc -l) -eq 1 ]]; then
    print_status "PHP version $PHP_VERSION is compatible"
else
    print_warning "PHP version $PHP_VERSION detected. PHP 8.1+ recommended"
fi

print_step "2. Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

if [ $? -ne 0 ]; then
    print_error "Composer installation failed"
    exit 1
fi

print_step "3. Setting up environment file..."
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        print_status "Created .env file from .env.example"
    else
        print_error ".env.example file not found"
        exit 1
    fi
fi

print_step "4. Generating application key..."
php artisan key:generate --force

print_step "5. Optimizing Laravel for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

print_step "6. Creating storage link..."
php artisan storage:link

print_step "7. Setting file permissions..."
chmod -R 755 .
chmod -R 775 storage
chmod -R 775 bootstrap/cache

print_step "8. Installing NPM dependencies..."
if [ -f "package.json" ]; then
    npm install --production
    npm run build
else
    print_warning "No package.json found, skipping NPM build"
fi

print_step "9. Database setup instructions..."

echo ""
echo "📋 Database Setup Required:"
echo "1. Go to cPanel → MySQL Databases"
echo "2. Create database: barimanager_hrms"
echo "3. Create user: hrms_user"
echo "4. Add user to database with ALL PRIVILEGES"
echo "5. Update .env file with database credentials"
echo ""

print_step "10. Running database migrations..."
read -p "Have you set up the database? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    php artisan db:seed --force
else
    print_warning "Skipping database setup. Run manually:"
    echo "php artisan migrate --force"
    echo "php artisan db:seed --force"
fi

print_step "11. Creating .htaccess file..."
cat > public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Force HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

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

# Security Headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Cache Control
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/ico "access plus 1 year"
    ExpiresByType image/icon "access plus 1 year"
    ExpiresByType text/plain "access plus 1 month"
    ExpiresByType application/pdf "access plus 1 month"
    ExpiresByType application/x-shockwave-flash "access plus 1 month"
</IfModule>

# Gzip Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
EOF

print_step "12. Creating cron job instructions..."

echo ""
echo "📋 Cron Jobs Setup Required:"
echo "Go to cPanel → Cron Jobs and add:"
echo ""
echo "Laravel Scheduler (every minute):"
echo "* * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1"
echo ""
echo "Database Backup (daily at 2 AM):"
echo "0 2 * * * mysqldump -u username_dbname -p'password' username_dbname > /home/username/backup/db_\$(date +\%Y\%m\%d).sql"
echo ""

print_step "13. SSL Certificate instructions..."

echo ""
echo "📋 SSL Certificate Setup:"
echo "1. Go to cPanel → SSL/TLS"
echo "2. Install SSL Certificate for barimanager.com"
echo "3. Force HTTPS Redirect"
echo ""

print_status "Deployment completed successfully! 🎉"

echo ""
echo "📋 Next steps:"
echo "1. Update .env file with correct database credentials"
echo "2. Set up SSL certificate in cPanel"
echo "3. Configure cron jobs in cPanel"
echo "4. Test the website at https://barimanager.com"
echo "5. Set up email accounts in cPanel"
echo ""
echo "🔧 Troubleshooting:"
echo "- Check error logs: cPanel → Error Logs"
echo "- Check Laravel logs: storage/logs/laravel.log"
echo "- Test database connection via phpMyAdmin"
echo ""
echo "✅ cPanel deployment completed!"
