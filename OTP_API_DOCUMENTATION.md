# OTP Verification API Documentation

## Overview
This API provides OTP (One-Time Password) verification functionality for the HRMS system. It supports sending, verifying, and resending OTP codes via SMS.

## Base Information
- **Base URL:** `https://barimanager.com/api`
- **API Version:** v1
- **Authentication:** Bearer Token (for protected endpoints)
- **Content-Type:** `application/json`

## Available Endpoints

### 1. Get OTP Settings
**Endpoint:** `GET /otp-settings`  
**Description:** Retrieve current OTP configuration settings  
**Authentication:** Not required

#### Response Format
```json
{
  "success": true,
  "data": {
    "enabled": true,
    "length": 6,
    "expiry_minutes": 10,
    "max_attempts": 3,
    "cooldown_seconds": 30,
    "require_registration": true,
    "require_password_reset": true
  }
}
```

---

### 2. Send OTP
**Endpoint:** `POST /send-otp`  
**Description:** Send OTP code to a phone number  
**Authentication:** Not required

#### Request Body
```json
{
  "phone": "01712345678",
  "type": "profile_update"
}
```

#### Request Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `phone` | string | Yes | Phone number (10-20 characters) |
| `type` | string | Yes | OTP type: `registration`, `login`, `reset`, `profile_update` |

#### Response Format
```json
{
  "success": true,
  "message": "OTP sent successfully",
  "data": {
    "phone": "01712345678",
    "type": "profile_update",
    "expiry_minutes": 10,
    "otp": "123456",
    "sent_at": "2025-09-26T15:30:00Z"
  }
}
```

#### Error Responses
```json
{
  "success": false,
  "message": "OTP verification system is currently disabled",
  "error_code": "OTP_DISABLED"
}
```

---

### 3. Verify OTP
**Endpoint:** `POST /verify-otp`  
**Description:** Verify OTP code  
**Authentication:** Not required

#### Request Body
```json
{
  "phone": "01712345678",
  "otp": "123456",
  "type": "profile_update",
  "user_id": 75
}
```

#### Request Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `phone` | string | Yes | Phone number |
| `otp` | string | Yes | 6-digit OTP code |
| `type` | string | Yes | OTP type |
| `user_id` | integer | No | User ID (optional) |

#### Response Format
```json
{
  "success": true,
  "message": "🎉 Mobile number verified successfully!",
  "data": {
    "phone": "01712345678",
    "verification_status": "verified",
    "user_details": {
      "type": "owner",
      "name": "John Doe",
      "phone": "01712345678",
      "phone_verified": true
    },
    "verified_at": "2025-09-26T15:30:00Z"
  }
}
```

#### Error Responses
```json
{
  "success": false,
  "message": "Invalid OTP. Please try again.",
  "error_code": "INVALID_OTP"
}
```

---

### 4. Resend OTP
**Endpoint:** `POST /resend-otp`  
**Description:** Resend OTP code  
**Authentication:** Not required

#### Request Body
```json
{
  "phone": "01712345678",
  "type": "profile_update"
}
```

#### Response Format
```json
{
  "success": true,
  "message": "OTP resent successfully",
  "data": {
    "phone": "01712345678",
    "type": "profile_update",
    "expiry_minutes": 10,
    "otp": "789012",
    "sent_at": "2025-09-26T15:30:00Z"
  }
}
```

---

## OTP Types

| Type | Description | Use Case |
|------|-------------|----------|
| `registration` | User registration | New user signup |
| `login` | User login | Two-factor authentication |
| `reset` | Password reset | Password recovery |
| `profile_update` | Profile update | Phone number verification |

## Error Codes

| Code | Description |
|------|-------------|
| `OTP_DISABLED` | OTP system is disabled |
| `INVALID_OTP` | OTP code is invalid |
| `OTP_EXPIRED` | OTP has expired |
| `MAX_ATTEMPTS` | Maximum attempts exceeded |
| `COOLDOWN_ACTIVE` | Resend cooldown is active |
| `VALIDATION_FAILED` | Request validation failed |

## Rate Limiting

- **OTP Generation:** 5 per phone per hour
- **OTP Verification:** 3 attempts per OTP
- **Resend Cooldown:** 30 seconds between resends

## Security Features

- **OTP Expiry:** 10 minutes
- **Max Attempts:** 3 per OTP
- **IP Blocking:** Automatic blocking for suspicious activity
- **Phone Blocking:** Temporary blocking for abuse
- **Session Tracking:** All activities are logged

## Example Usage

### Complete OTP Flow

#### Step 1: Get Settings
```bash
curl -X GET https://barimanager.com/api/otp-settings
```

#### Step 2: Send OTP
```bash
curl -X POST https://barimanager.com/api/send-otp \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "01712345678",
    "type": "profile_update"
  }'
```

#### Step 3: Verify OTP
```bash
curl -X POST https://barimanager.com/api/verify-otp \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "01712345678",
    "otp": "123456",
    "type": "profile_update"
  }'
```

#### Step 4: Resend OTP (if needed)
```bash
curl -X POST https://barimanager.com/api/resend-otp \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "01712345678",
    "type": "profile_update"
  }'
```

## Response Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden (IP/Phone blocked) |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Internal Server Error |
| 503 | Service Unavailable |

## Notes

- All timestamps are in UTC format
- OTP codes are 6 digits
- SMS is sent via SMSinBD provider
- All activities are logged for security
- IP and phone blocking is automatic for abuse prevention
