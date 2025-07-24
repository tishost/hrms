# Universal PDF Fix - Both Owner & Tenant

## 🚨 Issue Summary

1. **Owner Error:** "User is not a tenant" (tenant endpoint called)
2. **Tenant Error:** "User is not an owner" (owner endpoint called)

## ✅ Solution Applied

### Smart User Type Detection
`InvoicePdfScreen` এ proper user type detection add করেছি:

```dart
// Detect user type to call correct endpoint
String userType = await _detectUserType(token);
print('=== User Type Detection ===');
print('Detected User Type: $userType');

// Call appropriate endpoint based on user type
String apiEndpoint;
if (userType == 'owner') {
  apiEndpoint = '${getApiUrl()}/owner/invoices/${widget.invoiceId}/pdf-file';
  print('Using Owner Endpoint: $apiEndpoint');
} else {
  apiEndpoint = '${getApiUrl()}/tenant/invoices/${widget.invoiceId}/pdf-file';
  print('Using Tenant Endpoint: $apiEndpoint');
}
```

### User Type Detection Method
```dart
Future<String> _detectUserType(String token) async {
  // Call /api/user/profile to detect user type
  final response = await http.get(
    Uri.parse('${getApiUrl()}/user/profile'),
    headers: {'Authorization': 'Bearer $token'},
  );
  
  if (response.statusCode == 200) {
    final userData = json.decode(response.body);
    final user = userData['user'];
    
    if (user['owner'] != null) {
      print('✅ User detected as: Owner');
      return 'owner';
    } else if (user['tenant'] != null) {
      print('✅ User detected as: Tenant');
      return 'tenant';
    }
  }
  
  // Default to tenant if detection fails
  return 'tenant';
}
```

## 🔄 Complete Flow

### For Owner:
1. User clicks invoice
2. Call `/api/user/profile`
3. Detect user as "owner"
4. Call `/api/owner/invoices/{id}/pdf-file`
5. Display PDF ✅

### For Tenant:
1. User clicks invoice
2. Call `/api/user/profile`
3. Detect user as "tenant"
4. Call `/api/tenant/invoices/{id}/pdf-file`
5. Display PDF ✅

## 📱 Test Instructions

### Step 1: Build Mobile App
```bash
cd hrms_app
flutter build apk --debug
# or
flutter run
```

### Step 2: Test Owner
1. **Login as owner:** owner@hrms.com
2. **Click on invoice:** INV-2025-0002
3. **Check console logs:**
   ```
   === User Type Detection ===
   Detected User Type: owner
   Using Owner Endpoint: http://103.98.76.11/api/owner/invoices/2/pdf-file
   ```

### Step 3: Test Tenant
1. **Login as tenant:** sam@djddnd.com
2. **Click on invoice:** INV-2025-0002
3. **Check console logs:**
   ```
   === User Type Detection ===
   Detected User Type: tenant
   Using Tenant Endpoint: http://103.98.76.11/api/tenant/invoices/2/pdf-file
   ```

## 🔧 Backend Status

Backend এ সব ঠিক আছে:
- ✅ Owner PDF API: Working
- ✅ Tenant PDF API: Working
- ✅ User Profile API: Working
- ✅ Permission checks: Working

## 🎯 Expected Results

### Owner User:
- ✅ PDF loads successfully
- ✅ No "User is not a tenant" error
- ✅ Owner PDF content displayed

### Tenant User:
- ✅ PDF loads successfully
- ✅ No "User is not an owner" error
- ✅ Tenant PDF content displayed

## 🛠️ Debug Information

### Console Logs to Look For:
```
=== User Type Detection Debug ===
API URL: http://103.98.76.11/api/user/profile
Response Status: 200
Response Body: {"success":true,"user":{...}}
✅ User detected as: Owner/Tenant
=== User Type Detection ===
Detected User Type: owner/tenant
Using Owner/Tenant Endpoint: http://103.98.76.11/api/owner/tenant/invoices/2/pdf-file
```

### If Issues Persist:
1. **Check console logs** for user type detection
2. **Verify API response** from user profile
3. **Check endpoint** being called
4. **Share debug information**

## 🎯 Status

- **Root Cause:** ✅ Identified
- **Smart Detection:** ✅ Implemented
- **Backend:** ✅ Working
- **Mobile App:** 🔍 Needs Testing
- **Expected:** ✅ Both owner and tenant PDF should work

**এখন mobile app test করুন!** 🚀

**Both owner and tenant এর জন্য PDF কাজ করা উচিত।** 
