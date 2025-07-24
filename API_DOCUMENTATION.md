# HRMS API Documentation

## Base URL
```
http://103.98.76.11/api
```

**Note:** This URL is configured in `hrms_app/lib/utils/api_config.dart` and can be easily changed for different environments.

## Authentication Endpoints

### 1. Send OTP
**POST** `/send-otp`

Send OTP to phone number for verification.

#### Request Body
```json
{
  "phone": "+1234567890",
  "type": "registration"
}
```

#### Validation Rules
- `phone`: Required, string, max 20 characters
- `type`: Required, must be one of: "registration", "login", "reset"

#### Success Response (200)
```json
{
  "success": true,
  "message": "OTP sent successfully",
  "otp": "123456",
  "expires_in": 10
}
```

#### Error Response (422)
```json
{
  "success": false,
  "message": "Phone number is already registered"
}
```

### 2. Verify OTP
**POST** `/verify-otp`

Verify OTP for phone number.

#### Request Body
```json
{
  "phone": "+1234567890",
  "otp": "123456",
  "type": "registration"
}
```

#### Validation Rules
- `phone`: Required, string, max 20 characters
- `otp`: Required, string, exactly 6 characters
- `type`: Required, must be one of: "registration", "login", "reset"

#### Success Response (200)
```json
{
  "success": true,
  "message": "OTP verified successfully"
}
```

#### Error Response (422)
```json
{
  "success": false,
  "message": "Invalid or expired OTP"
}
```

### 3. Resend OTP
**POST** `/resend-otp`

Resend OTP to phone number (with 1-minute cooldown).

#### Request Body
```json
{
  "phone": "+1234567890",
  "type": "registration"
}
```

#### Success Response (200)
```json
{
  "success": true,
  "message": "OTP resent successfully",
  "otp": "654321",
  "expires_in": 10
}
```

### 4. Get OTP Settings
**GET** `/otp-settings`

Get current OTP system settings.

#### Success Response (200)
```json
{
  "success": true,
  "settings": {
    "is_enabled": true,
    "registration_required": true,
    "login_required": false,
    "reset_required": false,
    "profile_update_required": false,
    "otp_length": 6,
    "otp_expiry_minutes": 10
  }
}
```
  "expires_in": 10
}
```

#### Error Response (429)
```json
{
  "success": false,
  "message": "Please wait 1 minute before requesting another OTP"
}
```

### 4. Owner Registration (Updated)
**POST** `/register-owner`

Register a new property owner with OTP verification.

#### Request Body
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "address": "123 Main Street, City, Country",
  "country": "Bangladesh",
  "password": "password123",
  "password_confirmation": "password123",
  "otp": "123456"
}
```

#### Validation Rules
- `name`: Required, string, max 255 characters
- `email`: Required, valid email, unique in users table, max 255 characters
- `phone`: Required, string, max 20 characters, unique in owners table
- `address`: Required, string, max 500 characters
- `country`: Required, string, max 100 characters
- `password`: Required, string, minimum 6 characters
- `password_confirmation`: Required, must match password
- `otp`: Required, string, exactly 6 characters, must be valid for phone

#### Success Response (201)
```json
{
  "success": true,
  "message": "Owner registered successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  },
  "owner": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "address": "123 Main Street, City, Country",
    "country": "Bangladesh",
    "owner_uid": "OWN-ABC12345",
    "total_properties": 0,
    "total_tenants": 0,
    "user_id": 1,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  },
  "token": "1|abc123def456...",
  "role": "Owner"
}
```

#### Error Response (422 - Validation Error)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."],
    "phone": ["The phone number is already registered."],
    "otp": ["Invalid or expired OTP"]
  }
}
```

### 5. Login
**POST** `/login`

Authenticate a user and get access token.

#### Request Body
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

#### Success Response (200)
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "role": "Owner",
  "token": "1|abc123def456..."
}
```

### 6. Logout
**POST** `/logout`

