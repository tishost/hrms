#!/bin/bash

# HRMS cPanel Development Workflow (No SSH Required)
# Domain: barimanager.com | IP: 139.99.33.181

echo "🚀 Setting up cPanel Development Workflow..."

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

# Server information
SERVER_IP="139.99.33.181"
USERNAME="barimanager"
PASSWORD="*euSlN(C71+2LRz9"
PROJECT_PATH="/home/barimanager/public_html"

print_step "1. Creating cPanel development workflow..."

# Create development workflow script
cat > /tmp/cpanel_dev_workflow.sh << 'EOF'
#!/bin/bash
echo "🚀 Starting cPanel Development Workflow..."

# Go to project directory
cd /home/barimanager/public_html

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Not in Laravel project directory"
    exit 1
fi

echo "✅ Laravel project found"

# Pull latest changes (if git is available)
if [ -d ".git" ]; then
    echo "📥 Pulling latest changes..."
    git pull origin main
else
    echo "⚠️  Git repository not found"
fi

# Install dependencies
echo "📦 Installing Composer dependencies..."
composer install --dev --no-interaction

# Install NPM dependencies
if [ -f "package.json" ]; then
    echo "📦 Installing NPM dependencies..."
    npm install
    npm run dev
else
    echo "⚠️  No package.json found"
fi

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 755 .
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

echo "✅ Development environment ready!"
echo ""
echo "📋 Next steps:"
echo "1. Edit files via cPanel File Manager"
echo "2. Run commands in cPanel Terminal"
echo "3. Test at https://barimanager.com"
echo "4. Check logs: tail -f storage/logs/laravel.log"
EOF

chmod +x /tmp/cpanel_dev_workflow.sh

print_step "2. Creating development commands..."

# Create development commands
cat > /tmp/cpanel_dev_commands.sh << 'EOF'
#!/bin/bash
echo "🔧 cPanel Development Commands"
echo "=============================="

echo ""
echo "📋 Available Commands:"
echo "  dev-start    - Setup development environment"
echo "  dev-logs     - Watch Laravel logs"
echo "  dev-clear    - Clear all caches"
echo "  dev-status   - Check project status"
echo "  dev-backup   - Create backup"
echo "  dev-test     - Run tests"
echo "  dev-migrate  - Run migrations"
echo "  dev-seed     - Seed database"
echo "  dev-tinker   - Open Laravel tinker"
echo ""

# Add aliases to bashrc
cat >> ~/.bashrc << 'ALIASES'
# HRMS Development Aliases
alias dev-start='cd /home/barimanager/public_html && git pull origin main && composer install && npm run dev && php artisan config:clear'
alias dev-logs='tail -f /home/barimanager/public_html/storage/logs/laravel.log'
alias dev-clear='php artisan config:clear && php artisan route:clear && php artisan view:clear'
alias dev-backup='cp -r /home/barimanager/public_html /home/barimanager/backup/$(date +%Y%m%d_%H%M%S)'
alias dev-status='cd /home/barimanager/public_html && git status && echo "--- Logs ---" && tail -5 storage/logs/laravel.log'
alias dev-test='cd /home/barimanager/public_html && php artisan test'
alias dev-migrate='cd /home/barimanager/public_html && php artisan migrate'
alias dev-seed='cd /home/barimanager/public_html && php artisan db:seed'
alias dev-tinker='cd /home/barimanager/public_html && php artisan tinker'
ALIASES

echo "✅ Development commands added to ~/.bashrc"
echo "💡 Run 'source ~/.bashrc' to load commands"
EOF

chmod +x /tmp/cpanel_dev_commands.sh

print_step "3. Creating backup script..."

# Create backup script
cat > /home/barimanager/backup/cpanel_backup.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
PROJECT_DIR="/home/barimanager/public_html"
BACKUP_DIR="/home/barimanager/backup"

# Create backup directory
mkdir -p $BACKUP_DIR

echo "📦 Creating backup..."

# Database backup
if mysql -u barimanager -p -e "USE barimanager_hrms_dev;" 2>/dev/null; then
    mysqldump -u barimanager -p barimanager_hrms_dev > $BACKUP_DIR/cpanel_db_$DATE.sql
    echo "✅ Database backup completed"
else
    echo "⚠️  Development database not found, skipping database backup"
fi

# Files backup (excluding vendor and node_modules)
tar --exclude='vendor' --exclude='node_modules' --exclude='storage/logs' \
    -czf $BACKUP_DIR/cpanel_files_$DATE.tar.gz $PROJECT_DIR

# Keep only last 7 days of backups
find $BACKUP_DIR -name "cpanel_*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "cpanel_*.tar.gz" -mtime +7 -delete

echo "✅ Backup completed: $DATE"
echo "📁 Backup location: $BACKUP_DIR"
EOF

chmod +x /home/barimanager/backup/cpanel_backup.sh

print_step "4. Creating monitoring script..."

# Create monitoring script
cat > /tmp/cpanel_monitor.sh << 'EOF'
#!/bin/bash
echo "📊 cPanel Development Monitoring"
echo "================================"

echo ""
echo "1. Laravel Logs (last 10 lines):"
tail -10 /home/barimanager/public_html/storage/logs/laravel.log

