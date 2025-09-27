# 📧 Email Template System Documentation

## 🎯 **How Custom Email Templates Work**

### **1. Template Triggering System**
Email templates are automatically triggered by **events** in your application. When specific events occur, the system automatically sends emails using your custom templates.

### **2. Event-Listener Architecture**
```
Event Occurs → Event Listener → Find Template → Send Email
```

## 🚀 **How to Use Custom Templates**

### **Step 1: Create a Template**
1. Go to **Admin Panel → Templates → Email Templates**
2. Click **"Create New Template"**
3. Choose **"HTML"** or **"Plain Text"**
4. Design your email template
5. Use **template variables** like `{{user_name}}`, `{{company_name}}`

### **Step 2: Define Template Variables**
In your email template, use these variables:
```html
<h1>Welcome {{user_name}}!</h1>
<p>Your account was created on {{created_at}}</p>
<p>Company: {{company_name}}</p>
<p>Amount: ${{amount}}</p>
```

### **Step 3: Trigger the Template**
In your code, dispatch an event:
```php
// When user registers
Event::dispatch(new UserRegistered($user));

// When payment is completed
Event::dispatch(new PaymentCompleted($user, $payment));
```

## 📋 **Available Events & Templates**

### **1. User Registration**
- **Event**: `UserRegistered`
- **Template Name**: `user_registration`
- **Variables**: `user_name`, `user_email`, `created_at`, `company_name`

### **2. Payment Confirmation**
- **Event**: `PaymentCompleted`
- **Template Name**: `payment_confirmation`
- **Variables**: `user_name`, `amount`, `transaction_id`, `payment_date`, `company_name`

### **3. Invoice Generation**
- **Event**: `InvoiceGenerated`
- **Template Name**: `invoice_generated`
- **Variables**: `user_name`, `invoice_number`, `amount`, `due_date`, `company_name`

### **4. System Notifications**
- **Event**: `SystemNotification`
- **Template Name**: `system_notification`
- **Variables**: `user_name`, `notification_title`, `notification_message`, `company_name`

### **5. Tenant Invitations**
- **Event**: `TenantInvitation`
- **Template Name**: `tenant_invitation`
- **Variables**: `tenant_name`, `invitation_link`, `company_name`

### **6. Owner Notifications**
- **Event**: `OwnerNotification`
- **Template Name**: `owner_notification`
- **Variables**: `owner_name`, `notification_type`, `message`, `company_name`

## 🛠️ **How to Add New Events**

### **Step 1: Create Event Class**
```php
// app/Events/NewEvent.php
class NewEvent
{
    public $user;
    public $data;

    public function __construct(User $user, $data)
    {
        $this->user = $user;
        $this->data = $data;
    }
}
```

### **Step 2: Add Event Listener**
```php
// In EmailTemplateListener.php
public function handleNewEvent($event)
{
    $this->sendTemplateEmail('new_template', $event->user, [
        'user_name' => $event->user->name,
        'custom_data' => $event->data,
        'company_name' => config('app.name'),
    ]);
}
```

### **Step 3: Register Event**
```php
// In EventServiceProvider.php
protected $listen = [
    \App\Events\NewEvent::class => [
        \App\Listeners\EmailTemplateListener::class . '@handleNewEvent',
    ],
];
```

### **Step 4: Create Template**
1. Go to **Admin Panel → Templates → Email Templates**
2. Create template with name: `new_template`
3. Use variables: `{{user_name}}`, `{{custom_data}}`, `{{company_name}}`

### **Step 5: Trigger Event**
```php
// In your controller or service
Event::dispatch(new NewEvent($user, $data));
```

## 🎨 **Template Design Guidelines**

### **HTML Templates**
- Use **responsive design** (mobile-friendly)
- Include **company branding**
- Use **professional colors** and fonts
- Add **call-to-action buttons**
- Include **social media links**

### **Template Variables**
- Always use `{{variable_name}}` format
- Use descriptive variable names
- Include common variables: `{{company_name}}`, `{{user_name}}`

### **Content Structure**
```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{email_subject}}</title>
</head>
<body>
    <div class="email-container">
        <header>
            <h1>{{company_name}}</h1>
        </header>
        
        <main>
            <h2>{{email_title}}</h2>
            <p>{{email_content}}</p>
        </main>
        
        <footer>
            <p>© {{company_name}} - All rights reserved</p>
        </footer>
    </div>
</body>
</html>
```

## 🔧 **Technical Implementation**

### **Template Storage**
- Templates are stored in `email_templates` table
- HTML content is stored in `content` field
- Template variables are replaced at runtime

### **Email Sending**
- Uses Laravel's Mail system
- Templates are processed through `TemplateEmail` Mailable
- Variables are replaced using `str_replace()`

### **Error Handling**
- Failed emails are logged
- Missing templates are logged as warnings
- Invalid email addresses are handled gracefully

## 📊 **Monitoring & Logs**

### **Email Logs**
Check `storage/logs/laravel.log` for:
- Successful email sends
- Failed email attempts
- Missing template warnings
- Invalid email addresses

### **Template Status**
- **Active**: Template will be used
- **Inactive**: Template will be ignored
- **Draft**: Template is being edited

## 🚨 **Common Issues & Solutions**

### **Template Not Sending**
1. Check if template is **active**
2. Verify template **name** matches event
3. Check **user email** exists
4. Review **error logs**

### **Variables Not Replacing**
1. Use correct variable format: `{{variable_name}}`
2. Ensure variable is passed in event
3. Check variable name spelling

### **HTML Not Rendering**
1. Use `isHtml()` method in template
2. Ensure HTML is valid
3. Check email client compatibility

## 🎯 **Best Practices**

1. **Test Templates**: Always test templates before going live
2. **Use Variables**: Make templates dynamic with variables
3. **Mobile Responsive**: Ensure templates work on mobile
4. **Professional Design**: Use consistent branding
5. **Error Handling**: Handle missing templates gracefully
6. **Logging**: Monitor email sending success/failure
7. **Performance**: Use queue for high-volume emails

## 🔄 **Workflow Example**

1. **User registers** → `UserRegistered` event fired
2. **Event listener** catches event
3. **Template lookup** finds `user_registration` template
4. **Variables replaced** with user data
5. **Email sent** to user's email address
6. **Success logged** in system logs

This system makes it easy to manage all your email communications from one place while keeping them professional and consistent!