Logout and invalidate the current token.

**Headers:**
```
Authorization: Bearer {token}
```

#### Success Response (200)
```json
{
  "message": "Logged out successfully."
}
```

## Testing the API

### Using cURL

#### Send OTP
```bash
curl -X POST http://103.98.76.11/api/send-otp \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "phone": "+1234567890",
    "type": "registration"
  }'
```

#### Verify OTP
```bash
curl -X POST http://103.98.76.11/api/verify-otp \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "phone": "+1234567890",
    "otp": "123456",
    "type": "registration"
  }'
```

#### Owner Registration with OTP
```bash
curl -X POST http://103.98.76.11/api/register-owner \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test Owner",
    "email": "test@example.com",
    "phone": "+1234567890",
    "address": "Test Address",
    "country": "Bangladesh",
    "password": "password123",
    "password_confirmation": "password123",
    "otp": "123456"
  }'
```

## Flutter Integration

The Flutter app uses the `AuthService` class to interact with these APIs:

```dart
// Send OTP
final response = await AuthService.sendOtp('+1234567890', 'registration');

// Verify OTP
final response = await AuthService.verifyOtp('+1234567890', '123456', 'registration');

// Register owner with OTP
final response = await AuthService.registerOwner(
  name: 'John Doe',
  email: 'john@example.com',
  phone: '+1234567890',
  address: '123 Main Street',
  country: 'Bangladesh',
  password: 'password123',
  passwordConfirmation: 'password123',
  otp: '123456',
);
```

## Reports Endpoints

### 1. Get Report Types
**GET** `/reports/types`

Get available report types and their descriptions.

**Headers:**
```
Authorization: Bearer {token}
```

#### Success Response (200)
```json
{
  "success": true,
  "report_types": [
    {
      "id": "financial",
      "name": "Financial Report",
      "description": "Revenue, payments, and financial summary",
      "endpoint": "/api/reports/financial",
      "parameters": ["start_date", "end_date", "type"]
    },
    {
      "id": "occupancy",
      "name": "Occupancy Report",
      "description": "Property and unit occupancy status",
      "endpoint": "/api/reports/occupancy",
      "parameters": []
    },
    {
      "id": "tenant",
      "name": "Tenant Report",
      "description": "Tenant information and payment history",
      "endpoint": "/api/reports/tenant",
      "parameters": []
    },
    {
      "id": "transaction",
      "name": "Transaction Report",
      "description": "Detailed transaction ledger",
      "endpoint": "/api/reports/transaction",
      "parameters": ["start_date", "end_date", "type"]
    }
  ]
}
```

### 2. Financial Report
**POST** `/reports/financial`

Generate financial report with revenue and payment data.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

#### Request Body
```json
{
  "start_date": "2024-01-01",
  "end_date": "2024-12-31",
  "type": "all"
}
```

#### Validation Rules
- `start_date`: Required, date format (YYYY-MM-DD)
- `end_date`: Required, date format (YYYY-MM-DD), must be after start_date
- `type`: Optional, must be one of: "rent", "charges", "all"

#### Success Response (200)
```json
{
  "success": true,
  "report": {
    "period": {
      "start_date": "2024-01-01",
      "end_date": "2024-12-31",
      "type": "all"
    },
    "summary": {
      "total_invoiced": 50000,
      "total_paid": 45000,
      "total_unpaid": 5000,
      "total_partial": 0,
      "collection_rate": 90.0
    },
    "monthly_breakdown": {
      "2024-01": {
        "total": 5000,
        "paid": 4500,
        "unpaid": 500,
        "count": 10
      }
    },
    "property_breakdown": {
      "1": {
        "property_name": "Property A",
        "total": 25000,
        "paid": 22500,
        "unpaid": 2500,
        "count": 5
      }
    },
    "generated_at": "2024-01-15 10:30:00"
  }
}
```

### 3. Occupancy Report
**GET** `/reports/occupancy`

