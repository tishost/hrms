# 🔄 **Email Template System Flow Diagram**

## **Complete System Flow**

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   User Action   │    │   Event Fired    │    │ Event Listener  │
│                 │    │                  │    │                 │
│ • Register      │───▶│ UserRegistered   │───▶│ EmailTemplate   │
│ • Payment       │    │ PaymentCompleted │    │ Listener        │
│ • Notification  │    │ SystemNotification│    │                 │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                                                         │
                                                         ▼
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Email Sent    │    │ Template Process │    │ Template Lookup │
│                 │    │                  │    │                 │
│ • HTML/Text     │◀───│ Variable Replace │◀───│ Find Template   │
│ • User Email    │    │ Content Process  │    │ Check Active    │
│ • Success Log   │    │ Format Content   │    │ Get Variables   │
└─────────────────┘    └──────────────────┘    └─────────────────┘
```

## **Template Creation Flow**

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Admin Panel   │    │   HTML Editor    │    │ Template Save   │
│                 │    │                  │    │                 │
│ • Create New    │───▶│ • Rich Text      │───▶│ • Database      │
│ • Choose Type   │    │ • Code View      │    │ • Active Status │
│ • Design        │    │ • Variables      │    │ • Validation    │
└─────────────────┘    └──────────────────┘    └─────────────────┘
```

## **Event Processing Flow**

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Application   │    │   Event System   │    │   Email System  │
│                 │    │                  │    │                 │
│ • User Action   │───▶│ • Event Dispatch │───▶│ • Template Find │
│ • Business Logic│    │ • Listener Catch │    │ • Variable Replace│
│ • Data Update   │    │ • Handler Execute│    │ • Email Send    │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                                                         │
                                                         ▼
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   User Receives │    │   Logging        │    │   Error Handling│
│                 │    │                  │    │                 │
│ • HTML Email    │◀───│ • Success Log    │◀───│ • Missing Template│
│ • Formatted     │    │ • Error Log      │    │ • Invalid Email │
│ • Professional  │    │ • Debug Info     │    │ • Retry Logic   │
└─────────────────┘    └──────────────────┘    └─────────────────┘
```

## **Template Variable System**

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Template      │    │   Event Data     │    │   Final Email   │
│                 │    │                  │    │                 │
│ {{user_name}}   │───▶│ user.name        │───▶│ John Doe        │
│ {{amount}}      │    │ payment.amount   │    │ $100.00         │
│ {{company_name}}│    │ config('app.name')│    │ Your Company    │
└─────────────────┘    └──────────────────┘    └─────────────────┘
```

## **File Structure**

```
app/
├── Events/
│   ├── UserRegistered.php
│   ├── PaymentCompleted.php
│   └── SystemNotification.php
├── Listeners/
│   └── EmailTemplateListener.php
├── Mail/
│   └── TemplateEmail.php
├── Models/
│   └── EmailTemplate.php
└── Providers/
    └── EventServiceProvider.php

resources/views/admin/templates/
├── email/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
└── sms/
    ├── index.blade.php
    ├── create.blade.php
    ├── edit.blade.php
    └── show.blade.php
```

## **Database Schema**

```
email_templates
├── id (Primary Key)
├── name (Template Name)
├── subject (Email Subject)
├── content (HTML/Text Content)
├── category (system/owner/tenant)
├── priority (1-10)
├── description (Template Description)
├── tags (JSON Array)
├── is_active (Boolean)
├── created_at (Timestamp)
└── updated_at (Timestamp)
```

## **Key Components**

### **1. Event System**
- **Events**: UserRegistered, PaymentCompleted, etc.
- **Listeners**: EmailTemplateListener
- **Provider**: EventServiceProvider

### **2. Template System**
- **Model**: EmailTemplate
- **Mail**: TemplateEmail
- **Views**: Admin panel templates

### **3. Editor System**
- **HTML Editor**: Rich text editing
- **Code View**: Raw HTML editing
- **Variables**: Template variable insertion
- **Preview**: Live preview functionality

### **4. Processing System**
- **Variable Replacement**: {{variable}} → actual value
- **Content Processing**: HTML/Text formatting
- **Email Sending**: Laravel Mail system
- **Error Handling**: Logging and retry logic

## **Usage Examples**

### **1. User Registration**
```php
// User registers
$user = User::create([...]);

// Event fired automatically
Event::dispatch(new UserRegistered($user));

// Email sent automatically using 'user_registration' template
```

### **2. Payment Confirmation**
```php
// Payment processed
$payment = Payment::create([...]);

// Event fired automatically
Event::dispatch(new PaymentCompleted($user, $payment));

// Email sent automatically using 'payment_confirmation' template
```

### **3. Custom Event**
```php
// Custom event
Event::dispatch(new CustomEvent($user, $data));

// Email sent automatically using 'custom_template' template
```

This system provides a complete, automated email template solution that's easy to use and maintain! 🚀
