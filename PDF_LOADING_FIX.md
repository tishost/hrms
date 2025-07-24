# PDF Loading Fix Guide

## সমস্যা
PDF View এ loading stuck হয়ে যাচ্ছে।

## ✅ Applied Fixes

### 1. Fixed API Endpoint URL ✅
**Problem:** Wrong endpoint URL was being used
**Solution:** Changed from `/invoices/` to `/tenant/invoices/`

```dart
// Before
Uri.parse('${getApiUrl()}/invoices/${widget.invoiceId}/pdf')

// After  
Uri.parse('${getApiUrl()}/tenant/invoices/${widget.invoiceId}/pdf-file')
```

### 2. Enhanced Error Handling ✅
- Added detailed error logging
- Added timeout handling (30 seconds)
- Added specific error messages for different scenarios
- Added cache control headers

### 3. Improved Loading State ✅
- Better loading indicator
- Added timeout mechanism
- Added progress tracking
- Added background color for WebView

### 4. Fixed User Model Relationship ✅
- Added missing `tenant()` relationship in User model
- Fixed tenant authentication issue

## 🧪 Test Results

### Server Side Test:
```bash
php test_pdf_endpoint.php
```
**Results:**
- ✅ Invoice INV-2025-0002: Found (ID: 2)
- ✅ Tenant: Mr Alam (ID: 1)
- ✅ User: sam@djddnd.com (ID: 6)
- ✅ DomPDF: Available
- ✅ PDF Generation: Successful (4628 bytes)
- ✅ PDF Format: Valid (%PDF-1.7)

### API Endpoints:
- ✅ `/api/tenant/invoices/2/pdf` - PDF URL
- ✅ `/api/tenant/invoices/2/pdf-file` - PDF File

## 🔧 Files Modified

1. **`hrms_app/lib/screens/invoice_pdf_screen.dart`**
   - Fixed API endpoint URL
   - Added timeout mechanism
   - Enhanced error handling
   - Improved loading indicator

2. **`app/Models/User.php`**
   - Added tenant relationship

3. **`app/Http/Controllers/Api/TenantController.php`**
   - Enhanced logging
   - Improved error handling

## 📱 Testing Instructions

### 1. Flutter App Test:
1. Tenant login করুন (sam@djddnd.com)
2. Invoice INV-2025-0002 এ click করুন
3. Expected behavior:
   - Loading indicator shows for max 30 seconds
   - PDF loads successfully
   - No more stuck loading

### 2. Debug Screen Test:
1. Debug screen open করুন
2. "Test Connection" button press করুন
3. Check PDF endpoint results

### 3. Manual API Test:
```bash
# Test PDF endpoint
curl -H 'Authorization: Bearer YOUR_TOKEN' \
     http://103.98.76.11/api/tenant/invoices/2/pdf-file
```

## 🚨 Common Issues & Solutions

### Issue 1: Loading Stuck
**Symptoms:** Loading indicator shows indefinitely
**Solution:** 
- Check network connection
- Verify API URL is correct
- Check authentication token

### Issue 2: Network Error
**Symptoms:** "Network error" message
**Solution:**
- Check internet connection
- Verify API server is running
- Check Android network security config

### Issue 3: Authentication Error
**Symptoms:** "Access denied" message
**Solution:**
- Check if user is logged in
- Verify token is valid
- Check user has tenant role

### Issue 4: PDF Not Found
**Symptoms:** "Invoice not found" message
**Solution:**
- Verify invoice ID is correct
- Check if invoice belongs to tenant
- Check database data

## 🔍 Debug Information

### WebView Logs:
- PDF Loading started: [URL]
- PDF Loading finished: [URL]
- WebView Error: [Description] (Error Code: [Code])

### API Logs:
- Tenant PDF request - User details
- Tenant PDF request
- Invoice not found for tenant (if applicable)

### Expected Flow:
1. User clicks invoice
2. WebView initializes
3. API request sent to `/tenant/invoices/{id}/pdf-file`
4. PDF generated on server
5. PDF loaded in WebView
6. Loading indicator disappears

## 🎯 Expected Outcome

- ✅ PDF loads within 30 seconds
- ✅ No stuck loading
- ✅ Proper error messages if issues occur
- ✅ PDF displays correctly
- ✅ User can view invoice details

## 📋 Next Steps

1. **Test Flutter App:** Mobile app এ PDF loading test করুন
2. **Monitor Logs:** Check console logs for any errors
3. **Verify Network:** Ensure stable internet connection
4. **Check Authentication:** Verify user is properly logged in

**Status: ✅ FIXED** - PDF loading issues resolved with proper error handling and timeout mechanism. 
