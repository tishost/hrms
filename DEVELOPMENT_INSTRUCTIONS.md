# 🚀 HRMS Development Instructions

## 📋 **গুরুত্বপূর্ণ Instructions মনে রাখার জন্য**

### **1. Database & Migration Rules:**
```
✅ "otps name amader akta table asay jaita otp ar jonno use hobay multiperpos kaz ar jonno . oi table e otp ar jonno use korbay . akoi kaz ar jonno multiple table banaba nah remember"
✅ "r kono migration or new table bananor agay amar kasay confirmation nebo .. remember remember"
```

**Rules:**
- Existing tables use করবো, নতুন table নয়
- OTP এর জন্য `otps` table use করবো
- নতুন migration/table এর আগে confirmation নিবো

### **2. Notification System Rules:**
```
✅ "amra notifacation helper ar moddamoy sob dhoroner notifacation send korbo , like , sms, otp, email app notifacation , r sms service sms helper use korbo .. ait remember koro multiple funcation or controller bananba nah akoi kaz ar jonno"
```

**Rules:**
- সব notification NotificationHelper দিয়ে পাঠাবো
- SMS এর জন্য SmsHelper ব্যবহার করবো
- Multiple function/controller তৈরি করবো না

### **3. Code Pattern Rules:**
```
✅ "akoi kaz ar jonno multiple table banaba nah"
✅ "multiple funcation or controller bananba nah akoi kaz ar jonno"
✅ "existing table use korbo"
✅ "template-based notification ব্যবহার করবো"
```

**Rules:**
- Existing models/functions reuse করবো
- Duplicate functions avoid করবো
- Template-based notifications ব্যবহার করবো

### **4. UI/UX Rules:**
```
✅ "akbar otp send korlay send otp button hide hoya jabay"
✅ "table header er text gular color white hoyai text show korsay nah"
✅ "admin dashboard e subcription list a akta search and filter section add koro"
✅ "admin dashboard owner list table a owner search r filter add kor"
```

**Rules:**
- Button hide/show functionality add করবো
- Table styling fix করবো
- Search and filter functionality add করবো

### **5. Password Reset Rules:**
```
✅ "password rest a mobile or email address 2 ta optiion thkbay"
✅ "mobile no holay otp jabay otp delay password resset page asbay"
✅ "password lenth min six not 8"
✅ "ami akta OTP deya reset kortay chasse database thakay otp ta dou"
```

**Rules:**
- Mobile + Email options provide করবো
- OTP database থেকে retrieve করবো
- Password length 6 characters minimum

### **6. Production Rules:**
```
✅ "TODO: Integrate with SMS service (Twilio, etc.)"
✅ "Remove this in production" (for OTP in response)
```

**Rules:**
- OTP response production এ remove করবো
- SMS service integrate করবো

## 🎯 **Key Helpers & Functions to Remember:**

### **NotificationHelper:**
```php
use App\Helpers\NotificationHelper;

// SMS পাঠানো
NotificationHelper::sendSms($phone, $message, $variables);

// OTP SMS পাঠানো
NotificationHelper::sendOtpSms($phone, $otp);

// Email পাঠানো
NotificationHelper::sendEmail($email, $subject, $content, $variables);

// Template ব্যবহার করে
NotificationHelper::sendTemplate('sms', $phone, 'template_name', $variables);
NotificationHelper::sendTemplate('email', $email, 'template_name', $variables);
```

### **SmsHelper:**
```php
use App\Helpers\SmsHelper;

// Owner এর জন্য SMS
SmsHelper::sendOwnerSms($ownerId, $phone, $message, $template, $variables);

// Tenant এর জন্য SMS
SmsHelper::sendTenantSms($tenantId, $phone, $message, $template, $variables);

// System SMS
SmsHelper::sendSystemSms($phone, $message, $template, $variables);
```

### **Otp Model:**
```php
use App\Models\Otp;

// OTP generate করা
$otpRecord = Otp::generateOtp($phone, $type, $length);

// OTP verify করা
$isValid = Otp::verifyOtp($phone, $otp, $type);
```

## ✅ **Do's:**
1. সব Notification NotificationHelper দিয়ে পাঠাবো
2. SMS এর জন্য SmsHelper ব্যবহার করবো
3. OTP এর জন্য Otp Model ব্যবহার করবো
4. Template ব্যবহার করবো
5. Error handling করবো
6. Existing functions reuse করবো

## ❌ **Don'ts:**
1. নতুন Controller তৈরি করবো না
2. Duplicate function তৈরি করবো না
3. Hard-coded message লিখবো না
4. Direct SMS service call করবো না
5. নতুন table তৈরি করবো না (existing use করবো)

## 🔄 **Pattern মনে রাখার জন্য:**

### **SMS Pattern:**
```php
// ✅ সঠিক পদ্ধতি
$result = NotificationHelper::sendOtpSms($phone, $otp);

// ❌ ভুল পদ্ধতি
$smsService = new SmsService();
$smsService->sendSms($phone, $message);
```

### **Email Pattern:**
```php
// ✅ সঠিক পদ্ধতি
$result = NotificationHelper::sendPasswordResetEmail($user, $token);

// ❌ ভুল পদ্ধতি
Mail::to($email)->send(new ResetPassword($token));
```

### **OTP Pattern:**
```php
// ✅ সঠিক পদ্ধতি
$otpRecord = Otp::generateOtp($phone, 'password_reset', 6);
$isValid = Otp::verifyOtp($phone, $otp, 'password_reset');

// ❌ ভুল পদ্ধতি
$otp = rand(100000, 999999);
DB::table('otps')->insert([...]);
```

---
**এই Instructions সব সময় follow করবো!** 🚀 