echo ""
echo "2. Disk Usage:"
df -h | grep -E "(Filesystem|/home)"

echo ""
echo "3. Memory Usage:"
free -h

echo ""
echo "4. PHP Memory Limit:"
php -i | grep memory_limit

echo ""
echo "5. Recent Git Commits:"
cd /home/barimanager/public_html && git log --oneline -5 2>/dev/null || echo "Git repository not found"

echo ""
echo "6. Database Size:"
mysql -u barimanager -p -e "SELECT table_schema AS 'Database', ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)' FROM information_schema.tables WHERE table_schema LIKE 'barimanager%';" 2>/dev/null || echo "Database info not available"

echo ""
echo "7. File Permissions:"
ls -la /home/barimanager/public_html/storage/
ls -la /home/barimanager/public_html/bootstrap/cache/
EOF

chmod +x /tmp/cpanel_monitor.sh

print_step "5. Creating SFTP connection guide..."

# Create SFTP connection guide
cat > cpanel-sftp-guide.md << EOF
# cPanel SFTP Connection Guide
## Domain: barimanager.com | IP: 139.99.33.181

### Connection Information
- **Protocol**: SFTP
- **Host**: 139.99.33.181
- **Username**: barimanager
- **Password**: *euSlN(C71+2LRz9
- **Port**: 22
- **Remote Path**: /home/barimanager/public_html

### Connection Methods

#### Method 1: Cursor SFTP
1. Open Cursor
2. File → Open Folder
3. Click "Connect to SFTP"
4. Enter connection details above

#### Method 2: FileZilla
1. Download FileZilla
2. File → Site Manager
3. Add new site with SFTP details
4. Connect and navigate to public_html

#### Method 3: cPanel File Manager
1. Go to https://barimanager.com/cpanel
2. Login with barimanager / *euSlN(C71+2LRz9
3. Go to File Manager
4. Navigate to public_html

### Development Workflow

#### Option A: cPanel File Manager + Terminal
1. Edit files in cPanel File Manager
2. Run commands in cPanel Terminal
3. Test at https://barimanager.com

#### Option B: Local Development + Upload
1. Develop locally
2. Upload via SFTP/FTP
3. Test on server

#### Option C: Git + Server Pull
1. Push changes to Git
2. Pull on server via Terminal
3. Run composer install and npm run dev

### Quick Commands (cPanel Terminal)
\`\`\`bash
# Load development commands
source ~/.bashrc

# Start development
dev-start

# Watch logs
dev-logs

# Clear caches
dev-clear

# Check status
dev-status

# Create backup
dev-backup
\`\`\`

### Troubleshooting
- If SFTP fails, use cPanel File Manager
- If Terminal not available, use cPanel Terminal
- Check logs: tail -f storage/logs/laravel.log
- Clear caches: dev-clear
EOF

print_step "6. Creating development environment setup..."

# Create development environment setup
cat > /tmp/cpanel_dev_env.sh << 'EOF'
#!/bin/bash
echo "🔧 Setting up cPanel Development Environment..."

# Go to project directory
cd /home/barimanager/public_html

# Create development .env if not exists
if [ ! -f .env ]; then
    if [ -f dev-env.txt ]; then
        cp dev-env.txt .env
        echo "✅ Created .env from dev-env.txt"
    else
        echo "❌ dev-env.txt not found"
        exit 1
    fi
fi

# Generate app key
php artisan key:generate

# Update .env for development
sed -i 's/APP_ENV=production/APP_ENV=local/g' .env
sed -i 's/APP_DEBUG=false/APP_DEBUG=true/g' .env
sed -i 's/LOG_LEVEL=error/LOG_LEVEL=debug/g' .env

# Create development database
mysql -u barimanager -p -e "CREATE DATABASE IF NOT EXISTS barimanager_hrms_dev;" 2>/dev/null || echo "⚠️  Could not create development database"

# Run migrations
php artisan migrate --force

# Run seeders
php artisan db:seed --force

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Set permissions
chmod -R 755 .
chmod -R 775 storage
chmod -R 775 bootstrap/cache

echo "✅ Development environment setup completed!"
EOF

chmod +x /tmp/cpanel_dev_env.sh

print_status "cPanel development workflow setup completed! 🎉"

echo ""
echo "📋 Next Steps:"
echo "1. Go to cPanel → Terminal"
echo "2. Run: /tmp/cpanel_dev_workflow.sh"
echo "3. Run: /tmp/cpanel_dev_env.sh"
echo "4. Run: /tmp/cpanel_dev_commands.sh"
echo "5. Use cPanel File Manager for editing"
echo ""
echo "🔧 Quick Commands:"
echo "  - dev-start: Setup development environment"
echo "  - dev-logs: Watch Laravel logs"
echo "  - dev-clear: Clear all caches"
echo "  - dev-status: Check project status"
echo "  - dev-backup: Create backup"
echo ""
echo "📁 Important Files:"
echo "  - cpanel-sftp-guide.md: Connection instructions"
echo "  - /tmp/cpanel_dev_workflow.sh: Development workflow"
echo "  - /home/barimanager/backup/cpanel_backup.sh: Backup script"
echo ""
echo "✅ cPanel development workflow setup completed!"
