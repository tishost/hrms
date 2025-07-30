#!/bin/bash

# HRMS System - Live Development Setup Script
# Domain: barimanager.com

echo "🚀 Setting up Live Development Environment..."

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

print_step "1. Setting up development environment..."

# Create development .env file
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        print_status "Created .env file from .env.example"
    else
        print_error ".env.example file not found"
        exit 1
    fi
fi

print_step "2. Configuring development settings..."

# Update .env for development
sed -i 's/APP_ENV=production/APP_ENV=local/g' .env
sed -i 's/APP_DEBUG=false/APP_DEBUG=true/g' .env
sed -i 's/LOG_LEVEL=error/LOG_LEVEL=debug/g' .env
sed -i 's/MAIL_MAILER=smtp/MAIL_MAILER=log/g' .env

print_status "Updated .env for development mode"

print_step "3. Installing development dependencies..."

# Install Composer dependencies with dev packages
composer install --dev --no-interaction

if [ $? -ne 0 ]; then
    print_error "Composer installation failed"
    exit 1
fi

print_step "4. Installing NPM dependencies..."

# Install NPM dependencies
if [ -f "package.json" ]; then
    npm install
    npm run dev
else
    print_warning "No package.json found, skipping NPM build"
fi

print_step "5. Setting up development database..."

# Create development database if it doesn't exist
read -p "Do you want to create a development database? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "Please create development database manually:"
    echo "1. Go to cPanel → MySQL Databases"
    echo "2. Create database: barimanager_hrms_dev"
    echo "3. Create user: hrms_user_dev"
    echo "4. Add user to database with ALL PRIVILEGES"
    echo "5. Update .env file with development database credentials"
fi

print_step "6. Clearing caches for development..."

# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

print_step "7. Setting up Git repository..."

# Initialize Git if not already done
if [ ! -d ".git" ]; then
    git init
    print_status "Initialized Git repository"
fi

# Create .gitignore if not exists
if [ ! -f ".gitignore" ]; then
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
    print_status "Created .gitignore file"
fi

print_step "8. Setting file permissions..."

# Set development permissions
chmod -R 755 .
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod 644 .env

print_step "9. Creating development .htaccess..."

# Create development .htaccess
cat > public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Development: Show errors
    php_flag display_errors on
    php_value error_reporting E_ALL

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

# Development Headers
<IfModule mod_headers.c>
    Header always set X-Development-Mode "true"
</IfModule>
EOF

print_step "10. Setting up development monitoring..."

# Create log monitoring script
cat > /tmp/watch_logs.sh << 'EOF'
#!/bin/bash
echo "Watching Laravel logs..."
tail -f storage/logs/laravel.log
EOF

chmod +x /tmp/watch_logs.sh

print_step "11. Creating development backup script..."

# Create development backup script
cat > /tmp/dev_backup.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
PROJECT_DIR="$(pwd)"
BACKUP_DIR="/home/$(whoami)/backup/dev"

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup (if development database exists)
if mysql -u root -p -e "USE barimanager_hrms_dev;" 2>/dev/null; then
    mysqldump -u root -p barimanager_hrms_dev > $BACKUP_DIR/dev_db_$DATE.sql
    echo "Database backup completed"
fi

# Files backup (excluding vendor and node_modules)
tar --exclude='vendor' --exclude='node_modules' --exclude='storage/logs' \
    -czf $BACKUP_DIR/dev_files_$DATE.tar.gz $PROJECT_DIR

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Development backup completed: $DATE"
EOF

chmod +x /tmp/dev_backup.sh

print_step "12. Setting up development workflow..."

# Create development workflow script
cat > /tmp/dev_workflow.sh << 'EOF'
#!/bin/bash
echo "🚀 Starting development workflow..."

# Pull latest changes
git pull origin main

# Install dependencies
composer install
npm install

# Build assets
npm run dev

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate

echo "✅ Development environment ready!"
echo "📝 Make your changes and test them..."
echo "💾 Don't forget to commit: git add . && git commit -m 'Update' && git push"
EOF

chmod +x /tmp/dev_workflow.sh

print_status "Development setup completed successfully! 🎉"

echo ""
echo "📋 Development Environment Ready!"
echo ""
echo "🔧 Quick Commands:"
echo "  - Watch logs: tail -f storage/logs/laravel.log"
echo "  - Start workflow: /tmp/dev_workflow.sh"
echo "  - Backup: /tmp/dev_backup.sh"
echo "  - Clear caches: php artisan config:clear"
echo ""
echo "📝 Next Steps:"
echo "1. Update .env with development database credentials"
echo "2. Test the website at https://barimanager.com"
echo "3. Start development workflow"
echo "4. Set up Git remote repository"
echo ""
echo "🔍 Monitoring:"
echo "- Logs: storage/logs/laravel.log"
echo "- Error logs: cPanel → Error Logs"
echo "- Database: cPanel → phpMyAdmin"
echo ""
echo "✅ Live development setup completed!"
