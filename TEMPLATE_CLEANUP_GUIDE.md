# 🗑️ Template Cleanup Guide

## Overview
This guide helps you safely remove old template keys from the `system_settings` table after migrating to the new `email_templates` and `sms_templates` tables.

## ⚠️ Important Notes
- **Always backup first** before removing any data
- **Test the new template system** before removing old templates
- **Verify migration is complete** before cleanup

## 📋 Step-by-Step Process

### 1. Verify New Template Tables
First, ensure the new template tables are populated:

```bash
# Check if migrations are run
php artisan migrate:status

# Check template counts
php artisan tinker --execute="
echo 'Email Templates: ' . \App\Models\EmailTemplate::count() . PHP_EOL;
echo 'SMS Templates: ' . \App\Models\SmsTemplate::count() . PHP_EOL;
"
```

### 2. Create Backup
**ALWAYS backup before removing data:**

```bash
# Create JSON backup
php artisan templates:backup-old --format=json

# Or create SQL backup
php artisan templates:backup-old --format=sql
```

Backup files will be saved to: `storage/app/backups/`

### 3. Test New Template System
Verify that notifications work with new templates:

```bash
# Test email template
php artisan tinker --execute="
\$template = \App\Models\EmailTemplate::where('key', 'welcome_email')->first();
if (\$template) {
    echo 'Email template found: ' . \$template->name . PHP_EOL;
} else {
    echo 'Email template NOT found!' . PHP_EOL;
}
"

# Test SMS template
php artisan tinker --execute="
\$template = \App\Models\SmsTemplate::where('key', 'welcome_sms')->first();
if (\$template) {
    echo 'SMS template found: ' . \$template->name . PHP_EOL;
} else {
    echo 'SMS template NOT found!' . PHP_EOL;
}
"
```

### 4. Dry Run (Preview)
See what will be removed without actually removing:

```bash
php artisan templates:remove-old --dry-run
```

This will show you:
- Which keys will be removed
- How many records will be affected
- Current status of new template tables

### 5. Remove Old Templates
If everything looks good, remove the old templates:

```bash
php artisan templates:remove-old
```

## 📊 Template Keys That Will Be Removed

### Email Templates (13)
- `welcome_email`
- `account_setup_guide_email`
- `features_overview_email`
- `subscription_info_email`
- `account_verification_email`
- `security_alert_email`
- `subscription_expiry_reminder_email`
- `payment_success_email`
- `invoice_reminder_email`
- `payment_confirmation_email`
- `invoice_notification_email`
- `subscription_reminder_email`
- `subscription_activation_email`

### SMS Templates (8)
- `welcome_sms`
- `owner_welcome_sms`
- `payment_confirmation_sms`
- `due_date_reminder_sms`
- `subscription_activation_sms`
- `invoice_reminder_sms`
- `password_reset_otp_sms`
- `system_otp_sms`

### Additional Keys
- Old template format keys (with `template_` prefix)
- Language-specific keys (with `_bangla` and `_english` suffixes)

## 🔄 Rollback Process

If you need to restore the old templates:

### From JSON Backup
```bash
php artisan tinker --execute="
\$backup = json_decode(file_get_contents('storage/app/backups/old_templates_backup_YYYY-MM-DD_HH-MM-SS.json'), true);
foreach (\$backup['templates'] as \$template) {
    \App\Models\SystemSetting::create([
        'key' => \$template['key'],
        'value' => \$template['value'],
        'created_at' => \$template['created_at'],
        'updated_at' => \$template['updated_at']
    ]);
}
echo 'Templates restored from backup' . PHP_EOL;
"
```

### From SQL Backup
```bash
# Import SQL backup
mysql -u username -p database_name < storage/app/backups/old_templates_backup_YYYY-MM-DD_HH-MM-SS.sql
```

## ✅ Verification Checklist

After cleanup, verify:

- [ ] New template tables have data
- [ ] Email notifications work
- [ ] SMS notifications work
- [ ] Admin panel shows templates
- [ ] No errors in logs
- [ ] Backup file exists and is valid

## 🚨 Troubleshooting

### If New Templates Are Empty
```bash
# Run seeders to populate new tables
php artisan db:seed --class=EmailTemplateSeeder
php artisan db:seed --class=SmsTemplateSeeder
```

### If Notifications Stop Working
1. Check if templates exist in new tables
2. Verify template keys match exactly
3. Check if templates are active (`is_active = 1`)
4. Restore from backup if needed

### If You Need to Restore
1. Stop the application
2. Restore from backup
3. Fix any issues
4. Re-run the migration process

## 📞 Support

If you encounter issues:
1. Check the backup files
2. Review the logs
3. Verify database connections
4. Test with a small subset first

## 🎯 Benefits After Cleanup

- **Cleaner Database**: Removed unused template keys
- **Better Performance**: Smaller system_settings table
- **Organized Templates**: Templates in dedicated tables
- **Enhanced Features**: Character limits, categories, priorities
- **Better Management**: Admin panel for template editing

---

**Remember: Always backup before cleanup!** 💾
