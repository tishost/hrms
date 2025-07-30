# 🚀 Cursor SFTP Plugin Setup Guide
## Domain: barimanager.com | IP: 139.99.33.181

### ✅ Cursor এ SFTP Plugin Installation

#### ১. Cursor Extensions Install

**Method 1: Built-in Remote Extension**

1. **Cursor Open করুন**
2. **Extensions ট্যাবে যান** (Ctrl+Shift+X)
3. **Search করুন**: "Remote - SSH"
4. **Install করুন**: "Remote - SSH" extension
5. **Restart Cursor**

**Method 2: SFTP Extension**

1. **Extensions ট্যাবে যান**
2. **Search করুন**: "SFTP"
3. **Install করুন**: "SFTP" extension
4. **Restart Cursor**

#### ২. Remote-SSH Extension Setup

**Extension Install করার পর:**

1. **File → Open Folder**
2. **"Connect to SSH Host" ক্লিক করুন**
3. **Connection string দিন:**
   ```
   barimanager@139.99.33.181
   ```
4. **Platform select করুন**: "Linux"
5. **Password দিন**: `*euSlN(C71+2LRz9`

#### ৩. SFTP Extension Configuration

**SFTP Extension Install করার পর:**

1. **Command Palette খুলুন** (Ctrl+Shift+P)
2. **Type করুন**: "SFTP: Config"
3. **Create new configuration file**
4. **Configuration দিন:**

```json
{
  "name": "HRMS Development",
  "host": "139.99.33.181",
  "username": "barimanager",
  "password": "*euSlN(C71+2LRz9",
  "port": 22,
  "remotePath": "/home/barimanager/public_html",
  "uploadOnSave": true,
  "syncMode": "update",
  "ignore": [
    ".vscode",
    ".git",
    ".DS_Store",
    "node_modules",
    "vendor",
    "storage/logs",
    "storage/framework/cache"
  ]
}
```

#### ৪. Alternative: FTP Extension

**যদি SFTP কাজ না করে:**

1. **Extensions ট্যাবে যান**
2. **Search করুন**: "FTP"
3. **Install করুন**: "FTP" extension
4. **Configuration দিন:**

```json
{
  "name": "HRMS FTP",
  "host": "139.99.33.181",
  "username": "barimanager",
  "password": "*euSlN(C71+2LRz9",
  "port": 21,
  "remotePath": "/public_html",
  "uploadOnSave": true
}
```

#### ৫. Cursor Settings Configuration

**Cursor settings.json এ যোগ করুন:**

```json
{
  "remote.SSH.defaultExtensions": [
    "ms-vscode.vscode-json",
    "ms-vscode.vscode-typescript",
    "ms-vscode.vscode-php"
  ],
  "remote.SSH.configFile": "~/.ssh/config",
  "sftp.uploadOnSave": true,
  "sftp.syncMode": "update",
  "sftp.ignore": [
    ".vscode",
    ".git",
    ".DS_Store",
    "node_modules",
    "vendor"
  ]
}
```

#### ৬. SSH Config File Setup

**Local machine এ SSH config তৈরি করুন:**

```bash
# ~/.ssh/config ফাইল তৈরি করুন
mkdir -p ~/.ssh
cat > ~/.ssh/config << 'EOF'
Host barimanager
    HostName 139.99.33.181
    User barimanager
    Port 22
    PreferredAuthentications password
EOF

chmod 600 ~/.ssh/config
```

#### ৭. Connection Test

**Connection test করার জন্য:**

1. **Terminal খুলুন**
2. **Test SSH connection:**
   ```bash
   ssh barimanager@139.99.33.181
   ```
3. **Password দিন**: `*euSlN(C71+2LRz9`

#### ৮. Cursor Remote Development Workflow

**Connection establish করার পর:**

1. **File → Open Folder**
2. **"Connect to SSH Host"**
3. **Enter**: `barimanager@139.99.33.181`
4. **Select platform**: Linux
5. **Enter password**: `*euSlN(C71+2LRz9`
6. **Navigate to**: `/home/barimanager/public_html`

#### ৯. Development Commands in Cursor

**Cursor এর integrated terminal এ:**

```bash
# Go to project directory
cd /home/barimanager/public_html

# Load development aliases
source ~/.bashrc

# Start development
dev-start

# Watch logs
dev-logs

# Clear caches
dev-clear

# Check status
dev-status
```

#### ১০. Troubleshooting

**Common Issues:**

**Connection Failed:**
```bash
# Test connection manually
ssh barimanager@139.99.33.181

# Check if SSH is enabled on server
# Contact hosting provider if needed
```

**Extension Not Found:**
1. **Cursor update করুন**
2. **Extensions manually search করুন**
3. **Alternative extension try করুন**

**Permission Denied:**
```bash
# Check file permissions on server
ls -la /home/barimanager/public_html

# Fix permissions
chmod -R 755 /home/barimanager/public_html
chmod -R 775 /home/barimanager/public_html/storage
```

#### ১১. Alternative: FileZilla + Cursor

**যদি Cursor plugins কাজ না করে:**

1. **FileZilla download করুন**
2. **SFTP connection setup করুন:**
   - Host: `139.99.33.181`
   - Username: `barimanager`
   - Password: `*euSlN(C71+2LRz9`
   - Port: `22`
3. **Navigate to**: `/home/barimanager/public_html`
4. **Edit files locally in Cursor**
5. **Upload via FileZilla**

#### ১২. cPanel Alternative

**যদি সব plugin fail হয়:**

1. **Go to**: https://barimanager.com/cpanel
2. **Login**: barimanager / *euSlN(C71+2LRz9
3. **File Manager → public_html**
4. **Edit files directly**
5. **Use cPanel Terminal for commands**

### ✅ Plugin Installation Checklist

- [ ] Cursor updated to latest version
- [ ] Remote-SSH extension installed
- [ ] SFTP extension installed (alternative)
- [ ] SSH connection tested
- [ ] File permissions set correctly
- [ ] Development environment configured
- [ ] Backup system set up

### 🚀 Quick Setup Commands

**After plugin installation:**

```bash
# In Cursor terminal
cd /home/barimanager/public_html

# Setup development environment
/tmp/cpanel_dev_workflow.sh

# Load development commands
source ~/.bashrc

# Start development
dev-start

# Watch logs
dev-logs
```

### 📋 Plugin Installation Steps Summary

1. **Open Cursor**
2. **Go to Extensions** (Ctrl+Shift+X)
3. **Search "Remote - SSH"**
4. **Install extension**
5. **Restart Cursor**
6. **File → Open Folder**
7. **"Connect to SSH Host"**
8. **Enter**: `barimanager@139.99.33.181`
9. **Enter password**: `*euSlN(C71+2LRz9`
10. **Navigate to project directory**

🎉 **Cursor SFTP Plugin Setup Complete!**

এই setup দিয়ে আপনি Cursor দিয়ে remote development করতে পারবেন। কোন সমস্যা হলে আমাকে জানান! 
