@echo off
chcp 65001 >nul
echo 🔄 Laravel Project Sync Tool
echo.

set LOCAL_PATH=D:\BariManager\hrms_backend
set REMOTE_PATH=/home/barimanager/hrms
set REMOTE_HOST=barimanager@139.99.33.181

:menu
echo Choose an option:
echo 1) Sync local → remote
echo 2) Sync remote → local
echo 3) Run migration on remote
echo 4) Clear cache on remote
echo 5) View remote logs
echo 6) Exit
echo.

set /p choice="Enter your choice (1-6): "

if "%choice%"=="1" goto sync_to_remote
if "%choice%"=="2" goto sync_from_remote
if "%choice%"=="3" goto run_migration
if "%choice%"=="4" goto clear_cache
if "%choice%"=="5" goto view_logs
if "%choice%"=="6" goto exit
goto menu

:sync_to_remote
echo 📤 Syncing local changes to remote server...
rsync -avz --exclude=vendor/ --exclude=node_modules/ --exclude=storage/logs/ --exclude=storage/framework/cache/ --exclude=.git/ "%LOCAL_PATH%/" "%REMOTE_HOST%:%REMOTE_PATH%/"
goto end

:sync_from_remote
echo 📥 Syncing remote changes to local...
rsync -avz --exclude=vendor/ --exclude=node_modules/ --exclude=storage/logs/ --exclude=storage/framework/cache/ --exclude=.git/ "%REMOTE_HOST%:%REMOTE_PATH%/" "%LOCAL_PATH%/"
goto end

:run_migration
echo 🚀 Running migration on remote server...
ssh "%REMOTE_HOST%" "cd %REMOTE_PATH% && php artisan migrate"
goto end

:clear_cache
echo 🧹 Clearing cache on remote server...
ssh "%REMOTE_HOST%" "cd %REMOTE_PATH% && php artisan config:clear && php artisan cache:clear"
goto end

:view_logs
echo 📋 Viewing remote logs...
ssh "%REMOTE_HOST%" "cd %REMOTE_PATH% && tail -f storage/logs/laravel.log"
goto end

:end
echo ✅ Operation completed!
pause
goto menu

:exit
echo 👋 Goodbye!
pause 