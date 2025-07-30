#!/bin/bash

# cPanel Database Setup Script for HRMS
# Run this script on your cPanel server

echo "🚀 Starting HRMS Database Setup in cPanel..."
echo "=============================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "Laravel artisan file not found. Please run this script from your Laravel project root."
    exit 1
fi

print_status "Laravel project detected"

# Check PHP version
PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
print_status "PHP Version: $PHP_VERSION"

# Check if .env file exists
if [ ! -f ".env" ]; then
    print_warning ".env file not found. Creating from .env.example..."
    if [ -f ".env.example" ]; then
        cp .env.example .env
        print_status ".env file created from .env.example"
    else
        print_error ".env.example file not found. Please create .env file manually."
        exit 1
    fi
fi

# Generate application key
print_status "Generating application key..."
php artisan key:generate --force

# Clear all caches
print_status "Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Check database connection
print_status "Testing database connection..."
if php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connection successful';" 2>/dev/null; then
    print_status "Database connection successful"
else
    print_error "Database connection failed. Please check your .env file configuration."
    echo "Required .env settings:"
    echo "DB_CONNECTION=mysql"
    echo "DB_HOST=localhost"
    echo "DB_PORT=3306"
    echo "DB_DATABASE=barimanager_hrms"
    echo "DB_USERNAME=barimanager_hrms_user"
    echo "DB_PASSWORD=your_password"
    exit 1
fi

# Run migrations
print_status "Running database migrations..."
php artisan migrate --force

if [ $? -eq 0 ]; then
    print_status "Migrations completed successfully"
else
    print_error "Migration failed. Please check your database configuration."
    exit 1
fi

# Run seeders
print_status "Running database seeders..."
php artisan db:seed --force

if [ $? -eq 0 ]; then
    print_status "Seeders completed successfully"
else
    print_warning "Some seeders may have failed. This is normal if data already exists."
fi

# Create storage link
print_status "Creating storage link..."
php artisan storage:link

# Set proper permissions
print_status "Setting file permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env

# Check if admin user exists
print_status "Checking admin user..."
ADMIN_EXISTS=$(php artisan tinker --execute="echo \App\Models\User::where('email', 'admin@barimanager.com')->exists() ? 'true' : 'false';" 2>/dev/null)

if [ "$ADMIN_EXISTS" = "true" ]; then
    print_status "Admin user exists"
else
    print_warning "Admin user not found. You may need to run AdminUserSeeder manually."
fi

# Final verification
print_status "Performing final verification..."

# Check tables
TABLE_COUNT=$(php artisan tinker --execute="echo count(DB::select('SHOW TABLES'));" 2>/dev/null)
print_status "Total tables created: $TABLE_COUNT"

# Check users
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null)
print_status "Total users: $USER_COUNT"

# Check owners
OWNER_COUNT=$(php artisan tinker --execute="echo \App\Models\Owner::count();" 2>/dev/null)
print_status "Total owners: $OWNER_COUNT"

echo ""
echo "🎉 HRMS Database Setup Completed Successfully!"
echo "=============================================="
echo ""
echo "📋 Next Steps:"
echo "1. Test your website: https://barimanager.com"
echo "2. Login with admin credentials"
echo "3. Configure additional settings"
echo ""
echo "🔧 Useful Commands:"
echo "- Check migration status: php artisan migrate:status"
echo "- Run specific seeder: php artisan db:seed --class=AdminUserSeeder"
echo "- Check logs: tail -f storage/logs/laravel.log"
echo ""
echo "📞 If you encounter issues:"
echo "- Check .env file configuration"
echo "- Verify database credentials"
echo "- Check Laravel logs"
echo "- Contact support if needed"
echo ""

print_status "Setup completed! Your HRMS application is ready to use." 