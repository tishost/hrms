#!/bin/bash

# Cursor Remote Development Connection Script
# Domain: barimanager.com | IP: 139.99.33.181

echo "🚀 Setting up Cursor Remote Development Connection..."

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

print_step "1. Testing SSH connection..."

# Test SSH connection
if ssh -o ConnectTimeout=10 -o BatchMode=yes $USERNAME@$SERVER_IP exit 2>/dev/null; then
    print_status "SSH connection successful"
else
    print_warning "SSH connection failed. Please check your SSH setup."
    echo "You can still use SFTP connection in Cursor."
fi

print_step "2. Creating Cursor connection configuration..."

# Create Cursor connection config
cat > cursor-connection.json << EOF
{
  "name": "HRMS Development",
  "type": "ssh",
  "host": "$SERVER_IP",
  "username": "$USERNAME",
  "port": 22,
  "remotePath": "$PROJECT_PATH",
  "connectTimeout": 10000,
  "idleTimeout": 30000,
  "keepalive": 60,
  "algorithms": {
    "kex": [
      "diffie-hellman-group14-sha256",
      "diffie-hellman-group16-sha512",
      "diffie-hellman-group18-sha512"
    ],
    "cipher": [
      "aes128-ctr",
      "aes192-ctr",
      "aes256-ctr"
    ],
    "serverHostKey": [
      "ssh-rsa",
      "ssh-ed25519"
    ],
    "hmac": [
      "hmac-sha2-256",
      "hmac-sha2-512"
    ]
  }
}
EOF

print_status "Created cursor-connection.json"

print_step "3. Creating development aliases..."

# Create development aliases
cat > ~/.cursor_aliases << 'EOF'
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
EOF

print_status "Created development aliases"

print_step "4. Setting up development environment..."

# Create development setup script
cat > /tmp/cursor_dev_setup.sh << 'EOF'
#!/bin/bash
echo "🚀 Setting up Cursor development environment..."

# Go to project directory
cd /home/barimanager/public_html

# Install dependencies
composer install --dev --no-interaction

# Install NPM dependencies
if [ -f "package.json" ]; then
    npm install
    npm run dev
fi

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Set permissions
chmod -R 755 .
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Create storage link
php artisan storage:link

echo "✅ Cursor development environment ready!"
EOF

chmod +x /tmp/cursor_dev_setup.sh

print_step "5. Creating backup script..."

# Create backup script
cat > /home/barimanager/backup/cursor_backup.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
PROJECT_DIR="/home/barimanager/public_html"
BACKUP_DIR="/home/barimanager/backup"

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u barimanager -p barimanager_hrms_dev > $BACKUP_DIR/cursor_db_$DATE.sql 2>/dev/null || echo "Database backup skipped"

# Files backup (excluding vendor and node_modules)
tar --exclude='vendor' --exclude='node_modules' --exclude='storage/logs' \
    -czf $BACKUP_DIR/cursor_files_$DATE.tar.gz $PROJECT_DIR

# Keep only last 7 days of backups
find $BACKUP_DIR -name "cursor_*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "cursor_*.tar.gz" -mtime +7 -delete

echo "Cursor development backup completed: $DATE"
EOF

chmod +x /home/barimanager/backup/cursor_backup.sh

print_step "6. Creating monitoring script..."

# Create monitoring script
cat > /tmp/cursor_monitor.sh << 'EOF'
#!/bin/bash
echo "📊 Cursor Development Monitoring"
echo "================================"

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
cd /home/barimanager/public_html && git log --oneline -5

echo ""
echo "6. Database Size:"
mysql -u barimanager -p -e "SELECT table_schema AS 'Database', ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)' FROM information_schema.tables WHERE table_schema LIKE 'barimanager%';" 2>/dev/null || echo "Database info not available"
EOF

chmod +x /tmp/cursor_monitor.sh

print_step "7. Creating Cursor connection instructions..."

# Create connection instructions
cat > cursor-connection-instructions.md << EOF
# Cursor Remote Development Connection Instructions

## Server Information
- **Host**: $SERVER_IP
- **Username**: $USERNAME
- **Password**: $PASSWORD
- **Project Path**: $PROJECT_PATH

## Connection Methods

### Method 1: SSH Connection (Recommended)
1. Open Cursor
2. Go to File → Open Folder
3. Click "Connect to SSH Host"
4. Enter connection details:
   - Host: $SERVER_IP
   - Username: $USERNAME
   - Port: 22
   - Remote Path: $PROJECT_PATH

### Method 2: SFTP Connection
1. Open Cursor
2. Go to File → Open Folder
3. Click "Connect to SFTP"
4. Enter connection details:
   - Host: $SERVER_IP
   - Username: $USERNAME
   - Password: $PASSWORD
   - Port: 22
   - Remote Path: $PROJECT_PATH

## Quick Commands (After Connection)
\`\`\`bash
# Load aliases
source ~/.cursor_aliases

# Start development
dev-start

# Watch logs
dev-logs

# Clear caches
dev-clear

# Check status
dev-status

# Run backup
dev-backup
\`\`\`

## Development Workflow
1. Connect to server via Cursor
2. Run \`dev-start\` to setup environment
3. Make changes in Cursor
4. Test changes at https://barimanager.com
5. Commit changes: \`git add . && git commit -m "Update" && git push\`
6. Run \`dev-backup\` before major changes

## Troubleshooting
- If connection fails, try SFTP instead of SSH
- Check server status: \`sudo systemctl status ssh\`
- Check logs: \`tail -f storage/logs/laravel.log\`
- Clear caches: \`dev-clear\`
EOF

print_status "Created connection instructions"

print_step "8. Testing development environment..."

# Test if we can access the project directory
if ssh -o ConnectTimeout=10 $USERNAME@$SERVER_IP "test -d $PROJECT_PATH" 2>/dev/null; then
    print_status "Project directory accessible"
else
    print_warning "Project directory not found. Please check the path: $PROJECT_PATH"
fi

print_status "Cursor remote development setup completed! 🎉"

echo ""
echo "📋 Next Steps:"
echo "1. Open Cursor"
echo "2. Go to File → Open Folder"
echo "3. Click 'Connect to SSH Host' or 'Connect to SFTP'"
echo "4. Use the connection details from cursor-connection-instructions.md"
echo "5. Run: source ~/.cursor_aliases"
echo "6. Run: dev-start"
echo ""
echo "🔧 Quick Commands:"
echo "  - dev-start: Setup development environment"
echo "  - dev-logs: Watch Laravel logs"
echo "  - dev-clear: Clear all caches"
echo "  - dev-status: Check project status"
echo "  - dev-backup: Create backup"
echo ""
echo "📁 Important Files:"
echo "  - cursor-connection.json: Connection configuration"
echo "  - cursor-connection-instructions.md: Detailed instructions"
echo "  - ~/.cursor_aliases: Development aliases"
echo ""
echo "✅ Cursor remote development setup completed!"
