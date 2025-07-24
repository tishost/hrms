# টেন্যান্ট রেজিস্ট্রেশন ফিক্স ডকুমেন্টেশন

## সমস্যা সমাধান

টেন্যান্ট এন্ট্রি ডেটা ডেটাবেসে সেভ করার সমস্যা সমাধান করা হয়েছে। নিম্নলিখিত সমস্যাগুলো ঠিক করা হয়েছে:

### ১. OTP ভেরিফিকেশন চেক
- রেজিস্ট্রেশন কমপ্লিট করার আগে OTP ভেরিফিকেশন চেক করা হয়
- ব্যবহৃত OTP আবার ব্যবহার করা যায় না
- OTP এর এক্সপায়ারি টাইম চেক করা হয়

### ২. ডেটাবেস ট্রানজেকশন হ্যান্ডলিং
- সব ডেটা সেভ করার সময় ট্রানজেকশন ব্যবহার করা হয়
- এরর হলে সব ডেটা রোলব্যাক হয়
- ডেটা ইন্টিগ্রিটি নিশ্চিত করা হয়

### ৩. এরর হ্যান্ডলিং
- বিস্তারিত লগিং যোগ করা হয়েছে
- ইউজার-ফ্রেন্ডলি এরর মেসেজ
- ডিবাগিং এর জন্য ডিটেইলড লগ

### ৪. Flutter অ্যাপ ইমপ্রুভমেন্ট
- Riverpod ইন্টিগ্রেশন
- Go Router ব্যবহার
- বেটার UI/UX
- প্রপার নেভিগেশন

## API এন্ডপয়েন্টস

### ১. OTP রিকোয়েস্ট
```
POST /api/tenant/request-otp
Content-Type: application/json

{
    "mobile": "01712345678"
}
```

**রেসপন্স:**
```json
{
    "success": true,
    "message": "OTP sent successfully",
    "otp": "123456",
    "tenant": {
        "name": "John Doe",
        "mobile": "01712345678",
        "email": "john@example.com",
        "property_name": "Sunrise Apartments",
        "unit_name": "A-101"
    }
}
```

### ২. OTP ভেরিফিকেশন
```
POST /api/tenant/verify-otp
Content-Type: application/json

{
    "mobile": "01712345678",
    "otp": "123456"
}
```

**রেসপন্স:**
```json
{
    "success": true,
    "message": "OTP verified successfully"
}
```

### ৩. রেজিস্ট্রেশন কমপ্লিট
```
POST /api/tenant/register
Content-Type: application/json

{
    "mobile": "01712345678",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**রেসপন্স:**
```json
{
    "success": true,
    "message": "Registration successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "phone": "01712345678",
        "email": "john@example.com",
        "tenant_id": 1,
        "owner_id": 1
    },
    "role": "tenant",
    "token": "1|abc123..."
}
```

## ডেটাবেস স্ট্রাকচার

### Users টেবিল
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(255) UNIQUE,
    password VARCHAR(255) NOT NULL,
    tenant_id BIGINT NULL,
    owner_id BIGINT NULL,
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (owner_id) REFERENCES owners(id) ON DELETE CASCADE
);
```

