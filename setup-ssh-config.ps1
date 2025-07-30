# PowerShell Script to Setup SSH Configuration for Cursor Remote-SSH
# Domain: barimanager.com | IP: 139.99.33.181

Write-Host "Setting up SSH configuration for Cursor Remote-SSH..." -ForegroundColor Green

# Create .ssh directory if it doesn't exist
$sshDir = "$env:USERPROFILE\.ssh"
if (!(Test-Path $sshDir)) {
    New-Item -ItemType Directory -Path $sshDir -Force
    Write-Host "Created .ssh directory" -ForegroundColor Yellow
}

# Backup existing config
$configFile = "$sshDir\config"
if (Test-Path $configFile) {
    $backupFile = "$configFile.backup"
    Copy-Item $configFile $backupFile
    Write-Host "Backed up existing SSH config to $backupFile" -ForegroundColor Yellow
}

# Create SSH configuration content
$sshConfig = @"
# SSH Configuration for Cursor Remote-SSH
# Domain: barimanager.com | IP: 139.99.33.181

# HRMS Development Server Configuration
Host barimanager
    HostName 139.99.33.181
    User barimanager
    Port 22
    PreferredAuthentications password
    ServerAliveInterval 60
    ServerAliveCountMax 3
    ConnectTimeout 30
    StrictHostKeyChecking no
    UserKnownHostsFile /dev/null

# Alternative configuration for different connection methods
Host barimanager-sftp
    HostName 139.99.33.181
    User barimanager
    Port 22
    PreferredAuthentications password
    ServerAliveInterval 60
    ServerAliveCountMax 3
    ConnectTimeout 30
    StrictHostKeyChecking no
    UserKnownHostsFile /dev/null
    Subsystem sftp internal-sftp

# FTP fallback configuration
Host barimanager-ftp
    HostName 139.99.33.181
    User barimanager
    Port 21
    PreferredAuthentications password
    ConnectTimeout 30
"@

# Write configuration to file
$sshConfig | Out-File -FilePath $configFile -Encoding UTF8
Write-Host "SSH configuration created successfully!" -ForegroundColor Green

Write-Host ""
Write-Host "Configuration details:" -ForegroundColor Cyan
Write-Host "- Host: barimanager" -ForegroundColor White
Write-Host "- HostName: 139.99.33.181" -ForegroundColor White
Write-Host "- User: barimanager" -ForegroundColor White
Write-Host "- Port: 22" -ForegroundColor White

Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Open Cursor" -ForegroundColor White
Write-Host "2. Go to File → Open Folder" -ForegroundColor White
Write-Host "3. Click 'Connect to SSH Host'" -ForegroundColor White
Write-Host "4. Enter: barimanager@139.99.33.181" -ForegroundColor White
Write-Host "5. Enter password: *euSlN(C71+2LRz9" -ForegroundColor White

Write-Host ""
Write-Host "Alternative connection methods:" -ForegroundColor Cyan
Write-Host "- SFTP: barimanager-sftp" -ForegroundColor White
Write-Host "- FTP: barimanager-ftp" -ForegroundColor White

Write-Host ""
Write-Host "SSH config file location: $configFile" -ForegroundColor Yellow

Read-Host "Press Enter to continue..."
