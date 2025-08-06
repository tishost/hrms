#!/bin/bash

# Local Laravel Project Sync Script
# This script syncs local development with remote server

LOCAL_PATH="D:/BariManager/hrms_backend"
REMOTE_PATH="/home/barimanager/hrms"
REMOTE_HOST="barimanager@139.99.33.181"

echo "🔄 Syncing Laravel Project..."

# Function to sync from local to remote
sync_to_remote() {
    echo "📤 Syncing local changes to remote server..."
    rsync -avz --exclude='vendor/' --exclude='node_modules/' --exclude='storage/logs/' --exclude='storage/framework/cache/' --exclude='.git/' "$LOCAL_PATH/" "$REMOTE_HOST:$REMOTE_PATH/"
}

# Function to sync from remote to local
sync_from_remote() {
    echo "📥 Syncing remote changes to local..."
    rsync -avz --exclude='vendor/' --exclude='node_modules/' --exclude='storage/logs/' --exclude='storage/framework/cache/' --exclude='.git/' "$REMOTE_HOST:$REMOTE_PATH/" "$LOCAL_PATH/"
}

# Function to run remote commands
run_remote_command() {
    echo "🚀 Running command on remote server: $1"
    ssh "$REMOTE_HOST" "cd $REMOTE_PATH && $1"
}

# Main menu
echo "Choose an option:"
echo "1) Sync local → remote"
echo "2) Sync remote → local"
echo "3) Run migration on remote"
echo "4) Clear cache on remote"
echo "5) Restart services on remote"
echo "6) View remote logs"

read -p "Enter your choice (1-6): " choice

case $choice in
    1)
        sync_to_remote
        ;;
    2)
        sync_from_remote
        ;;
    3)
        run_remote_command "php artisan migrate"
        ;;
    4)
        run_remote_command "php artisan config:clear && php artisan cache:clear"
        ;;
    5)
        run_remote_command "sudo systemctl restart nginx && sudo systemctl restart php-fpm"
        ;;
    6)
        run_remote_command "tail -f storage/logs/laravel.log"
        ;;
    *)
        echo "Invalid choice!"
        ;;
esac

echo "✅ Sync completed!" 