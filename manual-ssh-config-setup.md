# 🚀 Manual SSH Configuration Setup for Cursor
## Domain: barimanager.com | IP: 139.99.33.181

### ✅ SSH Configuration Setup

#### ১. SSH Config File Location

**Windows এ SSH config file এর location:**
```
C:\Users\samiu\.ssh\config
```

#### ২. Manual SSH Config Setup

**Step 1: Create .ssh directory**
```cmd
mkdir "C:\Users\samiu\.ssh"
```

**Step 2: Create SSH config file**
```cmd
notepad "C:\Users\samiu\.ssh\config"
```

**Step 3: Add configuration to config file**

নিচের configuration কপি করে config file এ paste করুন:

```
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
```

**Step 4: Save the file**

#### ৩. Cursor এ Connection Setup

**SSH config setup করার পর:**

1. **Cursor Open করুন**
2. **File → Open Folder**
3. **"Connect to SSH Host" ক্লিক করুন**
4. **Connection string দিন:**
   ```
   barimanager@139.99.33.181
   ```
5. **Platform select করুন**: "Linux"
6. **Password দিন**: `*euSlN(C71+2LRz9`

#### ৪. Alternative Connection Methods

**Method 1: Using Host Alias**
```
barimanager
```

**Method 2: Direct IP**
```
barimanager@139.99.33.181
```

**Method 3: SFTP Connection**
```
barimanager-sftp
```

#### ৫. SSH Config File Structure

**Your SSH config file should look like this:**

```
# Existing configuration (if any)
HostName 103.98.76.150

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
```

#### ৬. Testing SSH Connection

**Command Prompt এ test করুন:**

```cmd
ssh barimanager@139.99.33.181
```

**Password দিন**: `*euSlN(C71+2LRz9`

#### ৭. Cursor Remote Development Workflow

**Connection establish করার পর:**

1. **Navigate to project directory:**
   ```bash
   cd /home/barimanager/public_html
   ```

2. **Load development aliases:**
   ```bash
   source ~/.bashrc
   ```

3. **Start development:**
   ```bash
   dev-start
   ```

4. **Watch logs:**
   ```bash
   dev-logs
   ```

#### ৮. Troubleshooting

**Common Issues:**

**Connection Failed:**
```cmd
# Test connection manually
ssh barimanager@139.99.33.181

# Check if SSH is enabled on server
# Contact hosting provider if needed
```

**Permission Denied:**
```bash
# Check file permissions on server
ls -la /home/barimanager/public_html

# Fix permissions
chmod -R 755 /home/barimanager/public_html
chmod -R 775 /home/barimanager/public_html/storage
```

**SSH Config Not Found:**
```cmd
# Check if config file exists
dir "C:\Users\samiu\.ssh\config"

# Create if not exists
mkdir "C:\Users\samiu\.ssh"
notepad "C:\Users\samiu\.ssh\config"
```

#### ৯. Quick Setup Commands

**PowerShell এ run করুন:**

```powershell
# Create .ssh directory
New-Item -ItemType Directory -Path "$env:USERPROFILE\.ssh" -Force

# Create config file
$config = @"
# SSH Configuration for Cursor Remote-SSH
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
"@

$config | Out-File -FilePath "$env:USERPROFILE\.ssh\config" -Encoding UTF8
```

#### ১০. Cursor Settings Configuration

**Cursor settings.json এ যোগ করুন:**

```json
{
  "remote.SSH.defaultExtensions": [
    "ms-vscode.vscode-json",
    "ms-vscode.vscode-typescript",
    "ms-vscode.vscode-php"
  ],
  "remote.SSH.configFile": "~/.ssh/config",
  "remote.SSH.showLoginTerminal": true
}
```

### ✅ SSH Configuration Checklist

- [ ] .ssh directory created
- [ ] SSH config file created
- [ ] Configuration added to config file
- [ ] SSH connection tested
- [ ] Cursor Remote-SSH extension installed
- [ ] Connection established in Cursor
- [ ] Development environment configured

### 🚀 Quick Setup Summary

1. **Create SSH config file:**
   ```cmd
   notepad "C:\Users\samiu\.ssh\config"
   ```

2. **Add configuration:**
   ```
   Host barimanager
       HostName 139.99.33.181
       User barimanager
       Port 22
       PreferredAuthentications password
   ```

3. **Test connection:**
   ```cmd
   ssh barimanager@139.99.33.181
   ```

4. **Open in Cursor:**
   - File → Open Folder
   - Connect to SSH Host
   - Enter: `barimanager@139.99.33.181`

🎉 **SSH Configuration Setup Complete!**

এই setup দিয়ে আপনি Cursor দিয়ে remote development করতে পারবেন। কোন সমস্যা হলে আমাকে জানান! 
