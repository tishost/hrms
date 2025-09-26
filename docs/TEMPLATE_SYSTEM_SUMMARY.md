# 🎯 **Custom Email Template System - Complete Guide**

## 📋 **What We've Built**

### **1. Template Management System**
- ✅ **Admin Panel** for creating/editing templates
- ✅ **HTML Editor** with rich text formatting
- ✅ **Code View** for raw HTML editing
- ✅ **Template Variables** system
- ✅ **Professional Design** matching your landing page

### **2. Event-Driven Email System**
- ✅ **Event Listeners** for automatic email sending
- ✅ **Template Variables** replacement
- ✅ **Error Handling** and logging
- ✅ **Multiple Event Types** support

## 🚀 **How It Works**

### **Step 1: Create Template**
```
Admin Panel → Templates → Email Templates → Create New
```

### **Step 2: Design Template**
```html
<h1>Welcome {{user_name}}!</h1>
<p>Your account was created on {{created_at}}</p>
<p>Company: {{company_name}}</p>
```

### **Step 3: Trigger Event**
```php
// In your code
Event::dispatch(new UserRegistered($user));
```

### **Step 4: Email Sent Automatically**
- Event listener catches the event
- Finds the template
- Replaces variables
- Sends email

## 📧 **Available Events & Templates**

| Event | Template Name | Variables | When to Use |
|-------|---------------|-----------|-------------|
| `UserRegistered` | `user_registration` | `user_name`, `user_email`, `created_at`, `company_name` | New user signs up |
| `PaymentCompleted` | `payment_confirmation` | `user_name`, `amount`, `transaction_id`, `payment_date`, `company_name` | Payment successful |
| `InvoiceGenerated` | `invoice_generated` | `user_name`, `invoice_number`, `amount`, `due_date`, `company_name` | Invoice created |
| `SystemNotification` | `system_notification` | `user_name`, `notification_title`, `notification_message`, `company_name` | System alerts |
| `TenantInvitation` | `tenant_invitation` | `tenant_name`, `invitation_link`, `company_name` | Invite tenants |
| `OwnerNotification` | `owner_notification` | `owner_name`, `notification_type`, `message`, `company_name` | Owner alerts |

## 🛠️ **How to Add New Events**

### **1. Create Event Class**
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

### **2. Add Event Listener**
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

### **3. Register Event**
```php
// In EventServiceProvider.php
protected $listen = [
    \App\Events\NewEvent::class => [
        \App\Listeners\EmailTemplateListener::class . '@handleNewEvent',
    ],
];
```

### **4. Create Template**
- Go to Admin Panel → Templates → Email Templates
- Create template with name: `new_template`
- Use variables: `{{user_name}}`, `{{custom_data}}`, `{{company_name}}`

### **5. Trigger Event**
```php
Event::dispatch(new NewEvent($user, $data));
```

## 🎨 **Template Design Features**

### **HTML Editor**
- ✅ **Rich Text Formatting** (bold, italic, underline)
- ✅ **Color Picker** for text and background
- ✅ **Alignment** (left, center, right, justify)
- ✅ **Lists** (ordered, unordered)
- ✅ **Links** and **Images**
- ✅ **Tables** with custom rows/columns
- ✅ **Template Variables** button
- ✅ **Code View** toggle
- ✅ **Live Preview**

### **Professional Styling**
- ✅ **Gradient Backgrounds** matching landing page
- ✅ **Responsive Design** (mobile-friendly)
- ✅ **Company Branding** integration
- ✅ **Professional Colors** and typography
- ✅ **Call-to-Action Buttons**
- ✅ **Social Media Links**

## 🔧 **Technical Implementation**

### **Files Created/Modified**
1. **`app/Listeners/EmailTemplateListener.php`** - Handles all email events
2. **`app/Mail/TemplateEmail.php`** - Processes and sends template emails
3. **`app/Events/UserRegistered.php`** - User registration event
4. **`app/Events/PaymentCompleted.php`** - Payment completion event
5. **`app/Providers/EventServiceProvider.php`** - Event registration
6. **`app/Http/Controllers/ExampleTemplateUsage.php`** - Usage examples
7. **`app/Console/Commands/TestEmailTemplate.php`** - Testing command

### **Database Structure**
- **`email_templates`** table stores all templates
- **`content`** field stores HTML/plain text
- **`is_active`** field controls template usage
- **`tags`** field for categorization

### **Email Processing**
1. **Event Fired** → Event listener catches it
2. **Template Lookup** → Find template by name
3. **Variable Replacement** → Replace `{{variables}}` with actual data
4. **Email Sending** → Use Laravel Mail system
5. **Logging** → Log success/failure

## 🧪 **Testing the System**

### **Test Command**
```bash
# Test user registration email
php artisan test:email-template user_registration test@example.com

# Test payment confirmation email
php artisan test:email-template payment_confirmation test@example.com
```

### **Manual Testing**
```php
// In your controller
Event::dispatch(new UserRegistered($user));
```

## 📊 **Monitoring & Logs**

### **Check Logs**
```bash
tail -f storage/logs/laravel.log
```

### **Log Messages**
- ✅ **Success**: "Email template 'template_name' sent to user@email.com"
- ⚠️ **Warning**: "Email template 'template_name' not found or inactive"
- ❌ **Error**: "Failed to send email template 'template_name': error_message"

## 🚨 **Common Issues & Solutions**

### **Template Not Sending**
1. ✅ Check if template is **active**
2. ✅ Verify template **name** matches event
3. ✅ Check **user email** exists
4. ✅ Review **error logs**

### **Variables Not Replacing**
1. ✅ Use correct format: `{{variable_name}}`
2. ✅ Ensure variable is passed in event
3. ✅ Check variable name spelling

### **HTML Not Rendering**
1. ✅ Use `isHtml()` method in template
2. ✅ Ensure HTML is valid
3. ✅ Check email client compatibility

## 🎯 **Best Practices**

1. **Test Templates**: Always test before going live
2. **Use Variables**: Make templates dynamic
3. **Mobile Responsive**: Ensure mobile compatibility
4. **Professional Design**: Use consistent branding
5. **Error Handling**: Handle missing templates gracefully
6. **Logging**: Monitor email sending success/failure
7. **Performance**: Use queue for high-volume emails

## 🔄 **Complete Workflow Example**

### **User Registration Flow**
1. **User fills registration form**
2. **User model created** in database
3. **`UserRegistered` event fired**
4. **Event listener catches event**
5. **Template lookup** finds `user_registration` template
6. **Variables replaced** with user data
7. **Email sent** to user's email address
8. **Success logged** in system logs

### **Payment Confirmation Flow**
1. **Payment processed** successfully
2. **`PaymentCompleted` event fired**
3. **Event listener catches event**
4. **Template lookup** finds `payment_confirmation` template
5. **Variables replaced** with payment data
6. **Email sent** to user's email address
7. **Success logged** in system logs

## 🎉 **What You Can Do Now**

1. **Create Templates**: Design beautiful email templates
2. **Trigger Events**: Automatically send emails on events
3. **Use Variables**: Make templates dynamic
4. **Monitor Logs**: Track email sending success/failure
5. **Test System**: Use test commands to verify functionality
6. **Add New Events**: Extend system with custom events
7. **Professional Emails**: Send branded, professional emails

## 🚀 **Next Steps**

1. **Create your first template** in the admin panel
2. **Test the system** using the test command
3. **Add custom events** for your specific needs
4. **Monitor logs** to ensure everything works
5. **Design professional templates** matching your brand

**Your email template system is now fully functional and ready to use!** 🎯
