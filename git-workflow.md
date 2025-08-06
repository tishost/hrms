# Git-Based Local-Remote Development Workflow

## 🚀 Setup

### 1. Local Repository Clone
```bash
git clone ssh://barimanager@139.99.33.181/home/barimanager/hrms.git D:/BariManager/hrms_backend
cd D:/BariManager/hrms_backend
composer install
```

### 2. Development Branch
```bash
git checkout -b development
git push -u origin development
```

## 🔄 Daily Workflow

### Morning (Local Development)
```bash
# 1. Pull latest changes
git pull origin main

# 2. Start local development
php artisan serve --port=8000

# 3. Make changes and test locally
```

### Evening (Deploy to Remote)
```bash
# 1. Commit changes
git add .
git commit -m "Feature: Add new functionality"

# 2. Push to remote
git push origin development

# 3. SSH to server and pull
ssh barimanager@139.99.33.181
cd /home/barimanager/hrms
git pull origin development
php artisan migrate
php artisan config:clear
```

## 🛠️ Quick Commands

### Local Development
```bash
# Start local server
php artisan serve --port=8000

# Run migrations locally
php artisan migrate

# Clear cache
php artisan config:clear
```

### Remote Deployment
```bash
# Deploy to remote
git push origin development
ssh barimanager@139.99.33.181 "cd /home/barimanager/hrms && git pull origin development && php artisan migrate"
```

## 📱 Flutter Integration

### API Configuration
```dart
// lib/config/api_config.dart
class ApiConfig {
  static const String localApi = 'http://localhost:8000/api';
  static const String remoteApi = 'http://139.99.33.181/api';
  
  static String get baseUrl {
    // Use local for development, remote for production
    return kDebugMode ? localApi : remoteApi;
  }
} 