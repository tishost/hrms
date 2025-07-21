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

## Notes

- The API uses Laravel Sanctum for authentication
- All responses are in JSON format
- Error messages are user-friendly and localized
- The owner registration creates both a User and Owner record
- The Owner role is automatically assigned to new owners
- Each owner gets a unique `owner_uid` generated automatically
- **Phone numbers must be unique** - no duplicate phone numbers allowed
- **OTP verification is required** for owner registration
- OTP expires after 10 minutes
- OTP can be resent after 1 minute cooldown
- For testing, OTP is returned in the response (remove in production) 
