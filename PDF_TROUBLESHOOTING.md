# PDF Loading Troubleshooting Guide

## সমস্যা
`net::ERR_CLEARTEXT_NOT_PERMITTED` error আসছে PDF load করার সময়।

## কারণ
Android এর network security policy এর কারণে HTTP connection block হচ্ছে।

## সমাধান

### 1. Network Security Configuration ✅
- `android/app/src/main/res/xml/network_security_config.xml` file তৈরি করা হয়েছে
- Local development এর জন্য cleartext traffic allow করা হয়েছে

### 2. AndroidManifest.xml Update ✅
- `android:networkSecurityConfig="@xml/network_security_config"` যোগ করা হয়েছে
- `android:usesCleartextTraffic="true"` যোগ করা হয়েছে

### 3. API Configuration ✅
- `lib/utils/api_config.dart` এ local development URL set করা হয়েছে
- Android emulator এর জন্য `10.0.2.2` ব্যবহার করা হয়েছে

### 4. Error Handling ✅
- PDF viewer screen এ better error handling যোগ করা হয়েছে
- Specific error messages show করা হচ্ছে

### 5. Debug Screen ✅
- API connection test করার জন্য debug screen তৈরি করা হয়েছে
- Tenant dashboard এ debug button যোগ করা হয়েছে

## Testing Steps

### Step 1: Check WAMP Server
1. WAMP server running আছে কিনা check করুন
2. `http://localhost/hrms/public/api` accessible কিনা test করুন

### Step 2: Check API URL
1. Flutter app এ debug screen open করুন
2. "Test Connection" button press করুন
3. API URL এবং response check করুন

### Step 3: Check Authentication
1. Tenant login করুন
2. Authentication token আছে কিনা check করুন
3. API calls successful কিনা verify করুন

### Step 4: Test PDF Endpoint
1. Debug screen এ PDF endpoint test করুন
2. PDF response status এবং content-type check করুন

## Common Issues & Solutions

### Issue 1: ERR_CLEARTEXT_NOT_PERMITTED
**Solution:**
- Network security configuration check করুন
- API URL HTTPS ব্যবহার করছে কিনা check করুন
- Local development এর জন্য HTTP allow করা আছে কিনা verify করুন

### Issue 2: ERR_CONNECTION_REFUSED
**Solution:**
- WAMP server running আছে কিনা check করুন
- Port 80 accessible কিনা verify করুন
- Firewall settings check করুন

### Issue 3: ERR_NAME_NOT_RESOLVED
**Solution:**
- API URL correct কিনা check করুন
- DNS resolution working কিনা verify করুন
- Emulator এর জন্য `10.0.2.2` ব্যবহার করুন

### Issue 4: Authentication Failed
**Solution:**
- Tenant login করুন
- Token valid কিনা check করুন
- API headers correct কিনা verify করুন

## Files Modified

1. `android/app/src/main/res/xml/network_security_config.xml` (new)
2. `android/app/src/main/AndroidManifest.xml`
3. `lib/utils/api_config.dart`
4. `lib/screens/invoice_pdf_screen.dart`
5. `lib/screens/debug_screen.dart` (new)
6. `lib/screens/tenant_dashboard_screen.dart`

## Next Steps

1. **Rebuild App**: `flutter clean && flutter build apk`
2. **Test Debug Screen**: API connection test করুন
3. **Check Logs**: Console logs check করুন
4. **Verify PDF**: PDF viewer test করুন

## Expected Result

PDF viewer এ এখন error না আসা উচিত এবং PDF properly load হওয়া উচিত। যদি এখনও সমস্যা থাকে, debug screen এর results share করুন। 
