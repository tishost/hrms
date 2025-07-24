# Mobile App Error Fix Guide

## সমস্যা
Mobile app এ এখনও "Invoice not found" error আসছে।

## Step-by-Step Solution

### Step 1: Check Database Data
```bash
php test_api_endpoints.php
```
এই script run করে tenant এবং invoice data check করুন।

### Step 2: Check Flutter Debug Screen
1. Flutter app এ tenant login করুন
2. Debug screen open করুন (dashboard এ bug icon)
3. "Test Connection" button press করুন
4. Results check করুন

### Step 3: Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```
API requests এবং errors check করুন।

### Step 4: Manual API Testing
Authentication token নিয়ে API endpoints test করুন:

```bash
# Get token from Flutter app debug screen
curl -H 'Authorization: Bearer YOUR_TOKEN' http://103.98.76.11/api/tenant/test
curl -H 'Authorization: Bearer YOUR_TOKEN' http://103.98.76.11/api/tenant/dashboard
curl -H 'Authorization: Bearer YOUR_TOKEN' http://103.98.76.11/api/tenant/invoices
```

## Common Issues & Solutions

### Issue 1: No Tenant Found
**Symptoms:** "User is not a tenant" error
**Solution:**
- Check if user has tenant role
- Verify tenant_id in users table
- Check tenant record exists

### Issue 2: No Invoices Found
**Symptoms:** "Invoice not found" error
**Solution:**
- Check if tenant has invoices
- Verify invoice belongs to correct tenant
- Check invoice status

### Issue 3: Authentication Failed
**Symptoms:** 401 or 403 errors
**Solution:**
- Check token is valid
- Verify token format
- Check token expiration

### Issue 4: API Endpoint Error
**Symptoms:** 404 or 500 errors
**Solution:**
- Check API URL is correct
- Verify routes are registered
- Check server is running

## Debug Information Added

### 1. Enhanced Logging ✅
- Tenant PDF requests এ detailed logging
- Invoice queries এ debugging information
- Request headers এবং URL logging

### 2. Test Endpoint ✅
- `/api/tenant/test` endpoint যোগ করা হয়েছে
- Tenant authentication check
- Invoice data validation

### 3. Debug Screen ✅
- API connection test
- Tenant test endpoint
- Invoice list validation
- PDF endpoint test

### 4. Error Handling ✅
- Better error messages
- Specific error types
- Detailed error information

## Files Modified

1. `app/Http/Controllers/Api/TenantController.php` - Enhanced logging + test endpoint
2. `app/Http/Controllers/Api/TenantDashboardController.php` - Invoice logging
3. `routes/api.php` - Test endpoint route
4. `lib/screens/debug_screen.dart` - Enhanced debugging
5. `test_api_endpoints.php` - Database test script

## Expected Results

### Debug Screen Results Should Show:
- ✅ API URL: http://103.98.76.11/api
- ✅ Test Status: 200
- ✅ Test Success: true
- ✅ Tenant ID: [actual tenant ID]
- ✅ Tenant Name: [actual tenant name]
- ✅ Invoices Count: [number of invoices]

### If Errors Occur:
1. **Test Status: 403** - Authentication issue
2. **Test Status: 404** - Endpoint not found
3. **Test Status: 500** - Server error
4. **No Invoices Found** - Database issue

## Next Steps

1. **Run Database Test:** `php test_api_endpoints.php`
2. **Check Debug Screen:** Flutter app এ test করুন
3. **Check Logs:** Laravel logs monitor করুন
4. **Share Results:** Debug screen results share করুন

## Manual Testing Commands

```bash
# Test database
php test_api_endpoints.php

# Check logs
tail -f storage/logs/laravel.log

# Test API endpoints (replace YOUR_TOKEN)
curl -H 'Authorization: Bearer YOUR_TOKEN' http://103.98.76.11/api/tenant/test
curl -H 'Authorization: Bearer YOUR_TOKEN' http://103.98.76.11/api/tenant/invoices
```

এই steps follow করে exact problem identify করা যাবে এবং fix করা যাবে। 