Generate occupancy report showing property and unit status.

**Headers:**
```
Authorization: Bearer {token}
```

#### Success Response (200)
```json
{
  "success": true,
  "report": {
    "summary": {
      "total_properties": 5,
      "total_units": 20,
      "total_occupied": 15,
      "total_vacant": 5,
      "overall_occupancy_rate": 75.0
    },
    "properties": [
      {
        "property_id": 1,
        "property_name": "Property A",
        "total_units": 10,
        "occupied_units": 8,
        "vacant_units": 2,
        "occupancy_rate": 80.0,
        "units": [
          {
            "unit_id": 1,
            "unit_name": "Unit 1A",
            "status": "rented",
            "tenant_name": "John Doe",
            "rent_amount": 5000
          }
        ]
      }
    ],
    "generated_at": "2024-01-15 10:30:00"
  }
}
```

### 4. Tenant Report
**GET** `/reports/tenant`

Generate tenant report with payment history and statistics.

**Headers:**
```
Authorization: Bearer {token}
```

#### Success Response (200)
```json
{
  "success": true,
  "report": {
    "summary": {
      "total_tenants": 15,
      "active_tenants": 12,
      "inactive_tenants": 3
    },
    "tenants": [
      {
        "tenant_id": 1,
        "tenant_name": "John Doe",
        "phone": "+1234567890",
        "email": "john@example.com",
        "property_name": "Property A",
        "unit_name": "Unit 1A",
        "rent_amount": 5000,
        "move_in_date": "2024-01-01",
        "status": "active",
        "invoice_stats": {
          "total_invoices": 12,
          "paid_invoices": 10,
          "unpaid_invoices": 2,
          "total_amount": 60000,
          "paid_amount": 50000,
          "outstanding_amount": 10000,
          "payment_rate": 83.33
        }
      }
    ],
    "generated_at": "2024-01-15 10:30:00"
  }
}
```

### 5. Transaction Report
**POST** `/reports/transaction`

Generate detailed transaction ledger report.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

#### Request Body
```json
{
  "start_date": "2024-01-01",
  "end_date": "2024-12-31",
  "type": "all"
}
```

#### Validation Rules
- `start_date`: Required, date format (YYYY-MM-DD)
- `end_date`: Required, date format (YYYY-MM-DD), must be after start_date
- `type`: Optional, must be one of: "rent", "charges", "payment", "all"

#### Success Response (200)
```json
{
  "success": true,
  "report": {
    "period": {
      "start_date": "2024-01-01",
      "end_date": "2024-12-31",
      "type": "all"
    },
    "summary": {
      "total_transactions": 100,
      "total_debit": 50000,
      "total_credit": 45000,
      "net_amount": -5000
    },
    "transactions": [
      {
        "id": 1,
        "date": "2024-01-15",
        "tenant_name": "John Doe",
        "property_name": "Property A",
        "unit_name": "Unit 1A",
        "transaction_type": "rent",
        "description": "Monthly Rent",
        "debit_amount": 5000,
        "credit_amount": 0,
        "balance": 5000,
        "payment_status": "unpaid"
      }
    ],
    "generated_at": "2024-01-15 10:30:00"
  }
}
```

## Notes

- The API uses Laravel Sanctum for authentication
- All responses are in JSON format
- Error messages are user-friendly and localized
- The owner registration creates both a User and Owner record
- The Owner role is automatically assigned to new owners
- Each owner gets a unique `owner_uid` generated automatically
- **Phone numbers must be unique** - no duplicate phone numbers allowed
- **OTP verification is required** for owner registration when OTP system is enabled
- OTP expires after 10 minutes
- OTP can be resent after 1 minute cooldown
- For testing, OTP is returned in the response (remove in production)
- **Reports are owner-specific** - each owner can only see their own data
- **Date ranges** for reports should be reasonable (not too long periods)
- **Report generation** includes real-time data from the database 
