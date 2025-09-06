#!/bin/bash
# Deploy HRMS to cPanel Server
# Run this script to deploy the application to barimanager.com

echo "🚀 Deploying HRMS to cPanel Server..."

# Server details
SERVER="139.99.33.181"
USER="barimanager"
PROJECT_PATH="/home/barimanager/hrms"
WEB_ROOT="/home/barimanager/public_html"
TENANTS_PATH="/home/barimanager/public_html/tenants"

echo "📁 Creating necessary directories on server..."

# Create directories via SSH
ssh $USER@$SERVER << EOF
# Create project directory if it doesn't exist
mkdir -p $PROJECT_PATH

# Create tenants/nid directory in web root (public_html)
mkdir -p $TENANTS_PATH/nid

# Create storage directories in project
mkdir -p $PROJECT_PATH/storage/app/public/tenants/nid

# Set proper permissions
chmod -R 755 $TENANTS_PATH/
chmod -R 755 $PROJECT_PATH/storage/
chmod -R 755 $PROJECT_PATH/storage/app/public/tenants/

# Create symbolic link from web root to project storage
if [ ! -L "$WEB_ROOT/storage" ]; then
    ln -s ../hrms/storage/app/public $WEB_ROOT/storage
    echo "✅ Created symbolic link: $WEB_ROOT/storage -> ../hrms/storage/app/public"
fi

echo "✅ Directories created successfully"
echo "📁 Structure:"
echo "   $PROJECT_PATH/ (Main Laravel project)"
echo "   $WEB_ROOT/ (Web root)"
echo "   $TENANTS_PATH/nid/ (Direct image access)"
echo "   $WEB_ROOT/storage/ (symlink to hrms/storage/app/public)"
EOF

echo "📤 Uploading files to server..."
# Upload the entire project to hrms directory (excluding node_modules, .git, etc.)
rsync -avz --exclude 'node_modules' --exclude '.git' --exclude 'storage/logs' --exclude '.env' ./ $USER@$SERVER:$PROJECT_PATH/

echo "🔧 Running Laravel commands on server..."
ssh $USER@$SERVER << 'EOF'
cd /home/barimanager/hrms

# Install dependencies
composer install --no-dev --optimize-autoloader

# Generate app key if not exists
if [ -z "$(grep 'APP_KEY=' .env)" ]; then
    php artisan key:generate
fi

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run migrations
php artisan migrate --force

# Set permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

echo "✅ Deployment completed successfully!"
EOF

echo "🎉 HRMS deployed to cPanel server successfully!"
echo "🌐 Application URL: https://barimanager.com"
echo "📧 Check logs: storage/logs/laravel.log"
