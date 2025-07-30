@echo off
echo Setting up SSH configuration for Cursor Remote-SSH...

REM Create .ssh directory if it doesn't exist
if not exist "%USERPROFILE%\.ssh" mkdir "%USERPROFILE%\.ssh"

REM Backup existing config
if exist "%USERPROFILE%\.ssh\config" (
    echo Backing up existing SSH config...
    copy "%USERPROFILE%\.ssh\config" "%USERPROFILE%\.ssh\config.backup"
)

REM Create new SSH config
echo Creating SSH configuration for barimanager.com...

(
echo # SSH Configuration for Cursor Remote-SSH
echo # Domain: barimanager.com ^| IP: 139.99.33.181
echo.
echo # HRMS Development Server Configuration
echo Host barimanager
echo     HostName 139.99.33.181
echo     User barimanager
echo     Port 22
echo     PreferredAuthentications password
echo     ServerAliveInterval 60
echo     ServerAliveCountMax 3
echo     ConnectTimeout 30
echo     StrictHostKeyChecking no
echo     UserKnownHostsFile /dev/null
echo.
echo # Alternative configuration for different connection methods
echo Host barimanager-sftp
echo     HostName 139.99.33.181
echo     User barimanager
echo     Port 22
echo     PreferredAuthentications password
echo     ServerAliveInterval 60
echo     ServerAliveCountMax 3
echo     ConnectTimeout 30
echo     StrictHostKeyChecking no
echo     UserKnownHostsFile /dev/null
echo     Subsystem sftp internal-sftp
echo.
echo # FTP fallback configuration
echo Host barimanager-ftp
echo     HostName 139.99.33.181
echo     User barimanager
echo     Port 21
echo     PreferredAuthentications password
echo     ConnectTimeout 30
) > "%USERPROFILE%\.ssh\config"

echo SSH configuration created successfully!
echo.
echo Configuration details:
echo - Host: barimanager
echo - HostName: 139.99.33.181
echo - User: barimanager
echo - Port: 22
echo.
echo Next steps:
echo 1. Open Cursor
echo 2. Go to File ^> Open Folder
echo 3. Click "Connect to SSH Host"
echo 4. Enter: barimanager@139.99.33.181
echo 5. Enter password: *euSlN(C71+2LRz9
echo.
pause
