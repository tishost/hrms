# Owner PDF Fix Guide

## 🚨 Current Issue

Owner হিসেবে login করে PDF view করার সময় error আসছে: `"User is not a tenant"`

## 🔍 Root Cause

Error message দেখাচ্ছে যে mobile app এ tenant API endpoint call হচ্ছে owner এর জন্য। এর মানে user type detection ঠিকমত কাজ করছে না।

## ✅ Backend Status

Backend এ সব ঠিক আছে:

### Test Results:
```bash
php test_owner_user_detection.php
```

**Results:**
- ✅ User found: owner@hrms.com
- ✅ Owner relationship found
- ✅ AuthController getUserProfile: Working
- ✅ OwnerController profile: Working
- ✅ Owner data in response: Present
- ❌ No tenant data in response: Correct

## 🔧 Mobile App Fix

### 1. Debug Information Added

Universal PDF Screen এ debug information add করেছি:

```dart
print('=== User Type Detection Debug ===');
print('API URL: ${getApiUrl()}/user/profile');
print('Token: ${token.substring(0, 20)}...');
print('Response Status: ${response.statusCode}');
print('Response Body: ${response.body}');
print('Parsed User Data: $userData');
```

### 2. Test Steps

**Step 1: Mobile App এ Test করুন**
1. Owner হিসেবে login করুন
2. Invoice এ click করুন
3. Console logs দেখুন

**Step 2: Debug Logs Check করুন**
Mobile app এর console এ এই logs দেখুন:
```
=== User Type Detection Debug ===
API URL: http://103.98.76.11/api/user/profile
Token: YOUR_TOKEN_HERE...
Response Status: 200
Response Body: {"success":true,"user":{...}}
Parsed User Data: {success: true, user: {...}}
✅ User detected as: Owner
Owner Data: {id: 2, first_name: samiul, ...}
=== Universal PDF Debug ===
User Type: owner
Invoice ID: 2
API Endpoint: http://103.98.76.11/api/owner/invoices/2/pdf-file
```

### 3. Expected Flow

**Correct Flow:**
1. User clicks invoice
2. UniversalPdfScreen loads
3. Calls `/api/user/profile`
4. Detects user as "owner"
5. Calls `/api/owner/invoices/{id}/pdf-file`
6. PDF loads successfully

**Current Issue Flow:**
1. User clicks invoice
2. UniversalPdfScreen loads
3. Calls `/api/user/profile`
4. ❌ Detection fails or wrong endpoint
5. ❌ Calls `/api/tenant/invoices/{id}/pdf-file`
6. ❌ Gets "User is not a tenant" error

## 🛠️ Troubleshooting

### 1. Check Token
Mobile app এ token ঠিক আছে কিনা check করুন:
```dart
print('Token: ${token.substring(0, 20)}...');
```

### 2. Check API Response
User profile API response check করুন:
```dart
print('Response Body: ${response.body}');
```

### 3. Check User Type Detection
User type detection result check করুন:
```dart
print('✅ User detected as: Owner');
print('Owner Data: ${user['owner']}');
```

### 4. Check API Endpoint
Final API endpoint check করুন:
```dart
print('API Endpoint: $apiEndpoint');
```

## 🎯 Quick Fix Options

### Option 1: Manual User Type
```dart
UniversalPdfScreen(
  invoiceId: invoice.id,
  invoiceNumber: invoice.invoiceNumber,
  userType: 'owner', // Force owner type
)
```

### Option 2: Check API URL
Mobile app এ API URL ঠিক আছে কিনা check করুন:
```dart
// In api_config.dart
String getApiUrl() {
  return 'http://103.98.76.11/api';
}
```

### Option 3: Test with Postman
Postman এ test করুন:
```
GET http://103.98.76.11/api/user/profile
Headers:
  Authorization: Bearer YOUR_TOKEN
  Accept: application/json
```

## 📱 Mobile App Test Instructions

1. **Build and Run** mobile app
2. **Login as owner** (owner@hrms.com)
3. **Click on invoice** INV-2025-0002
4. **Check console logs** for debug information
5. **Share logs** if error persists

## 🔍 Debug Checklist

- [ ] Token valid and not expired
- [ ] API URL correct (http://103.98.76.11/api)
- [ ] User profile API returns owner data
- [ ] User type detection returns "owner"
- [ ] Owner PDF endpoint called
- [ ] Owner PDF endpoint returns PDF

## 📞 Next Steps

1. **Mobile app test করুন** এবং console logs share করুন
2. **Debug information দেখুন** user type detection এ
3. **API endpoint check করুন** final call এ
4. **Error persists হলে** logs share করুন

**Status:** Backend ✅ Working, Mobile App 🔍 Needs Testing 