### TenantOtp টেবিল
```sql
CREATE TABLE tenant_otps (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    mobile VARCHAR(255) NOT NULL,
    otp VARCHAR(6) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    is_used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## টেস্টিং

### টেস্ট স্ক্রিপ্ট চালানো
```bash
cd /path/to/hrms
php test_tenant_registration.php
```

### ম্যানুয়াল টেস্টিং
1. **টেন্যান্ট তৈরি করুন**: অ্যাডমিন প্যানেলে টেন্যান্ট তৈরি করুন
2. **মোবাইল অ্যাপে রেজিস্ট্রেশন**: টেন্যান্ট রেজিস্ট্রেশন স্ক্রিনে যান
3. **OTP রিকোয়েস্ট**: মোবাইল নম্বর দিয়ে OTP রিকোয়েস্ট করুন
4. **OTP ভেরিফাই**: প্রাপ্ত OTP দিয়ে ভেরিফাই করুন
5. **পাসওয়ার্ড সেট**: পাসওয়ার্ড সেট করে রেজিস্ট্রেশন কমপ্লিট করুন

## ডিবাগিং

### লগ ফাইল চেক
```bash
tail -f storage/logs/laravel.log
```

### ডিবাগ মেসেজ
- OTP রিকোয়েস্ট: `Tenant OTP request received`
- OTP ভেরিফিকেশন: `Tenant OTP verification request`
- রেজিস্ট্রেশন: `Tenant registration request`
- সাকসেস: `Tenant registration completed successfully`

## নিরাপত্তা ফিচার

### ১. OTP সিকিউরিটি
- 6 ডিজিট OTP
- 10 মিনিট এক্সপায়ারি
- একবার ব্যবহার
- রেট লিমিটিং

### ২. পাসওয়ার্ড সিকিউরিটি
- মিনিমাম 6 ক্যারেক্টার
- পাসওয়ার্ড কনফার্মেশন
- হ্যাশিং

### ৩. ভ্যালিডেশন
- মোবাইল নম্বর ভ্যালিডেশন
- ইমেইল ভ্যালিডেশন
- রিকোয়ায়ার্ড ফিল্ড চেক

## এরর হ্যান্ডলিং

### সাধারণ এরর
- **404**: টেন্যান্ট পাওয়া যায়নি
- **400**: ইউজার ইতিমধ্যে রেজিস্টার্ড
- **400**: OTP ভেরিফাই হয়নি
- **500**: সার্ভার এরর

### এরর মেসেজ
```json
{
    "error": "Tenant not found. Please contact your owner."
}
```

## ফ্লাটার অ্যাপ ফিচার

### ১. UI/UX ইমপ্রুভমেন্ট
- মডার্ন ডিজাইন
- লোডিং স্টেট
- এরর মেসেজ
- সাকসেস মেসেজ

### ২. নেভিগেশন
- Go Router ব্যবহার
- প্রপার ব্যাক নেভিগেশন
- রাউট প্রটেকশন

### ৩. স্টেট ম্যানেজমেন্ট
- Riverpod প্রোভাইডার
- অথেনটিকেশন স্টেট
- ইউজার প্রোফাইল

## ডেপ্লয়মেন্ট চেকলিস্ট

### ১. ডেটাবেস
- [ ] মাইগ্রেশন রান করা হয়েছে
- [ ] টেবিল স্ট্রাকচার সঠিক
- [ ] ফরেন কী কনস্ট্রেইন্ট সেট

### ২. API
- [ ] রাউটস সেট করা হয়েছে
- [ ] কন্ট্রোলার আপডেট করা হয়েছে
- [ ] মডেল রিলেশনশিপ সঠিক

### ৩. ফ্লাটার অ্যাপ
- [ ] স্ক্রিন আপডেট করা হয়েছে
- [ ] API কনফিগ সঠিক
- [ ] নেভিগেশন সেট করা হয়েছে

### ৪. টেস্টিং
- [ ] টেস্ট স্ক্রিপ্ট রান করা হয়েছে
- [ ] ম্যানুয়াল টেস্টিং সম্পন্ন
- [ ] এরর হ্যান্ডলিং চেক করা হয়েছে

## সাপোর্ট

যদি কোন সমস্যা হয়:
1. লগ ফাইল চেক করুন
2. ডেটাবেস কানেকশন ভেরিফাই করুন
3. API এন্ডপয়েন্ট টেস্ট করুন
4. ফ্লাটার অ্যাপ ডিবাগ করুন

## আপডেট লগ

### v1.0.0 (2024-01-XX)
- টেন্যান্ট রেজিস্ট্রেশন ফিক্স
- OTP ভেরিফিকেশন যোগ
- ডেটাবেস ট্রানজেকশন হ্যান্ডলিং
- এরর হ্যান্ডলিং ইমপ্রুভমেন্ট
- ফ্লাটার অ্যাপ UI/UX আপডেট 
